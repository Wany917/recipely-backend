<?php
namespace Recipely\Models\provider;

use PDO,PDOException, InvalidArgumentException;
use Recipely\Config\ConnectDb;
use Recipely\Utils\ProviderExceptions;
use Recipely\Controllers\provider\Provider;


class ProviderModel {
    public function insertProvider(Provider $provider){

        try{
            $pdo = ConnectDb::getInstance();

            $stmt = $pdo->prepare(
                "INSERT INTO providers(lastname, firstname, specialty, experience, user_id) 
                VALUES (:lastname, :firstname, :specialty, :experience, :user_id)"
            );

            $params = [
                'lastname' => $provider->toArray()['lastname'],
                'firstname' => $provider->toArray()['firstname'],
                'specialty' => $provider->toArray()['specialty'],
                'experience' => $provider->toArray()['experience'],
                'user_id' => $provider->toArray()['user_id']
            ];
            $stmt->execute($params);

            $stmt = $pdo->prepare(
                "SELECT id, lastname,firstname,specialty,experience,user_id FROM providers WHERE user_id = :user_id"
            );

            $stmt->execute(['user_id' => $provider->toArray()['user_id']]);
            $providerData = $stmt->fetch(PDO::FETCH_ASSOC);
            
           // Set provider state when is created
           try{
               $sql = "UPDATE users SET is_provider = 1  WHERE id = :id";
               $stmt = $pdo->prepare($sql);
               $stmt->bindParam(':id', $providerData['user_id'], PDO::PARAM_STR);
               $stmt->execute();
                
               return $providerData;
               ConnectDb::closeConnexion();
           } catch(PDOException $e){
               ConnectDb::closeConnexion();
               return $e->getMessage();
           }
        } catch(PDOException $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function updateProvider($id, $provider){
        $provider = json_decode($provider, true);

        if(json_last_error() !== JSON_ERROR_NONE){
            return throw new ProviderExceptions("Invalid JSON");
        }

        if(!isset($id)){
            return throw new ProviderExceptions("Invalid ID");
        }
        
        try{
            $pdo = ConnectDb::getInstance();
            $sql_research = "SELECT id FROM providers WHERE id = :id";
            $stmt = $pdo->prepare($sql_research);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$fetch){
                return throw new ProviderExceptions("Invalid Provider");
            }

            $params = [
                'id' => $id,
            ];
            $updates = [];

            foreach($provider as $key => $value){
                if($key !== 'id'){
                    $params[$key] = $value;
                    $updates[] = $key." = :".$key;
                }
            }

            if(empty($updates)){
                return throw new ProviderExceptions("No fields provided to update");
            }

            $sql_patch = "UPDATE providers SET ".implode(', ', $updates)." WHERE id = :id";
            $stmt = $pdo->prepare($sql_patch);
            $stmt->execute($params);
            
            ConnectDb::closeConnexion();
            return $params;
        } catch (InvalidArgumentException $e){
            ConnectDb::closeConnexion();
            return throw new ProviderExceptions($e->getMessage());
        }
    }

    public function deleteProvider($id){
        try{
            if(empty($id)){
                return throw new ProviderExceptions("Empty ID");
            }
    
            $pdo = ConnectDb::getInstance();
            $sql_research = "SELECT user_id FROM providers WHERE id = :id";
            $stmt = $pdo->prepare($sql_research);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if(empty($fetch)){
                return throw new ProviderExceptions("Invalid Provider");
            } else {
                $params = [
                    'id' => $id,
                    'user_id' => $fetch['user_id'],
                ];
    
                $sql_delete = "DELETE FROM providers WHERE id = :id";
                $stmt = $pdo->prepare($sql_delete);
                $stmt->execute(['id' => $id]);                
    
                ConnectDb::closeConnexion();
                return true;
            }
        } catch (InvalidArgumentException $e){
            ConnectDb::closeConnexion();
            return throw new ProviderExceptions($e->getMessage());
        }
    }
    
    
    public function getProvider($id){
        if($id == ":id" || empty($id)){
            throw new ProviderExceptions("Invalid ID");
        }
    
        $pdo = ConnectDb::getInstance();
        $sql_research = "SELECT id, user_id, firstname, lastname, specialty, experience FROM providers WHERE id = :id";
        $stmt = $pdo->prepare($sql_research);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    
        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if(!$fetch){
            throw new ProviderExceptions("Provider not found");
        }
    
        ConnectDb::closeConnexion();
        return $fetch;
    }
    
    
    

    public function getAllProviders(){
        try{
            $pdo = ConnectDb::getInstance();
            $sql_research = "SELECT id, user_id, lastname, firstname, experience, specialty FROM providers";
            $stmt = $pdo->prepare($sql_research);
            $stmt->execute();

            $fetch = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($fetch)){
                return false;
            } else {
                ConnectDb::closeConnexion();
                return $fetch;
            }
        } catch (InvalidArgumentException $e){
            ConnectDb::closeConnexion();
            return throw new ProviderExceptions($e->getMessage());
        }
    }
}


