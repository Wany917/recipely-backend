<?php
namespace Recipely\Utils;

use Exception;

Class RoomExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notCreated(){
        return "Room not created";
    }

    public function notUpdated(){
        return "Room not updated";
    }

    public function notDeleted(){
        return "Room not deleted";
    }

    public function notFound(){
        return "Room(s) not found";
    }
}