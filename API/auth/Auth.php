<?php 

namespace Recipely\Auth;

use Recipely\Auth\JwtAuth;
use Recipely\Lib\Response;
use Recipely\Auth\Authenticator;
use Recipely\Models\user\UserModel;
use Recipely\Controllers\user\UserControllers;


class Auth{
    private JwtAuth $jwt;
    private Response $response;
    private Authenticator $authenticator;

    public function __construct(){
        $this->jwt = new JwtAuth();
        $this->response = Response::create();
        $this->authenticator = new Authenticator();
    }

    public function login($data) {
        $currentEmail = $data['email'];
        $currentPwd = $data['password'];
        $userController = new UserControllers();
        $user = $userController->getUserByEmail($currentEmail);
    
        if($user && is_array($user)){
            $password = $user['password'];
            if(password_verify($currentPwd, $password)){
                $jwt = $this->jwt;
                $payload = [
                    'id' => $user['id'],
                    'verified' => $user['verified'],
                    'is_provider' => $user['is_provider'],
                    'account_type' => $user['account_type'],
                    'exp' => time() + (60 * 60 * 12)
                ];
                $token = $jwt->generateToken($payload);
                echo $this->response->sendAuthResponse(200,true,'Founded',[
                    "accessToken" => $token,

                    "data" => [
                        'id' => $user['id'], 
                        'email' => $user['email'],
                        'username' => $user['username'],
                        'firstname' => $user['firstname'],
                        'lastname' => $user['lastname'],
                        'address' => $user['address'],
                        'password' => $user['password'],             
                        'img_profile' => $user['img_profile'],
                        'phone_number' => $user['phone_number'],
                        'account_type' => $user['account_type'], 
                        'user_key' => $user['user_key'],
                        'subscription' => $user['subscription'],
                        'verified' => $user['verified'],
                        'specialty' => $user['specialty'],
                        'experience' => $user['experience'],
                        'is_provider' => $user['is_provider'],
                        'interest' => $user['interest'],
                    ],

                    "user" => [
                        'id' => $user['id'],
                        'interest' => $user['interest'],                        
                        "displayName" => $user['firstname'].' '.$user['lastname'],
                        'email' => $user['email'],
                        'password' => $user['password'],
                        "photoURL" => "https://api-dev-minimal-v5.vercel.app/assets/images/avatar/avatar_25.jpg",
                        'phoneNumber' => $user['phone_number'],
                        'country' => 'France',
                        'address' => 'Faubourg st Antoine',
                        'state' => 'Ile de France',
                        'city' => 'Paris',
                        'zipCode' => '75012',
                        'about' => 'success',
                        'isPublic' => true,
                        'role' => $user['account_type'] == 2 || $user['account_type'] == 3 ? 'admin' : 'user'
                    ]
                
                ]);
            } else {
                echo $this->response->sendResponse(401, false, 'Invalid credentials', null);
            }
            return true;
        } else {
            echo $this->response->sendResponse(404, false, 'Create your account ?', null);
        }
        return false;
    }
    

    public function register($data_user){
        $model = new UserControllers();
        $createUser = $model->createUser($data_user);
        if($createUser == true){
            return true;
        } else{
            return false;
        }
    }

    public function me(){
        $payload = $this->authenticator->authenticateUser();
        if($payload){
            $user = new UserModel();
            $user = $user->getUser($payload['id']);
            if(is_array($user)){
                
                echo $this->response->
                sendAuthResponse(200, true, 'User found', [
                    "data" => [
                        'id' => $user['id'], 
                        'email' => $user['email'],
                        'username' => $user['username'],
                        'firstname' => $user['firstname'],
                        'lastname' => $user['lastname'],
                        'address' => $user['address'],
                        'password' => $user['password'],             
                        'photoURL' => $user['img_profile'],
                        'phone_number' => $user['phone_number'],
                        'account_type' => $user['account_type'], 
                        'user_key' => $user['user_key'],
                        'subscription' => $user['subscription'],
                        'verified' => $user['verified'],
                        'specialty' => $user['specialty'],
                        'experience' => $user['experience'],
                        'is_provider' => $user['is_provider'],
                        'interest' => $user['interest'],
                    ],

                    "user" => [
                        'username' => $user['username'],
                        'firstname' => $user['firstname'],
                        'lastname' => $user['lastname'],
                        'interest' => $user['interest'],
                        
                        'photoURL' => $user['img_profile'],
                        'account_type' => $user['account_type'], 
                        'user_key' => $user['user_key'],
                        'subscription' => $user['subscription'],
                        'verified' => $user['verified'],
                        'specialty' => $user['specialty'],
                        'experience' => $user['experience'],
                        'is_provider' => $user['is_provider'],
                        
                        'id' => $user['id'],
                        "displayName" => $user['firstname'].' '.$user['lastname'],
                        'email' => $user['email'],
                        'password' => $user['password'],
                        'photoURL' => $user['img_profile'],
                        'phoneNumber' => $user['phone_number'],
                        'country' => 'France',
                        'address' => 'Faubourg st Antoine',
                        'state' => 'Ile de France',
                        'city' => 'Paris',
                        'zipCode' => '75012',
                        'about' => 'success',
                        'isPublic' => true,
                        'role' => $user['account_type'] == 2 || $user['account_type'] == 3 ? 'admin' : 'user'
                    ]
                ]);
            }
        } else {
            echo $this
            ->response
            ->sendResponse(401, false, 'Invalid token', null);
        }
       
    }    
}