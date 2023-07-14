<?php 
namespace Recipely\Models\recipe;

use Exception;
use PDO,PDOException;
use Recipely\Config\ConnectDb;
use Recipely\Utils\RecipeExceptions;
use Recipely\Controllers\recipe\Recipe;


class RecipeModel{
    public function insertRecipe(Recipe $recipe){
        try{

            $pdo = ConnectDb::getInstance();
            $sql_insert = "INSERT INTO recipes(id,name,description,serves,prep_time,creator,img_md,img_sm,video) 
            VALUES(:id,:name,:description,:serves,:prep_time,:creator,:img_md,:img_sm,:video)";

            $stmt = $pdo->prepare($sql_insert);

            $params = [
                ':id' => $recipe->toArray()['id'],
                ':name' => $recipe->toArray()['name'],
                ':description' => $recipe->toArray()['description'],
                ':serves' => $recipe->toArray()['serves'],
                ':prep_time' => $recipe->toArray()['prep_time'],
                ':creator' => $recipe->toArray()['creator'],
                ':img_md' => $recipe->toArray()['img_md'],
                ':img_sm' => $recipe->toArray()['img_sm'],
                ':video' => $recipe->toArray()['video']
            ];
            $stmt->execute($params);
            ConnectDb::closeConnexion();
            return $recipe->toArray();

        } catch (PDOException $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function addRecipeStep(String $recipe_id, Array $steps){
        try{
            $pdo = ConnectDb::getInstance();
            $sql_insert = "INSERT INTO recipe_steps(step_id, recipe_id,step_number,instruction) 
            VALUES (:step_id, :recipe_id, :step_number, :instruction)";
            $stmt = $pdo->prepare($sql_insert);
    
            foreach ($steps as $step) {
                $step_id = $this->generateStepId(); // Generate a unique step id for each step
                $params = [
                    ':step_id' => $step_id,
                    ':recipe_id' => $recipe_id,
                    ':step_number' => $step['step_number'],
                    ':instruction' => $step['instruction']
                ];
                $stmt->execute($params);
            }
            ConnectDb::closeConnexion();
            return true;

        }catch(PDOException $e){

            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function addRecipeIngredients($recipeId, $ingredients) {
        try {
            $pdo = ConnectDb::getInstance();

            // Récupération des IDs des ingrédients et des nouveaux ingrédients insérés
            $result = $this->checkAndInsertIngredients($ingredients);
            if (is_null($result)) {
                throw new RecipeExceptions('Erreur inattendue');
            }

            list($ingredientIds, $insertedIngredients) = $result;

            $stmt = $pdo->prepare("INSERT INTO recipes_ingredients(recipe_id, ingredient_id, quantity, unit) VALUES (:recipe_id, :ingredient_id, :quantity, :unit)");

            foreach($ingredients as $ingredient){
                $ingredientId = $ingredientIds[$ingredient['name']];
                $params = [
                    ':recipe_id' => $recipeId,
                    ':ingredient_id' => $ingredientId,
                    ':quantity' => $ingredient['quantity'],
                    ':unit' => $ingredient['unit']
                ];
                $stmt->execute($params);
            }

            ConnectDb::closeConnexion();
            return true;

        } catch (PDOException $e) {
            ConnectDb::closeConnexion();
            throw new RecipeExceptions($e->getMessage());
        }
    }
    

    public function checkAndInsertIngredients(Array $ingredients) {
        try {
            $pdo = ConnectDb::getInstance();

            $ingredientsId = [];
            $insertedIngredients = [];
            
            foreach ($ingredients as $ingredient) {
                $name = $ingredient['name'];
                $stmt = $pdo->prepare("SELECT id FROM ingredients WHERE name = :name");
                $stmt->execute(['name' => $name]);
                $result = $stmt->fetch();

                if (!$result) {
                    $idIngredient = $this->generateIngredientId();
                    $stmt = $pdo->prepare("INSERT INTO ingredients (id, name) VALUES (:id, :name)");
                    $stmt->execute(['id' => $idIngredient, 'name' => $name]);
                    $ingredientsId[$name] = $idIngredient;
                    $insertedIngredients[] = $ingredient;
                } else {
                    $ingredientsId[$name] = $result['id'];
                }
            }

            ConnectDb::closeConnexion();
            
            return [$ingredientsId, $insertedIngredients];

        } catch (PDOException $e) {
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }
    

    
    public function getStepCount(String $recipe_id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recipe_steps WHERE recipe_id = :recipe_id");
            $stmt->execute([':recipe_id' => $recipe_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            ConnectDb::closeConnexion();
            return $result['count'];
        }catch(PDOException $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function getAllRecipes(){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
            "SELECT
            recipes.id,
            recipes.name,
            recipes.description,
            recipes.serves,
            recipes.prep_time,
            recipes.creator,
            recipes.img_md,
            recipes.img_sm,
            recipes.video,
            recipes.status,
            GROUP_CONCAT(recipe_steps.step_number) AS step_numbers,
            GROUP_CONCAT(recipe_steps.instruction) AS instructions,
            GROUP_CONCAT(recipes_ingredients.ingredient_id) AS ingredient_ids,
            GROUP_CONCAT(recipes_ingredients.quantity) AS quantities
            FROM
                recipes
            LEFT JOIN
                recipe_steps ON recipes.id = recipe_steps.recipe_id
            LEFT JOIN
                recipes_ingredients ON recipes.id = recipes_ingredients.recipe_id
            GROUP BY
                recipes.id      
            "
            );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ConnectDb::closeConnexion();
            return $result;
        }catch(PDOException $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function getRecipe($id) {
        try {
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
               "SELECT
               recipes.id,
               recipes.name,
               recipes.description,
               recipes.serves,
               recipes.prep_time,
               recipes.creator,
               recipes.img_md,
               recipes.img_sm,
               recipes.video,
               recipes.status,
               GROUP_CONCAT(recipe_steps.step_number ORDER BY recipe_steps.step_number ASC) AS step_numbers,
               GROUP_CONCAT(recipe_steps.instruction ORDER BY recipe_steps.step_number ASC) AS instructions,
               GROUP_CONCAT(recipes_ingredients.ingredient_id) AS ingredient_ids,
               GROUP_CONCAT(recipes_ingredients.quantity) AS quantities,
               GROUP_CONCAT(recipes_ingredients.unit) AS units
                FROM
                    recipes
                LEFT JOIN
                    recipe_steps ON recipes.id = recipe_steps.recipe_id
                LEFT JOIN
                    recipes_ingredients ON recipes.id = recipes_ingredients.recipe_id
                WHERE
                    recipes.id = :id
                GROUP BY
                    recipes.id;
            ");

            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            ConnectDb::closeConnexion();
            return $result;
        } catch(PDOException $e) {
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }
    
    

    public function getRecipesByCreator($id_creator){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
            "SELECT
            recipes.id,
            recipes.name,
            recipes.description,
            recipes.serves,
            recipes.prep_time,
            recipes.creator,
            recipes.img_md,
            recipes.img_sm,
            recipes.video,
            recipes.status,
            GROUP_CONCAT(recipe_steps.step_number) AS step_numbers,
            GROUP_CONCAT(recipe_steps.instruction) AS instructions,
            GROUP_CONCAT(recipes_ingredients.ingredient_id) AS ingredient_ids,
            GROUP_CONCAT(recipes_ingredients.quantity) AS quantities
            FROM
                recipes
            LEFT JOIN
                recipe_steps ON recipes.id = recipe_steps.recipe_id
            LEFT JOIN
                recipes_ingredients ON recipes.id = recipes_ingredients.recipe_id
            WHERE
                recipes.creator = :creator
            GROUP BY
                recipes.id                
            "
            );
            $stmt->execute([':creator' => $id_creator]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ConnectDb::closeConnexion();
            return $result;
        }catch(PDOException $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function updateRecipe($recipe_id, $recipe_data) {
        $recipe_data = json_decode($recipe_data, true);
        try {
            $pdo = ConnectDb::getInstance();
            $checkValue = $this->checkValue($recipe_id, 'id', 'recipes');
            
            if ($checkValue) {
                $pdo->beginTransaction();
                $paramsRecipe = [':id' => $recipe_id,];
    
                $updateRecipe = [];
                foreach ($recipe_data as $key => $value) {
                    if ($key != 'id') {
                        $updateRecipe[] = $key . ' = :' . $key;
                        $paramsRecipe[':' . $key] = $value;
                    }
                }
                
                // Mettre à jour les informations de la recette générale
                $updateRecipeQuery = "UPDATE recipes SET " .implode(', ', $updateRecipe) . " WHERE id = :id";
                $stmt = $pdo->prepare($updateRecipeQuery);
                $stmt->execute($paramsRecipe);
                
                // Mettre à jour les étapes de la recette
                if (isset($recipe_data['steps'])) {
                    $this->updateRecipeSteps($recipe_id, $recipe_data['steps']);
                }
                
                // Mettre à jour les ingrédients de la recette
                if (isset($recipe_data['ingredients'])) {
                    $this->updateRecipeIngredients($recipe_id, $recipe_data['ingredients']);
                }
                
                $pdo->commit();
                ConnectDb::closeConnexion();
                return $recipe_data;
            } else {
                ConnectDb::closeConnexion();
                return false;
            }
        } catch (PDOException $e) {
            ConnectDb::closeConnexion();
           return $e->getMessage();
        }
    }
    
    
    private function updateRecipeSteps($recipe_id, $steps) {
        try {
            $pdo = ConnectDb::getInstance();
            $deleteQuery = 'DELETE FROM steps WHERE recipe_id = :recipe_id';
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->execute([':recipe_id' => $recipe_id]);
            
            $insertQuery = 'INSERT INTO steps (recipe_id, step_number, instruction) VALUES (:recipe_id, :step_number, :instruction)';
            $stmt = $pdo->prepare($insertQuery);
            
            foreach ($steps as $step) {
                $stmt->execute([
                    ':recipe_id' => $recipe_id,
                    ':step_number' => $step['step_number'],
                    ':instruction' => $step['instruction']
                ]);
            }       
            ConnectDb::closeConnexion();
            return true;
        } catch (PDOException $e) {
            ConnectDb::closeConnexion();
            echo $e->getMessage();
        }
    }
    
    private function updateRecipeIngredients($recipe_id, $ingredients) {
        try {
            $pdo = ConnectDb::getInstance();
            $deleteQuery = 'DELETE FROM ingredients WHERE recipe_id = :recipe_id';
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->execute([':recipe_id' => $recipe_id]);
            
            $insertQuery = 'INSERT INTO ingredients (recipe_id, ingredient_id, quantity, unit) VALUES (:recipe_id, :ingredient_id, :quantity, :unit)';
            $stmt = $pdo->prepare($insertQuery);
            
            foreach ($ingredients as $ingredient) {
                $stmt->execute([
                    ':recipe_id' => $recipe_id,
                    ':ingredient_id' => $ingredient['ingredient_id'],
                    ':quantity' => $ingredient['quantity'],
                    ':unit' => $ingredient['unit']
                ]);
            }
            
            ConnectDb::closeConnexion();
        } catch (PDOException $e) {
            ConnectDb::closeConnexion();
            echo $e->getMessage();
        }
    }
    
    
    public function deleteRecipe($recipe_id){
        try{
            $pdo = ConnectDb::getInstance();
            $checkValue = $this->checkValue($recipe_id, 'id', 'recipes');
            if($checkValue){
                // suppression des steps lié à l'id de la recette
                $stmt = $pdo->prepare("DELETE FROM recipe_steps WHERE recipe_id = :recipe_id");
                $stmt->execute([':recipe_id' => $recipe_id]);
                // suppression des ingredients lié à l'id de la recette
                $stmt = $pdo->prepare("DELETE FROM recipes_ingredients WHERE recipe_id = :recipe_id");
                $stmt->execute([':recipe_id' => $recipe_id]);
                // suppression de la recette
                $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = :id");
                $stmt->execute([':id' => $recipe_id]);
                ConnectDb::closeConnexion();
                return true;
            }else{
                ConnectDb::closeConnexion();
                return false;
            }
        }catch(PDOException $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }
    
    public function checkValue($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if($fetch){
            return true;
        } else{
            return false;
        }
    }

    public function generateStepId(){
        $id = substr(uniqid("stp-"), 0, 15);
        return $id;
    }

    public function generateIngredientId(){
        $id = substr(uniqid("ing-"), 0, 15);
        return $id;
    }

}