<?php
namespace Recipely\Utils;

use Exception;

Class OrderExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notCreated(){
        return "Order not created";
    }

    public function notUpdated(){
        return "Order not updated";
    }

    public function notDeleted(){
        return "Order not deleted";
    }

    public function notFound(){
        return "Order(s) not found";
    }
}