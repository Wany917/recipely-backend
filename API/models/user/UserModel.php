<?php
namespace Recipely\Models\user;

use PDO;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\user\User;
use Recipely\Utils\UserExceptions;

class UserModel {
    public function insertUsr(User $user){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
                "INSERT INTO users(
                    id, username, firstname, lastname, email, password, phone_number, interest, img_profile, 
                    account_type, user_key, token, subscription, address, verified, specialty, experience, is_provider
                ) 
                VALUES (
                    :id, :username, :firstname, :lastname, :email, :password, :phoneNumber, :interest, :imgProfile, 
                    :accountType, :userKey, :token, :subscription, :address, :verified, :specialty, :experience, :isProvider
                )"
            );
            
            $params = [
                ':id' => $user->getId(),
                ':username' => $user->toArray()['username'],
                ':firstname' => $user->toArray()['firstname'],
                ':lastname' => $user->toArray()['lastname'],
                ':email' => $user->toArray()['email'],
                ':password' => $user->toArray()['password'],
                ':imgProfile' => $user->toArray()['imgProfile'],
                ':accountType' => $user->toArray()['accountType'],
                ':userKey' => $user->toArray()['userKey'],
                ':phoneNumber' => $user->toArray()['phoneNumber'],
                ':interest' => json_encode($user->toArray()['interest'], JSON_UNESCAPED_UNICODE),
                ':token' => $user->toArray()['token'],
                ':subscription' => $user->toArray()['subscription'],
                ':address' => $user->toArray()['address'], // Assuming address is part of user data
                ':verified' => (int)$user->toArray()['verified'], // Or any default value
                ':specialty' => $user->toArray()['specialty'], // Assuming specialty is part of user data
                ':experience' => $user->toArray()['experience'], // Assuming experience is part of user data
                ':isProvider' => (int)$user->toArray()['isProvider'], // Assuming is_provider is part of user data
            ];
            
            $stmt->execute($params);

            ConnectDb::closeConnexion();
            return $user->toArray();
            
        } catch(UserExceptions $e){
            ConnectDb::closeConnexion();
           return $e->notCreated();
        }
    }

    public function update($id,$user){
        try{
            // Decode the JSON to a PHP associative array
            $user = json_decode($user, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return throw new UserExceptions('Invalid JSON provided');
            }
    
            if (!isset($id)) {
                return throw new UserExceptions('Invalid ID provided');
            }
    
            $pdo = ConnectDb::getInstance();
    
            // Check if the user exists
            $stmt = $pdo->prepare(
            "SELECT username, firstname, lastname, email, password, phone_number, interest, img_profile, 
            account_type, user_key, token, subscription, address, verified, specialty, experience, is_provider 
            FROM users WHERE id = :id"
            );

            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$result){
                return throw new UserExceptions('Invalid ID provided');
            }

            $params = [':id' => $id];
            $updates = [];
    
            foreach ($user as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
    
            if (empty($updates)) {
                return throw new UserExceptions('No fields provided to update');
            } else {
                $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            ConnectDb::closeConnexion();
            return $user;
        } catch(UserExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notUpdated();
        }
    }    

    public function deleteUser($id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            ConnectDb::closeConnexion();
            return true;
        } catch(UserExceptions $e){
            ConnectDb::closeConnexion();
            return false;
        }
    }

    public function getUser($id){
        try{
            $pdo = ConnectDb::getInstance();
            $sql = "SELECT id,username, firstname, lastname, email, password, phone_number, interest, img_profile, 
                account_type, user_key,subscription, address, verified, specialty, experience, is_provider 
                FROM users WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            $params = [':id' => $id];
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$result){
                return throw new UserExceptions('No user found');
            }
            ConnectDb::closeConnexion();
            return $result;
        } catch(UserExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getUserByEmail(String $email){
        try{
            if ($this->checkValue($email, 'email','users')){
                $pdo = ConnectDb::getInstance();
                $sql = "SELECT id,username, firstname, lastname, email, password, phone_number, interest, img_profile, 
                    account_type, user_key, token, subscription, address, verified, specialty, experience, is_provider 
                    FROM users WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $params = [':email' => $email];
                $stmt->execute($params);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$result){
                    throw new UserExceptions('No user found');
                    exit;
                }
                ConnectDb::closeConnexion();
                return $result;
            } else {
                return false;
            }
        } catch(UserExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getAllUsers(){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
                "SELECT id, username, firstname, lastname, email, password, phone_number, interest, img_profile, 
                account_type, user_key, token, subscription, address, verified, specialty, experience, is_provider
                FROM users"
            );

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(!$result){
                return throw new UserExceptions('No users found');
            }
            ConnectDb::closeConnexion();
            return $result;

        } catch(UserExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function setProvider($id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("UPDATE users SET is_provider = 1 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if(!$stmt){
                return throw new UserExceptions('No user found');
            }
            ConnectDb::closeConnexion();
            return $id;

        } catch(UserExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notUpdated();
        }
    }


    private function checkValue($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute([':value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if($fetch){
            return true;
        } else{
            return false;
        }
    }

}