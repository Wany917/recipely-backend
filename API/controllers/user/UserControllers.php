<?php
namespace Recipely\Controllers\user;

use Recipely\Lib\Header;
use Recipely\Lib\Response;
use stdClass,PDO,Exception;
use Recipely\Config\ConnectDb;
use Recipely\Auth\Authenticator;
use Recipely\Traits\user\Getters;
use Recipely\Traits\user\Setters;
use Recipely\Utils\UserExceptions;
use Recipely\Models\user\UserModel;
use Recipely\Controllers\user\User;

class UserControllers extends stdClass {
    use Getters, Setters;

    private Response $response;
    private Header $headers;
    private UserExceptions $exception;
    private Authenticator $authenticator;

    private array $errors = [];
    private array $imgArray = [
        'https://api-dev-minimal-v5.vercel.app/assets/images/avatar/avatar_25.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_6.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_7.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_5.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_8.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_9.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_10.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_11.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_12.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_13.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_14.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_15.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_16.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_17.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_18.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_19.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_20.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_21.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_22.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_23.jpg',
        'https://api-dev-minimal-v510.vercel.app/assets/images/avatar/avatar_24.jpg',
    ];
    private String $id;
    private String $username;
    private String $firstname;
    private String $lastname;
    private String $password;
    private String $email;
    private String $imgProfile;
    private Int $accountType;
    private Int $userKey;
    private String $token;
    private Int $phoneNumber;
    private String $subscription;
    private array $interest = [];

    private Int $experience;
    private string $specialty;
    private bool $isProvider;
    private bool $verified;
    private String $address;

    public function __construct(){
        $this->headers = new Header();
        $this->response = Response::create();
        $this->exception = new UserExceptions();    
        $this->authenticator = new Authenticator();
    }

    public function createUser($user_data){
        // by default 
        $this->setImgProfile($this->imgArray[rand(0,20)]);
        $this->setAccountType(0); 
        $this->setUserKey($this->generateUserKey()); 
        $this->setSpecialty('none');
        $this->setExperience(0);
        $this->setIsProvider(0);
        $this->setSubscription('free');
        $this->setToken('');
        $this->setVerified(0);

        // Je check les Données et je les assignes directement avec cette methode
        $this->checkData($user_data);
        // je check mon tableau d'erreur
        if(sizeof($this->errors) == 0){
            // je recupère les données assigner à mes variable de classe  et je les assignes (pour mon objet user)
            $this->setEmail($this->email);
            $this->setPassword($this->password);
            $this->setLastname($this->lastname);
            $this->setFirstname($this->firstname);
            $this->setUsername($this->username);

            $this->setPhoneNumber($this->phoneNumber);
            $this->setInterest($this->interest);
            $this->setId($this->generateId());

        // j'instantie mon objet user
        $user = new User(
            $this->id,
            $this->username,
            $this->firstname, 
            $this->lastname, 
            $this->email, 
            $this->password, 
            $this->imgProfile,
            $this->accountType,
            $this->userKey,
            $this->token,
            $this->phoneNumber, 
            $this->interest,
            $this->specialty,
            $this->experience,
            $this->isProvider,
            $this->subscription,
            $this->verified,
            $this->address
        );
            // j'instancie mon objet model qui me sert à communiquer avec le serveur
            $userModel = new UserModel();
            // j'appelle une method de mon model qui insert l'user en base de données
            $insertUser = $userModel->insertUsr($user);
            echo $this->response
            ->sendResponse(201, true,'User created successfully',$insertUser);
            return true;
        }else{
            // je retourne une reponse avec le status code 400 et un message d'erreur si il y a une erreur
            echo $this->response
            ->sendResponse(400, false,$this->errors,null);
            return false;
        }
    }

    private function checkData($data){
        foreach($data as $key => $value){
            if(in_array($key, ['firstname', 'lastname', 'username'])) {
                $this->checkName([$key => $value]);
            }
            switch($key){
                case 'email':
                    $this->checkEmail($value);
                    break;
                case 'password':
                    $this->checkPassword($value);
                    break;
                case 'phoneNumber':
                    $this->checkphoneNumber($value);
                    break;
                case 'interest':
                    // check interest get a array, so get the array
                    $value = is_array($value) ? $value : json_decode($value);
                    $this->checkInterest($value);
                    break;
                case 'address':
                    $this->checkAddress($value);
                    break;
            }

        }
    }

