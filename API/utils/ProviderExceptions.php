<?php 
namespace Recipely\Utils;

use Exception;
class ProviderExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notFound(){
        return "Provider not found";
    }

    public function notCreated(){
        return "Provider not created";
    }

    public function notUpdated(){
        return "Provider not updated";
    }

    public function notDeleted(){
        return "Provider not deleted";
    }

    public function notLogged(){
        return "Provider not logged";
    }

    public function notAuthorized(){
        return "Provider not authorized";
    }

    public function notValid(){
        return "Provider not valid";
    }


    public function notValidBody(){
        return "Invalid parameters";
    }

    public function unAuthorized(){
        return "Provider unauthorized";
    }   
}