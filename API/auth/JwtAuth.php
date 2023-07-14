<?php

namespace Recipely\Auth;
require_once __DIR__ .'/../vendor/firebase/php-jwt/src/JWT.php';
require_once __DIR__ .'/../vendor/firebase/php-jwt/src/Key.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;
class JwtAuth {
    
    private $secretKey;
    private $algorithm;


    public function __construct() {
        $this->secretKey = $_ENV['JWT_SECRET'];
        $this->algorithm = $_ENV['JWT_ALGORITHM'];
    }

    public function generateToken($payload) {
        return JWT::encode($payload, $this->secretKey,$this->algorithm);
    }

    public function decodeToken($token) {
        $header = new stdClass();
        $payload =  JWT::decode($token, new Key($this->secretKey,$this->algorithm), $header);

        if(isset($header->exp)) {
            return false;
        } else {
            $payload = json_decode(json_encode($payload), true);
            return $payload;
        }
    }
}