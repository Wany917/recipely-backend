<?php 
namespace Recipely\Controllers\recipe;

use Exception, PDO;
use Recipely\Lib\Response;
use Recipely\Config\ConnectDb;
use Recipely\Auth\Authenticator;
use Recipely\Utils\RecipeExceptions;
use Recipely\Controllers\recipe\Recipe;
use Recipely\Models\recipe\RecipeModel;
class RecipeControllers{
    private Response $response;
    private Authenticator $authenticator;
    private RecipeExceptions $exception;

    private String $id;
    private String $name;
    private String $description;
    private Int $serves;
    private Int $prep_time;
    private String $creator;
    private String $img_md;
    private String $img_sm;
    private String $video;


    private Array $requiredStepFields =['step_number', 'instruction'];
    private Array $requiredIngredientsFields = ['name', 'quantity', 'unit']; 

    private Array $ingredients = [];
    private Array $steps = [];


    private Array $errors = [];


    public function __construct()
    {  
        $this->response = Response::create();
        $this->authenticator = new Authenticator();
        $this->exception = new RecipeExceptions();
    }

    public function createRecipe($id_provider,$recipe_data){
        $payload = $this->authenticator->authenticateUser();
        if($payload){
            $isAdmin = $this->authenticator->checkUserValues($payload, 'account_type', 2, 3);
            $isProvider = $this->authenticator->checkUserValues($payload, 'is_provider', 1);            
        }else{
            echo $this->response
            ->sendResponse(403, $this->exception->unAuthorized(),null);
            return false;
        }
        if($this->checkValue($id_provider, 'id', 'providers') && 
            $this->checkValue($recipe_data['name'], 'name', 'recipes') == false)
            {
                if($isAdmin || $isProvider){
                    $this->id = $this->generateId();
                    $this->creator = $id_provider;
                    $this->checkData($recipe_data);
                    if(sizeof($this->errors) == 0){

                    $recipe = new Recipe(
                        $this->id,
                        $this->name,
                        $this->description,
                        $this->serves,
                        $this->prep_time,
                        $this->creator,
                        $this->img_md,
                        $this->img_sm,
                        $this->video
                    );
    
                    $recipe_model = new RecipeModel();
                    $insert = $recipe_model->insertRecipe($recipe);
                    if(is_array($insert)){
                        $recipe_model->addRecipeIngredients($this->id, $this->ingredients);
                        $recipe_model->addRecipeStep($this->id, $this->steps);
                    }else{
                        echo $this->response
                        ->sendResponse(400, false,"Error while creating Recipe :(",null);
                    }

                    echo $this->response
                    ->sendResponse(200, true, "Recipe created successfully !", [$insert]);    
                }else{
                    echo $this->response
                    ->sendResponse(400, false,$this->exception->unAuthorized(), null);
                    return false;
                }

            } else{
                echo $this->response
                ->sendResponse(400, false,"Error while creating Recipe :(", $this->errors);
                return false;
            }
        
        } else{
            $this->errors[] = "Recipe already exist.";
            echo $this->response
            ->sendResponse(400, false,"Error while creating Recipe :(",
            [$this->checkValue($id_provider, 'id', 'providers') ? $this->errors : "Provider doesn't exist"]
            );
            return false;
        }
    }

    private function checkData($recipe_data){
        // check if recipe_data is an array after that check if all the keys are present
        if(!is_array($recipe_data)){
            $this->errors[] = "Fields isn't correct !";
        }elseif(sizeof($recipe_data) != 9){
            $this->errors[] = "recipe data must contain 9 keys !";
        }
    
        foreach($recipe_data as $key => $value){
            if($value == null){
                $this->errors[] = "$key is null !";
            }else{            
                switch ($key){
                    case 'name':
                        if(strlen($value) < 3 || strlen($value) > 50){
                            $this->errors[] = "Name must be between 3 and 50 characters !";
                        }else{
                            $this->name = $value;
                        }
                        break;
                    case 'description':
                        if(strlen($value) < 3 || strlen($value) > 300){
                            $this->errors[] = "Description must be between 3 and 500 characters !";
                        }else{
                            $this->description = $value;
                        }
                        break;
                    case 'serves':
                        if(!is_numeric($value)){
                            $this->errors[] = "Serves must be a number !";
                        }else{
                            $this->serves = $value;
                        }
                        break;
                    case 'prep_time':
                        if(!is_numeric($value)){
                            $this->errors[] = "Prep time must be a number !";
                        }else{
                            $this->prep_time = $value;
                        }
                        break;
                    case 'img_md':
                        $this->img_md = $value;
                        break;
                    case 'img_sm':
                        $this->img_sm = $value;
                        break;
                    case 'video':
                        $this->video = $value;
                        break;
                    case 'steps':
                        if(!is_array($value) || empty($value)){
                            $this->errors[] = "Steps must be an array and cannot be empty !";
                        }else{
                            foreach ($value as $step) {
                                $this->steps[] = $step;
                            }
                            if($this->checkFields($this->steps, $this->requiredStepFields) == false){
                                $this->errors[] = "Steps must contain step number and instruction !";
                            }
                        }
                        break;
                    case 'ingredients':
                        if(!is_array($value) || empty($value)){
                            $this->errors[] = "Ingredients must be an array and cannot be empty !";
                        }else{
                            foreach ($value as $ingredient) {
                                $this->ingredients[] = $ingredient;
                            }
                            if($this->checkFields($this->ingredients, $this->requiredIngredientsFields) == false){
                                $this->errors[] = "Ingredients must contain name, quantity and unit !";
                            }
                        }
                        break;
                    default:
                        $this->errors[] = "Unknown key $key !";
                }
            }
        }
    }
    

