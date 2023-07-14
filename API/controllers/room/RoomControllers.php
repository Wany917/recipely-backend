<?php 

namespace Recipely\Controllers\room;

use PDO;
use Recipely\Lib\Response;
use Recipely\Lib\Header;
use Recipely\Auth\Authenticator;
use Recipely\Config\ConnectDb;
use Recipely\Models\room\RoomModel;
use Recipely\Controllers\room\Room;
use Recipely\Utils\RoomExceptions;


// Les rooms que va crée l'admin/superAdmin en fonctions des locaux y compris les existants
class RoomControllers {

    private Response $response;
    private Header $headers;
    private RoomExceptions $exception;
    private Authenticator $authenticator;
    
    public function __construct(){
        $this->headers = new Header();
        $this->response = Response::create();
        $this->exception = new RoomExceptions;    
        $this->authenticator = new Authenticator();
    }

    private Int $id;
    private Int $capacity;
    private Int $district;
    private String $address;
    private String $name_room;

    private $errors = [];

    public function createRoom($room_data){
        // recupere les champs saisi par l'user pour le traitement
        $this->capacity = $room_data['capacity'];
        $this->district = $room_data['district'];
        $this->address = $room_data['address'];
        $this->name_room = $room_data['name_room'];

        $this->checkData($room_data);

        $this->id = $this->generateId();

        if(empty($this->errors)){
            $room = new Room(
                $this->id,
                $this->capacity,
                $this->district,
                $this->address,
                $this->name_room
            );
           
            $roomModel = new RoomModel();
            $roomResult = $roomModel->insertRoom($room);

            echo $this->response->sendResponse(201, true, 'Room created succesfully', $roomResult);
            return true;
        } else {
            echo $this->response->sendResponse(400, false, 'Bad params', $this->errors);
            return false;
        }

    }


    private function checkData($room_data){
        foreach($room_data as $key => $value){
           switch($key){
               case 'name_room':
                   $this->checkNameRoom($value);
                   break;
               case 'address':
                   $this->checkAddress($value);
                   break;
               case 'district':
                   $this->checkDistrict($value);
                   break;
               case 'capacity':
                   $this->checkCapacity($value);
                   break;
           }
        }
    }
    // generate renvoie un id de type Int
    private function generateId(){
        $id = substr(uniqid(), -5);
        while(!$this->checkId($id)){
            $id = substr(uniqid(), -5);
        }
        return intval($id);
    }

    private function checkId($id): bool{
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT id from rooms WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $fetch = $stmt->fetch();
        ConnectDb::closeConnexion();
        
        if(!$fetch) {
            return true;
        }
        return false;
    }

    private function checkNameRoom($name){
        if(strlen($name) > 50){
            $this->errors[] = "Votre titre doit contenir 50 caractère au maximum";
            return false; 
        }
        $this->name_room = htmlspecialchars($name);
        return true;
    }

    private function checkAddress($address){}
    private function checkDistrict($district){}
    private function checkCapacity(){}

    // ----------------------------------------------------------------------------------------------------------------

    // ----------------------------------------------------------------------------------------------------------------

    public function deleteRoom($id){
        try{
            $model = new RoomModel();
            $room = $model->deleteRoom($id);

            if($room != true){
                echo $this->response->sendResponse(404, false, $this->exception->notDeleted(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Room deleted', null);
            }
            return true;
        }catch (RoomExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function updateRoom($id, $room){
        try{
            $model = new RoomModel();
            $room = $model->updateRoom($id, $room);

            if(empty($room)){
                echo $this->response->sendResponse(404, false, $this->exception->notUpdated(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Room updated succesfully', $room);
            }
            return true;
        }catch (RoomExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function getRoom($id){
        try{
            $model = new RoomModel();
            $room = $model->getRoom($id);

            if(empty($room)){
                echo $this->response->sendResponse(404, false, $this->exception->notFound(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Room found', $room);
            }
            return true;
        }catch (RoomExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }

    }

    public function getAllRooms(){
        try{
            $model = new RoomModel();
            $room = $model->getAllRooms();
            
            if(empty($room)){
                echo $this->response->sendResponse(404, false, $this->exception->notFound(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Room found', $room);
            }
            return true;
        }catch (RoomExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }
}