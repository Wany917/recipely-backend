<?php

namespace Recipely\Controllers\provider;

use Exception;
use PDO;
use Recipely\Lib\Response;
use Recipely\Config\ConnectDb;
use Recipely\Auth\Authenticator;
use Recipely\Utils\ProviderExceptions;
use Recipely\Controllers\provider\Provider;
use Recipely\Models\provider\ProviderModel;

class ProviderControllers {
    private Response $response;
    private Authenticator $authenticator;
    private ProviderExceptions $providerExceptions;

    public function __construct()
    {
        $this->response = Response::create();
        $this->authenticator = new Authenticator();
        $this->providerExceptions = new ProviderExceptions();
    }

    private String $id_user;
    private String $lastname;
    private String $firstname;
    private String $specialty;
    private Int $experience;

    private $errors = [];

    public function createProvider($user_id,$provider_data){
        $payload = $this->authenticator->authenticateUser();
        $isAdmin = $this->authenticator->checkUserValues($payload, 'account_type',2,3);
        if(!$this->checkValue($user_id, 'user_id', 'providers') == true && $isAdmin == true){
            $this->id_user = $user_id;

            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT lastname, firstname FROM users WHERE id = :id");
            $stmt->execute(['id' => $this->id_user]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->lastname = $user['lastname'];
            $this->firstname = $user['firstname'];
            ConnectDb::closeConnexion();

            $this->checkData($provider_data);
    
            if(sizeof($this->errors) == 0){
                $provider = new Provider(
                    $this->lastname,
                    $this->firstname,
                    $this->specialty,
                    $this->experience,
                    $this->id_user,
                );
                $model = new ProviderModel();
                $provider_model = $model->insertProvider($provider);
                if($provider_model){
                    echo $this->response
                    ->sendResponse(201,true,"Provider created successfully", [$provider_model]);
                    return true;
                } else {
                    echo $this->response
                    ->sendResponse(400,false,$this->errors, "Error while creating Provider ..");
                    return false;
                }
            } else {
                echo $this->response
                ->sendResponse(400,false, $this->errors,"Error while creating Provider ..");
                return false;
            }

        } else{
            echo $this->response
            ->sendResponse(400,false,$this->checkValue($user_id, 'user_id', 'providers') ? 'Provider already exists' : 'You are not allowed to create a provider',null);
            return false;
        } 
    }

    private function checkData($provider_data){
        if(!isset($provider_data['specialty'])){
            $this->errors[] = 'specialty is required';
        }else{
            if(!is_string($provider_data['specialty'])){
                $this->errors[] = 'specialty must be a string';
            } else {
                $this->specialty = $provider_data['specialty'];
            }
        }
        
        if(!isset($provider_data['experience'])){
            $this->errors[] = 'experience is required';
        } else{
            if(!is_numeric($provider_data['experience'])){
                $this->errors[] = 'experience must be a number';
            } else {
                $this->experience = $provider_data['experience'];
            }
        }
    }

    private function checkValue($value, $column, $table): bool {
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    
        $fetch = $stmt->fetch();
        ConnectDb::closeConnexion();
    
        // si c'est vide je renvoie true sinon false
        if ($fetch) {
            return true;
        }
        return false;
    }

    // -------------------------------------CALL METHOD----------------------------------------\\
    public function getAllProviders(){
        try {
            $model = new ProviderModel();
            $providers = $model->getAllProviders();
            if($providers){
                echo $this->response
                ->sendResponse(200,true,"Providers found", $providers);
                return true;
            } else {
                echo $this->response
                ->sendResponse(400,false,$this->providerExceptions->notFound(),null
            );
                return false;
            }
        } catch (Exception $e) {
            echo $this->response
            ->sendResponse(500,false,"Internal Error ..",
            [$e->getMessage()]
        );
            return false;
        }
    }

    public function getProvider($id){
        if($id == ":id" || empty($id) || is_numeric($id)){
            echo $this->response
            ->sendResponse(400,false,$this->providerExceptions->notFound(), null);
            return false;
        }else{
            try {
                $model = new ProviderModel();
                $provider = $model->getProvider($id); 
                if($provider === false){
                    echo $this->response
                    ->sendResponse(404,false,'Provider not found',null);
                    return false;
                } else {
                    echo $this->response
                    ->sendResponse(200,true,"Provider found", $provider);
                    return true;
                }
            } catch (ProviderExceptions $e) {
                echo $this->response
                ->sendResponse(404,false,$e->getMessage(),null);
                return false;
            }
        }
    }

    public function updateProvider($id, $provider){
        try {
            $model = new ProviderModel();
            $provider = $model->updateProvider($id, $provider);
            if($provider){
                echo $this->response
                ->sendResponse(200,true,"Provider updated", $provider);
                return true;
            } else {
                echo $this->response
                ->sendResponse(400,false,"Error while updating Provider ..",
                [$this->providerExceptions->notFound()]
            );
                return false;
            }
        } catch (ProviderExceptions $e) {
            echo $this->response
            ->sendResponse(500,false,"Internal Error ..");
            return false;
        }
    }

    public function deleteProvider($id){
        $payload = $this->authenticator->authenticateUser();
        $isAdmin = $payload['account_type'];

        if($isAdmin == 2 || $isAdmin == 3){
            try {
                $model = new ProviderModel();
                $provider = $model->deleteProvider($id);
                if($provider){
                    echo $this->response
                    ->sendResponse(200,true,"Provider deleted", $provider);
                    return true;
                } else {
                    echo $this->response
                    ->sendResponse(400,false,"Error while deleting Provider ..",null);
                    return false;
                }
            } catch (Exception $e) {
                echo $this->response
                ->sendResponse(500,false,"Internal Error ..",null
                );
                return false;
            }
        } else {
            echo $this->response
            ->sendResponse(403,false,$this->providerExceptions->notAuthorized(),
            null);
            return false;
        }
    }
}



