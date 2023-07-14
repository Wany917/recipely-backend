<?php

namespace Recipely\Controllers\workshop;

use Recipely\Lib\Response;
use Recipely\Lib\Header;
use Recipely\Config\ConnectDb;
use Recipely\Auth\Authenticator;
USE Recipely\Controllers\workshop\Workshop;
use Recipely\Models\workshop\WorkshopModel;
use Recipely\Utils\WorkshopExceptions;

class WorkshopControllers{

    private Response $response;
    private Header $headers;
    private WorkshopExceptions $exception;
    private Authenticator $authenticator;
    
    public function __construct(){
        $this->headers = new Header();
        $this->response = Response::create();
        $this->exception = new WorkshopExceptions;    
        $this->authenticator = new Authenticator();
    }

    private String $id;
    private Int $id_room;
    private String $id_provider;
    private String $name_workshop;
    private String $desc;

    private $errors = [];

    public function createWorkshop($id_room,$id_provider,$workshop_data){
        if($this->checkId($id_room, 'id', 'rooms') && $this->checkId($id_provider, 'id', 'providers')){
            
           $this->checkData($workshop_data);
           $this->id = $this->generateId();
           $this->id_room = $id_room;
           $this->id_provider = $id_provider;
           
   
           if(sizeof($this->errors) == 0){
               $workshop = new Workshop(
                    $this->id,
                    $this->id_room,
                    $this->id_provider,
                    $this->name_workshop,
                    $this->desc
               );

               $workshopModel = new WorkshopModel();
               $workshopResult = $workshopModel->insertWorkshop($workshop);
   
               echo $this->response->sendResponse(201, true, 'Workshop created succesfully', $workshopResult);
               return true;
           } else {
                echo $this->response->sendResponse(404, false, 'Bad params', $this->errors);
                return false;
           }
        }else {
            $this->errors[] = "id_room or id_provider doesn't exist";
            echo $this->response->sendResponse(400, false, 'Bad params', $this->errors);
            return false;
        }   
    }

    private function checkData($workshop_data){
        if(!is_array($workshop_data)){
            $this->errors[] = "workshop_data must be an array !";
        }elseif(sizeof($workshop_data) != 2){
            $this->errors[] = "workshop_data must have at least 2 keys !";
        }
        foreach($workshop_data as $key => $value){
            if($value == null){
                $this->errors[] = "$key is required !";
            }
            $value = trim($value);
            $value = htmlspecialchars($value);
            switch($key){
                case 'name_workshop':
                    if(strlen($value) < 3 || strlen($value) > 50){
                        $this->errors[] = "$key must be between 3 and 50 characters !";
                    } elseif(empty($value)){
                        $this->errors[] = "$key is required !";
                    } else {
                        $this->name_workshop = $value;
                    }
                    break;
                case 'desc':
                    if(strlen($value) < 3 || strlen($value) > 155){
                        $this->errors[] = "$key must be between 3 and 155 characters !";
                    } elseif(empty($value)){
                        $this->errors[] = "$key is required !";
                    } else {
                        $this->desc = $value;
                    }
                    break;
                default:
                    $this->errors[] = "$key is not a valid key !";
            }
        }
    }

    private function generateId(){
        $id = substr(uniqid("ws-"), 0, 15);
        return $id;
    }

    private function checkId($value, $column, $table): bool {
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    
        $fetch = $stmt->fetch();
        ConnectDb::closeConnexion();
    
        // si c'est vide je renvoie true sinon false
        if ($fetch) {
            return true;
        }
        return false;
    }




    // ----------------------------------------------------------------------------------------------------------------

    // ----------------------------------------------------------------------------------------------------------------

    public function getAllWorkshops(){
        try{
            $model = new WorkShopModel();
            $workshop = $model->getAllWorkshops();

            if(empty($workshop)){
                echo $this->response->sendResponse(404, false, $this->exception->notFound(), null);                
            }else{
                echo $this->response->sendResponse(200, true, 'Workshop found', $workshop);
            }
            return true;
        }catch (WorkshopExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function getWorkshop($id){
        try{
            $model = new WorkShopModel();
            $workshop = $model->getWorkshop($id);

            if(empty($workshop)){
                echo $this->response->sendResponse(404, false, $this->exception->notFound(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Workshop found', $workshop);
            }
            return true;
        }catch (WorkshopExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function deleteWorkshop($id){
        try{
            $model = new WorkShopModel();
            $workshop = $model->deleteWorkshop($id);

            if($workshop != true){
                echo $this->response->sendResponse(404, false, $this->exception->notDeleted(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Workshop deleted', null);
            }
            return true;
        }catch (WorkshopExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function updateWorkshop($id, $workshop_data){
        try{
            $model = new WorkShopModel();
            $workshop = $model->updateWorkshop($id, $workshop_data);

            if(empty($workshop)){
                echo $this->response->sendResponse(404, false, $this->exception->notUpdated(), null);
            }else{
                echo $this->response->sendResponse(200, true, "Workshop updated", $workshop);
            }
            return true;
        }catch(WorkshopExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }
}