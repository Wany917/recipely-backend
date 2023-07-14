<?php
namespace Recipely\Utils;

use Exception;

Class ServiceExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notCreated(){
        return "Service not created";
    }

    public function notUpdated(){
        return "Service not updated";
    }

    public function notDeleted(){
        return "Service not deleted";
    }

    public function notFound(){
        return "Service(s) not found";
    }
}