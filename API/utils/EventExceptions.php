<?php
namespace Recipely\Utils;

use Exception;

Class EventExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notCreated(){
        return "Event not created";
    }

    public function notUpdated(){
        return "Event not updated";
    }

    public function notDeleted(){
        return "Event not deleted";
    }

    public function notFound(){
        return "Event(s) not found";
    }
}