    private function generateId(){
        $id = uniqid();
        return $id;
    }
    private function shuffleAvatar(){
        $avatar = array_rand($this->avatar);
        return $this->avatar[$avatar];
    }
    private function checkId($id){
        $pdo = ConnectDb::getInstance();
        $statement = $pdo->prepare("SELECT id FROM users WHERE id = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    
        $result = $statement->fetch();
        ConnectDb::closeConnexion();

        if(!$result){
            return true;
        }
        return false;
    }

    
    private function checkEmail($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->errors[] = "Incorrect email format";
            return false;
        }
        
        $pdo = ConnectDb::getInstance();
        $statement = $pdo->prepare("SELECT email FROM users WHERE email = :email");
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch();
        ConnectDb::closeConnexion();

        if(!$result){
            $this->email = htmlspecialchars($email);
            return true;
        }else{
            $this->errors[] = "Email already used";
            return false;
        }

    }

    private function checkName($names){
        foreach($names as $name => $value){
            if(!preg_match('/^[a-zA-Z0-9_]+$/', $value)){
                $this->errors[] = "Incorrect $name format";
                return false;
            }
            // assign names on my class variable dynamically
            $this->$name = htmlspecialchars($value);
        }
        return true;
    }

    private function checkAddress($address){
        if(!preg_match('/^[a-zA-Z0-9_ ]+$/', $address)){
            $this->errors[] = "Incorrect address format";
            return false;
        }
        $this->address = htmlspecialchars($address);
        return true;
    }

    private function checkPassword($password)
    {
        $hasLowercase = preg_match('/(?=.*[a-z])/', $password);
        $hasUppercase = preg_match('/(?=.*[A-Z])/', $password);
        $hasDigit = preg_match('/(?=.*\d)/', $password);
        $hasMinLength = preg_match('/[a-zA-Z\d]{8,}/', $password);

        // Vérifier le mot de passe : min. 8 caractères, au moins une majuscule, une minuscule et un chiffre
        if (!$hasLowercase) {
            $this->errors[] = "Password must contains at least 1 lowercase letter";
        }
        if (!$hasUppercase) {
            $this->errors[] = "Password must contains at least 1 capital letter";
        }
        if (!$hasDigit) {
            $this->errors[] = "Password must contains at least 1 number";
        }
        if (!$hasMinLength) {
            $this->errors[] = "Password must contains at least 8 characters";
        }

        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return true;
    }

    private function checkphoneNumber($phoneNumber){
        // $phoneNumber = str_replace(' ', '', $phoneNumber);
        // $phoneNumber = intval($phoneNumber);
        // $phoneNumber = filter_var($phoneNumber, FILTER_SANITIZE_NUMBER_INT);
        // if(!filter_var($phoneNumber, FILTER_VALIDATE_INT) || strlen($phoneNumber) != 10){
        //     $this->errors[] = "Incorrect phone number format";
        //     return false;
        // }
        $this->phoneNumber = $phoneNumber;
        return true;
    }


    private function accountType($accountType){
        // les comptes providers doivent être crée par les Admin ou superAdmin.
        if($accountType != 0 || $accountType != 1 || $accountType != 2 || $accountType != 3){
            $this->errors[] = "Incorrect account type format";
        }
        $this->accountType = htmlspecialchars($accountType);
        return true;
    }