    public function getErrors(){
        return $this->errors;
    }

    public function generateId(){
        $id = substr(uniqid("rc-"), 0, 15);
        return $id;
    }

    function checkFields($array, $requiredFields) {
        foreach ($array as $item) {
            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $item)) {
                    return false;
                }
            }
        }
        return true;
    }

    // -------------------------------------------------------------------------------------------------------------------------------\\

    public function getAllRecipes(){
      try{
        $recipe_model = new RecipeModel();
        $recipes = $recipe_model->getAllRecipes();
        if($recipes){
            echo $this->response->sendAuthResponse(200, true,"Recipes found !",
            ['recipe' => $recipes]);
        }else{
            echo $this->response->sendResponse(404, false,$this->exception->notFound(), null);
        }
      } catch (Exception $e){
        echo $this->response->sendResponse(500, false,"Error while getting recipes !", [$e->getMessage()]);
      }
    }

    public function getRecipe($id){
        try{
            $recipe_model = new RecipeModel();
            $recipe = $recipe_model->getRecipe($id);
            if($recipe){
                echo $this->response->sendResponse(200, true,"Recipe found !", $recipe);
            }else{
                echo $this->response->sendResponse(404, $this->exception->notFound(), null);
            }
        } catch (Exception $e){
            echo $this->response->sendResponse(500, false,"Error while getting recipe !", [$e->getMessage()]);
        }
    }

    public function getRecipesByCreator($creator){
        try{
            $recipe_model = new RecipeModel();
            $recipe = $recipe_model->getRecipesByCreator($creator);
            if($recipe){
                echo $this->response->sendResponse(200, true,"Recipes found !", $recipe);
            }else{
                echo $this->response->sendResponse(404, false,$this->exception->notFound(), null);
            }
        } catch (Exception $e){
            echo $this->response->sendResponse(500,false,"Error while getting recipe !", [$e->getMessage()]);
        }
    }

    public function updateRecipe($id, $recipe_data){
        $payload = $this->authenticator->authenticateUser();
        $isAdmin = $this->authenticator->checkUser($payload,$payload['account_type']);
        $isProvider = $this->authenticator->checkUser($payload,$payload['is_provider']);

        try{
            if(!$this->checkValue($id, 'id', 'recipes') && ( $isAdmin == 2 || $isAdmin == 3) || $isProvider == 1){
                $recipe_model = new RecipeModel();
                $recipe = $recipe_model->updateRecipe($id, $recipe_data);
                if($recipe){
                    echo $this->response->sendResponse(200, "Recipe updated !", $recipe);
                }else{
                    echo $this->response->sendResponse(400, "Error while updating recipe !", null);
                    return false;
                }
            }else{
                echo $this->response->sendResponse(404, $this->exception->notFound(), null);
                return false;
            }
            return true;
        } catch (Exception $e){
            echo $this->response->sendResponse(500, "Error while updating recipe !",[ $e->getMessage()]);
            return false;
        }
    }

    public function checkValue($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if(!empty($fetch)){
            return true;
        } else{
            return false;
        }
    }

    public function deleteRecipe($id){
        try{
            if($this->checkValue($id, 'id', 'recipes')){
                $recipe_model = new RecipeModel();
                $recipe = $recipe_model->deleteRecipe($id);
                if($recipe){
                    echo $this->response->sendResponse(200, true,"Recipe deleted !",null);
                }else{
                    echo $this->response->sendResponse(400, false,"Error while deleting recipe !", null);
                    return false;
                }
            }else{
                echo $this->response->sendResponse(404, false,$this->exception->notFound(), null);
                return false;
            }
            return true;
        } catch (Exception $e){
            echo $this->response->sendResponse(500, false,"Error while deleting recipe !",[ $e->getMessage()]);
            return false;
        }
    }

}