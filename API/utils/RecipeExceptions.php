<?php 
namespace Recipely\Utils;

use Exception;
class RecipeExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notFound(){
        return "Recipe not found";
    }

    public function notCreated(){
        return "Recipe not created";
    }

    public function notUpdated(){
        return "Recipe not updated";
    }

    public function notDeleted(){
        return "Recipe not deleted";
    }

    public function notLogged(){
        return "Recipe not logged";
    }

    public function notAuthorized(){
        return "not authorized";
    }

    public function notValid(){
        return "Recipe not valid";
    }


    public function notValidBody(){
        return "Invalid parameters";
    }

    public function unAuthorized(){
        return "unauthorized";
    }   
}