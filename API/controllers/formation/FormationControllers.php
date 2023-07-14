<?php

namespace Recipely\Controllers\formation;

use PDO,Exception;
use Recipely\Lib\Response;
use Recipely\Config\ConnectDb;
use Recipely\Auth\Authenticator;
use Recipely\Utils\FormationExceptions;
use Recipely\Controllers\formation\Formation;
use Recipely\Models\formation\FormationModel;

class FormationControllers {

    private Response $response;
    private FormationExceptions $exception;
    private Authenticator $authenticator;

    private Int $id;
    private String $name;
    private String $description;
    private String $id_provider;
    private String $img;

    private Array $errors = [];
    private Array $recipes = [];

    public function __construct()
    {   
        $this->response = Response::create();
        $this->authenticator = new Authenticator();
        $this->exception = new FormationExceptions();
    }

    public function createFormation($id_provider,$formation_data){
        $payload = $this->authenticator->authenticateUser();
        $isAdmin = $this->authenticator->checkUserValues($payload,'account_type', 2,3);
        $isProvider = $this->authenticator->checkUserValues($payload,'is_provider', 1);
        
        if(!$isAdmin || !$isProvider){
            echo $this->response
            ->sendResponse(403, false,$this->exception->unAuthorized(), null);
            return false;
        }

        $this->checkData($formation_data);
        $this->id = $this->generateId();
        $this->recipes = $formation_data['recipes'];

        if(!$this->checkValue($id_provider, 'id', 'providers')){
            $this->errors[] = "Provider not found";
        }else {
            $this->id_provider = $id_provider;
            if(sizeof($this->errors) > 0){
               echo $this->response
               ->sendResponse(400, "Error while creating formation :(", $this->errors);
               return false;
            } else {
                $formation = new Formation(
                    $this->id, 
                    $this->name,
                    $this->description,
                    $this->id_provider,
                    $this->img
                );
                $formation_model = new FormationModel();
                try{
                    $insert = $formation_model->insertFormation($formation);
                    $formation_model->addRecipesFormations($this->id, $this->recipes);
                    echo $this->response
                    ->sendResponse(201,true,"Formation created successfully :)", 
                    [
                        "formation" => $insert
                    ]);
                    return true;
                } catch(Exception $e){
                   echo $this->response
                    ->sendResponse(400,false,$e->getMessage(), null);
                }
            }
        }
        
    }

    private function checkData($data){
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->checkName($value);
                    break;
                case 'description':
                    $this->checkDesc($value);
                    break;
                case 'img':
                    is_string($value) ? $this->img = $value : $this->errors[] = "Incorrect $key:$value fomat";
            }
        }
    }

    private function checkName($name){
        if(!preg_match('/^[a-zA-Z ]+$/',$name)){
            $this->errors[] = "Incorrect $name format";
            return false;
        }
        $this->name = $name;
        return true;
    }

    private function checkDesc($desc){
        // fait moi une regex pour la description qui accepte tout les string et space
        if(!preg_match('/^[a-zA-Z ]+$/',$desc)){
            $this->errors[] = "Incorrect $desc format";
            return false;
        }
        $this->description = $desc;
        return true;
    }

    private function generateId(){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT id FROM formations ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();
        if($fetch){
            return $fetch['id'] + 1;
        } else {
            return 1;
        }
    }


    private function checkValue($value, $column, $table){
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


    // ---------------------------------------------------------------------------------------------------------------------------------------------------------------------
    public function getAllFormations(){
       try{
            $formation_model = new FormationModel();
            $formations = $formation_model->getAllFormations();
            if(is_array($formations)){
                echo $this->response
                ->sendResponse(200,true,"Formations found :)",$formations);
            } else {
                echo $this->response
                ->sendResponse(400,true,"Error while getting formations :(",null);
            }
       } catch (Exception $e){
            echo $this->response
            ->sendResponse(400,true,"Error while getting formations :(",["error" => $e->getMessage()]);
       }
    }

    public function getFormationWithRecipes($id){
        try{
            $formation_model = new FormationModel();
            $formation = $formation_model->getFormationWithRecipes($id);
            if(is_array($formation)){
                echo $this->response
                ->sendResponse(200,true,"Formation found :)",$formation);
            } else {
                echo $this->response
                ->sendResponse(400,true,"Error while getting formation :(",null);
            }
        }catch (Exception $e){
            echo $this->response
            ->sendResponse(500,false,"Error while getting formation :(",["error" => $e->getMessage()]);
        }
    }

    public function getFormation($id){
        try{
            $formation_model = new FormationModel();
            $formation = $formation_model->getFormation($id);
            if(is_array($formation)){
                echo $this->response
                ->sendResponse(200,true,"Formation found :)",$formation);
            } else {
                echo $this->response
                ->sendResponse(400,true,"Error while getting formation :(",null);
            }
        } catch (Exception $e){
            echo $this->response
            ->sendResponse(500,false,"Error while getting formation :(",["error" => $e->getMessage()]);
        }
    }

    public function updateFormation($id,$data_formation){
        $payload = $this->authenticator->authenticateUser();
        $isAdmin = $this->authenticator->checkUserValues($payload,'account_type', 2,3);
        $isProvider = $this->authenticator->checkUserValues($payload,'is_provider', 1);

        if(!$isAdmin || !$isProvider){
            echo $this->response
            ->sendResponse(403, false,$this->exception->unAuthorized(), null);
            return false;
        }else{
            try{
                $formation_model = new FormationModel();
                $formation = $formation_model->updateFormation($id,$data_formation);
                if(is_array($formation)){
                    echo $this->response
                    ->sendResponse(200,true,"Formation updated :)",$formation);
                } else {
                    echo $this->response
                    ->sendResponse(400,true,"Error while updating formation :(",null);
                }
            } catch (Exception $e){
                echo $this->response
                ->sendResponse(500,false,"Error while getting formation :(",["error" => $e->getMessage()]);
            }
        }
    }

    public function deleteFormation($id){
        $payload = $this->authenticator->authenticateUser();
        $isAdmin = $this->authenticator->checkUserValues($payload,'account_type', 2,3);
        $isProvider = $this->authenticator->checkUserValues($payload,'is_provider', 1);

        if(!$isAdmin || !$isProvider){
            echo $this->response
            ->sendResponse(403, false,$this->exception->unAuthorized(), null);
            return false;
        }else{
            try{
                $formation_model = new FormationModel();
                $formation = $formation_model->deleteFormation($id);
                if($formation){
                    echo $this->response
                    ->sendResponse(200,true,"Formation deleted :)",null);
                } else {
                    echo $this->response
                    ->sendResponse(400,true,"Error while deleting formation :(",null);
                }
            } catch (Exception $e){
                echo $this->response
                ->sendResponse(500,false,"Error while deleting formation :(",["error" => $e->getMessage()]);
            }
        }
    }
}