<?php
namespace Recipely\Utils;

use Exception;

Class WorkshopExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notCreated(){
        return "Workshop not created";
    }

    public function notUpdated(){
        return "Workshop not updated";
    }

    public function notDeleted(){
        return "Workshop not deleted";
    }

    public function notFound(){
        return "Workshop(s) not found";
    }
}