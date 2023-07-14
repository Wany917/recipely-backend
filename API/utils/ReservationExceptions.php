<?php
namespace Recipely\Utils;

use Exception;

Class ReservationExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notCreated(){
        return "Reservation not created";
    }

    public function notUpdated(){
        return "Reservation not updated";
    }

    public function notDeleted(){
        return "Reservation not deleted";
    }

    public function notFound(){
        return "Reservation(s) not found";
    }
}