    private function isValidJson($jsonString)
    {
        json_decode($jsonString);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function convertArrayToJson($array){
        $json = json_encode($array);
        return $json;
    }

    private function generateUserKey() {
        // Crée un timestamp actuel et le concatène avec un nombre aléatoire pour le rendre unique
        return intval(substr(uniqid(), 10));
    }
    
    
    private function checkInterest($interest){
        $arrayChoice = ['Discover', 'Healthy living', 'Easy Fit', 'Vegetarian', 'Glutent', 'Nut Free', 'Easy Cooking', 'Good Fat'];
        foreach($interest as $value){
            if(!in_array($value, $arrayChoice)){
                $this->errors[] = "Incorrect interest format";
                return false;
            }else{
                array_push($this->interest, $value);
            }
        }
        return true;
    }

    // ----------------------------------------------------------------------------------------------------------------

    // ----------------------------------------------------------------------------------------------------------------

    public function getAllUsers()
    {
        $payload = true;
        try {
            if ($payload) {
                $model = new UserModel();
                $users = $model->getAllUsers();

                $responseArray = [];
                foreach ($users as $user) {
                    $responseArray[] = [
                        "id"  => $user["id"],
                        "zipCode"  => "75012",
                        "state" => 'Ile de France',
                        "city" => 'Paris',
                        "email" => $user['email'],
                        "address" => $user['address'],
                        "name" => $user['firstname'].' '.$user['lastname'],
                        "isVerified" => $user['verified'],
                        "country"  => 'France',
                        "avatarUrl"  => $user['img_profile'],
                        "phoneNumber" => $user['phone_number'],
                        "status" => '',
                        "role"  => $user['account_type'] == 2 || $user['account_type'] == 3 ? 'admin' : 'user',
                        "status"  => '',
                        "company"  => '',
                        "username"  => $user['username'],
                        "subscription"  => $user['subscription'],
                        "specialty"  => $user['specialty'],
                        "experience"  => $user['experience'],
                        "is_provider"  => $user['account_type'] == 1 ? true : false,
                        "interest"  => $user['interest'],
                    ];
                }
                echo $this->response->sendResponse(200, true, 'Recipely users', $responseArray);
            } else {
                echo $this->response->sendResponse(404, false, 
                [
                    $this->exception->notFound() ? $payload == true : $payload == false,
                    $this->exception->unAuthorized()
                ],null);
            }
            return true;
        } catch (UserExceptions $e) {
            echo $this->response->sendResponse(500, false, $e->getMessage(),null);
           return false;
        }
    }


    public function getUser($id){
        try{
            $model = new UserModel();
            $user = $model->getUser($id);
            if($user){
                echo $this->response
                ->withStatusCode(200)
                ->withHeader('X-Server', 'RECIPELY')
                ->withBody([
                    "Success:" => true,
                    "message" => "Recipely user",
                    "data" => [
                        "id"  => $user["id"],
                        "zipCode"  => "75012",
                        "state" => 'Ile de France',
                        "city" => 'Paris',
                        "email" => $user['email'],
                        "address" => $user['address'],
                        "name" => $user['firstname'].' '.$user['lastname'],
                        "isVerified" => $user['verified'],
                        "country"  => 'France',
                        "avatarUrl"  => $user['img_profile'],
                        "phoneNumber" => $user['phone_number'],
                        "status" => '',
                        "role"  => $user['account_type'] == 2 || $user['account_type'] == 3 ? 'admin' : 'user',
                        "status"  => '',
                        "company"  => '',
                        "username"  => $user['username'],
                        "subscription"  => $user['subscription'],
                        "specialty"  => $user['specialty'],
                        "experience"  => $user['experience'],
                        "is_provider"  => $user['account_type'] == 1 ? true : false,
                        "interest"  => $user['interest'],
                    ]
                ])->json();
            } else {
                echo $this->response
                ->withStatusCode(404)
                ->withHeader('X-Server', 'RECIPELY')
                ->withBody([
                    "Success:" => false,
                    "message" => $this->exception->notFound(),
                ])->json();
            }
            exit;
        } catch (Exception $e){
            echo $this->response
            ->withStatusCode(500)
            ->withHeader('X-Server', 'RECIPELY')
            ->withBody([
                "Success:" => false,
                "errors" => $e->getMessage()
            ]);
            exit;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $model = new UserModel();
            $user = $model->getUserByEmail($email);
            if ($user == false) {
                return false;
            } else {
                return $user;
            }
        } catch (UserExceptions $e) {
            return $e->notFoundEmail();
        }
    }
    
    public function deleteUser($id){
        try{
            $model = new UserModel();
            $user = $model->deleteUser($id);

            if($user == true){
                echo $this->response
                ->withStatusCode(200)
                ->withHeader('X-Server', 'RECIPELY')
                ->withBody([
                    "Success:" => true,
                    "message" => "Recipely user",
                ])->json();
            } else {
                echo $this->response
                ->withStatusCode(404)
                ->withHeader('X-Server', 'RECIPELY')
                ->withBody([
                    "Success:" => false,
                    "message" => $this->exception->notFound(),
                ])->json();
            }
            exit;
        } catch (Exception $e){
            echo $this->response
            ->withStatusCode(500)
            ->withHeader('X-Server', 'RECIPELY')
            ->withBody([
                "Success:" => false,
                "errors" => $e->getMessage()
            ]);
            exit;
        }
    }

    public function update($id,$user){
        try{
            $model = new UserModel();
            $user = $model->update($id,$user);

            if(!empty($user)){
                echo $this->response
                ->withStatusCode(200)
                ->withHeader('X-Server', 'RECIPELY')
                ->withBody([
                    "Success:" => true,
                    "message" => "Recipely user",
                    "data" => $user
                ])->json();
            } else {
                echo $this->response
                ->withStatusCode(404)
                ->withHeader('X-Server', 'RECIPELY')
                ->withBody([
                    "Success:" => false,
                    "message" => $this->exception->notFound(),
                ])->json();
            }
            exit;
        } catch (Exception $e){
            echo $this->response
            ->withStatusCode(500)
            ->withHeader('X-Server', 'RECIPELY')
            ->withBody([
                "Success:" => false,
                "errors" => $e->getMessage()
            ]);
            exit;
        }
    }
}