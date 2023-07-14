<?php 
namespace Recipely\Utils;

use Exception;
class FormationExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notFound(){
        return "Formation not found";
    }

    public function notCreated(){
        return "Formation not created";
    }

    public function notUpdated(){
        return "Formation not updated";
    }

    public function notDeleted(){
        return "Formation not deleted";
    }

    public function notLogged(){
        return "Formation not logged";
    }

    public function notAuthorized(){
        return "Formation not authorized";
    }

    public function notValid(){
        return "Formation not valid";
    }


    public function notValidBody(){
        return "Invalid parameters";
    }

    public function unAuthorized(){
        return "unauthorized";
    }   
}