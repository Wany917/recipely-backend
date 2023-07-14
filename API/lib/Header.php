<?php

namespace Recipely\Lib;

use Recipely\Models\user\UserModel;
class Header
{
    private array $headers;

    public function __construct()
    {
        $this->headers = getallheaders();
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        foreach ($this->headers as $headerName => $headerValue) {
            if (strcasecmp($headerName, $name) === 0) {
                [$headerName, $token] = explode(' ', $headerValue);
                return $token;
            }
        }
        return null;
    }
    

    // Récupère le token d'authentification
    public function getAuthHeader(): ?string
    {
        return $this->getHeader('Authorization');
    }

    public function getAuthHeaders(): ?array
    {
        $authorization = $this->getHeader('Authorization');
        if ($authorization !== null) {
            if (strpos($authorization, ' ') !== false) {
                [$headername, $token] = explode(' ', $authorization);
            } else {
                $headername = 'Token'; // or 'token' or whatever you prefer
                $token = $authorization;
            }
            return [$headername, $token];
        }
        echo ("Authorization header not found."); // Debug info
        return null;
    }
    
    // public function checkToken($token){
    //     $userModel = new UserModel();
    //     $user = $userModel->getToken($token);
    //     if($user == $token){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // }

}
