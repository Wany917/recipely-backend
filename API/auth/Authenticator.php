<?php 
namespace Recipely\Auth;

use Recipely\Lib\Header;
use Recipely\Lib\Response;
use Recipely\Auth\JwtAuth;

class Authenticator{
    private JwtAuth $jwt;
    private Header $headers;
    private Response $response;


    public function __construct(){
        $this->jwt = new JwtAuth();
        $this->headers = new Header();
        $this->response = Response::create();
    }

    public function authenticate($token){
        try {
            $payload = $this->jwt->decodeToken($token);
            if(!$payload) {
            echo $this->response
                ->sendResponse(401, false, 'Invalid token', null);
            return false;
            } else {
            return $payload;
            }
        }catch (\Firebase\JWT\ExpiredException $exp){
            echo $this->response
                ->sendResponse(401, false, 'Token expired', null);
            return false;
        }catch (\Firebase\JWT\BeforeValidException $exp){
            echo $this->response
                ->sendResponse(401, false, 'Token not valid yet', null);
            return false;
        }catch (\Firebase\JWT\SignatureInvalidException $e){
            echo $this->response
                ->sendResponse(401, false, 'Invalid token signature', null);
            return false;
        } catch (\Exception $e) {
            echo $this->response
                ->sendResponse(500, false, 'Internal error', null);
            return false;
        }
    }

    public function authenticateUser(){
        if($this->headers->getAuthHeader()){
            $token = $this->headers->getAuthHeader();
            $payload = $this->authenticate($token);
            if($payload){
                return $payload;
            }
        } else {
            return false;
        }
    }

    public function checkUser($payload, $field){
        if(isset($payload[$field]) && $payload[$field]){
            return true;
        } else {
            return false;
        }
    }   

    public function checkUserValues($payload, $field, ...$values){
        if(isset($payload[$field]) && in_array($payload[$field], $values)){
            return true;
        } else {
            return false;
        }
    }        
    
}