<?php 

namespace Recipely\Models\workshop;

use PDO, PDOException;
use Recipely\Lib\JsonRes;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\workshop\Workshop;
use Recipely\Utils\WorkshopExceptions;

class WorkShopModel{
    public function insertWorkshop(Workshop $workshop){
       try{

        $pdo = ConnectDb::getInstance();
        $sql_insert = "INSERT INTO workshops(id,name_workshop,description,id_room,id_provider) VALUES(:id,:name_workshop,:description,:id_room,:id_provider)";

        $stmt = $pdo->prepare($sql_insert);
        $params = [
            ':id' => $workshop->toArray()['id'],
            ':name_workshop' => $workshop->toArray()['name_workshop'],
            ':description' => $workshop->toArray()['desc'],
            ':id_room' => $workshop->toArray()['id_room'],
            ':id_provider' => $workshop->toArray()['id_provider']
        ];
        $stmt->execute($params);

        ConnectDb::closeConnexion();
        return $workshop->toArray();

       } catch(WorkshopExceptions $e){
        
        ConnectDb::closeConnexion();
        return $e->notCreated();
       }
    }

    public function updateWorkshop($id,$workshop){
       try{
        $workshop =  json_decode($workshop, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return throw new WorkshopExceptions('Invalid JSON');
            }

            if (!isset($id)) {
                return throw new WorkshopExceptions('Missing ID');
            }

            $pdo = ConnectDb::getInstance();

            $sql_research =  "SELECT id, name_workshop, id_room, id_provider FROM workshops WHERE id = :id";

            $stmt = $pdo->prepare($sql_research);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$fetch){
               return throw new WorkshopExceptions('Workshop not found');
            }
       
            $params = [':id' => $id];
            $updates = [];

            foreach ($workshop as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if(empty($updates)){
                return throw new WorkshopExceptions('Missing parameters');
            }

            $sql_update = "UPDATE workshops SET ".implode(', ',$updates) ." WHERE id = :id";
            $stmt = $pdo->prepare($sql_update);
            $stmt->execute($params);

            ConnectDb::closeConnexion();
            return $workshop;

       }catch(WorkshopExceptions $e){
        ConnectDb::closeConnexion();
        return $e->notUpdated();
       }
    }

    public function deleteWorkshop($id){
        try{
            // check if he exist
            $pdo = ConnectDb::getInstance();
            $sql_research = "SELECT id FROM workshops WHERE id = :id";

            $stmt = $pdo->prepare($sql_research);
            $params = [
                ':id' => $id
            ];
            $stmt->execute($params);

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$fetch){
                ConnectDb::closeConnexion();
                return throw new WorkshopExceptions('Workshop not found');
            }else{
                $sql_delete = "DELETE FROM workshops WHERE id = :id";
                $stmt = $pdo->prepare($sql_delete);
                $stmt->execute([':id' => $id]);
    
                ConnectDb::closeConnexion();
                return true;
            }


        } catch(WorkshopExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notDeleted();
        }
    }

    public function getWorkshop($id){
        try{
            $pdo = ConnectDb::getInstance();
            $sql_research = "SELECT id, name_workshop, description, id_room, id_provider FROM workshops WHERE id = :id";

            $stmt = $pdo->prepare($sql_research);
            $stmt->execute([
                ':id' => $id
            ]);

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$fetch){
                return throw new WorkshopExceptions('Workshop not found');
            }else{
                ConnectDb::closeConnexion();
                return $fetch;
            }
        } catch(WorkshopExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getAllWorkshops(){
        try{
            $pdo = ConnectDb::getInstance();

            $sql = "SELECT id, name_workshop, description, id_room, id_provider FROM workshops";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $fetch = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!$fetch){
                ConnectDb::closeConnexion();
                return throw new WorkshopExceptions('Workshop not found');
            }else{
                ConnectDb::closeConnexion();
                return $fetch;
            }

        } catch(WorkshopExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }
}
