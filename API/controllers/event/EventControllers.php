<?php 

namespace Recipely\Controllers\event;

use PDO,Exception,DateTime;
use Recipely\Lib\Response;
use Recipely\Lib\Header;
use Recipely\Auth\Authenticator;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\event\Event;
use Recipely\Models\event\EventModel;
use Recipely\Utils\EventExceptions;


class EventControllers {

    private Response $response;
    private Header $headers;
    private EventExceptions $exception;
    private Authenticator $authenticator;
    
    public function __construct(){
        $this->headers = new Header();
        $this->response = Response::create();
        $this->exception = new EventExceptions;    
        $this->authenticator = new Authenticator();
    }
    // LE NOM
    private String $type_event;
    private DateTime $date_event;
    private DateTime $time;
    private String $location;
    private String $id_client;

    private Array $errors = [];

    public function createEvent($id_client,$id_location, $event_data){
        $this->checkData($event_data);
        $this->id_client = $id_client;
       
        if (isset($id_location) || !empty($id_location)) {
            if ($this->checkValue($id_location, 'id', 'rooms')){
                $this->errors[] = "This location doesn't exist";
            } else {
                $pdo = ConnectDb::getInstance();
                $stmt = $pdo->prepare(
                    "SELECT name_room FROM rooms WHERE id = :id"
                );
                $stmt->execute([':id' => $id_location]);
                $this->location = $stmt->fetch(PDO::FETCH_ASSOC)['name_room'];
                ConnectDb::closeConnexion();
            }
        } else {
            $this->location = 'house';
        }

        if(sizeof($this->errors) == 0){
            $event = new Event(
                $this->type_event,
                $this->date_event,
                $this->time,
                $this->location,
                $this->id_client
            );

            $model = new EventModel();
            $model = $model->insertEvent($event);
            echo $this->response->sendResponse(201, true, 'Event created',$model);
            return true;
        } else {
            echo $this->response->sendResponse(400, false,$this->errors, null);
            return false;
        }

    }

    public function checkData($data){
        foreach($data as $key => $value){
           switch($key){
                case 'type_event':
                   $this->checkTypeEvent($value);
                    break;
                case 'date_event':
                     $this->checkDate($value);
                    break;
                case 'time':
                    $this->checkTime($value);
                    break;
           }
        }
    }

    public function checkTypeEvent($type_event){
        if(empty($type_event)){
            $this->errors['type_event'] = "Event type cannot be empty";
        } else if(strlen($type_event) > 50){
            $this->errors['type_event'] = "Event type cannot exceed 50 characters";
        }
        $this->type_event = $type_event;
    }

    public function checkDate($date){
        $date = explode('-', $date);
        if(count($date) != 3){
            $this->errors['date_event'] = "Date format is not valid";
        } else if($date[0] < 2023 || $date[0] > 2030 || $date[1] < 1 || $date[1] > 12 || $date[2] < 1 || $date[2] > 31){
            $this->errors['date_event'] = "Date format is not valid";
        }
        $this->date_event = new DateTime($date[0].'-'.$date[1].'-'.$date[2]);
    }
    public function checkTime($time){
        $time = explode(':', $time);
        if(count($time) != 3){
            $this->errors['time'] = "Time format is not valid";
        } else if($time[0] > 23 || $time[1] > 59 || $time[2] > 59){
            $this->errors['time'] = "Time format is not valid";
        }
        $this->time = new DateTime($time[0].':'.$time[1].':'.$time[2]);
    }

    public function getLocationName($id){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT name_room FROM rooms WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();
        return $fetch['name_room'];  
    }

    public function checkValue($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if(empty($fetch)){
            return true;
        } else{
            return false;
        }
    }



    // --------------------------------------------------------------------------------------------------------------------------------

    public function getAllEvents(){
        try{
            $model = new EventModel();
            $events = $model->getAllEvents();

            if(empty($events)){
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Recipely event', $events);
            }
            return true;
        }catch(EventExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function getEvent($id){
        try{
            $model = new EventModel();
            $event = $model->getEvent($id);
            if($event == false){
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return false;
            }else{
                echo $this->response->sendResponse(200, true, 'Recipely event', $event);
            }
            return true;
        }catch (EventExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function updateEvent($id,$event_data){
        try{
            $this->checkData($event_data);
            if(sizeof($this->errors) == 0){
                $model = new EventModel();
                $event = $model->updateEvent($id,$event_data);
                echo $this->response->sendResponse(200, true, 'Event updated', $event);
                return true;
            } else {
                echo $this->response->sendResponse(400, false, $this->errors, null);
                return false;
            }
        } catch (EventExceptions $e){
            echo $this->response->sendResponse(500, false, 'Internal server error', null);
            return false;
        }
    }

    public function deleteEvent($id){
        $this->checkValue($id, 'id', 'events') ? $this->errors['id'] = "Event not found" : null;
        try{
            if(sizeof($this->errors) == 0){
                $model = new EventModel();
                $event = $model->deleteEvent($id);
                echo $this->response->sendResponse(200, true, 'Event deleted', null);
                return true;
            } else {
                echo $this->response->sendResponse(400, false, 'Invalid data', null);
                return false;
            }
        } catch (EventExceptions $e){
            echo $this->response->sendResponse(500, false, 'Internal server error', null);
            return false;
        }
    }
}