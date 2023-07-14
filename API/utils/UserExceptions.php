<?php 
namespace Recipely\Utils;

use Exception;
class UserExceptions extends \Exception{
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous); 
    }


    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function notFound(){
        return "User(s) not found";
    }

    public function notCreated(){
        return "User not created";
    }

    public function notUpdated(){
        return "User not updated";
    }

    public function notDeleted(){
        return "User not deleted";
    }

    public function notLogged(){
        return "User not logged";
    }

    public function notAuthorized(){
        return "User not authorized";
    }

    public function notValid(){
        return "User not valid";
    }

    public function notFoundEmail(){
        return "Invalid parameters";
    }
    public function notFoundPassword(){
        return "Invalid parameters";
    }

    public function unAuthorized(){
        return "User unauthorized";
    }
}