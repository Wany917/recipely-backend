<?php  
namespace Recipely\Models\room;

use Recipely\Lib\JsonRes;
use Recipely\Utils\RoomExceptions;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\room\Room;
use PDO, PDOException,InvalidArgumentException;

class RoomModel{
    public function insertRoom(Room $room){
        $db = ConnectDb::getInstance();

        $sql = "INSERT INTO rooms (id, capacity, district, address, name_room) VALUES (:id, :capacity, :district, :address, :name_room)";

        try{
            $stmt = $db->prepare($sql);
    
            $params = [
                ":id" => $room->toArray()['id'],
                ":capacity" => $room->toArray()['capacity'],
                ":district" => $room->toArray()['district'],
                ":address" => $room->toArray()['address'],
                ":name_room" => $room->toArray()['name_room']
            ];
            $stmt->execute($params);
            ConnectDb::closeConnexion();

            return $room->toArray();
        } catch(RoomExceptions $e){
            ConnectDb::closeConnexion();
           return $e->notCreated();
        }
    }


    public function deleteRoom($id){
        $db = ConnectDb::getInstance();

        $sql_research =  "SELECT id, name_room, capacity,address,district FROM rooms WHERE id = :id";
        $sql_delete = "DELETE FROM rooms WHERE id = :id";

        try{
            $stmt = $db->prepare($sql_research);
            $stmt->execute([':id' => $id]);
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);


            if(!$fetch){
                return throw new RoomExceptions('Room not found');
            };

            $stmt = $db->prepare($sql_delete);
            $params = [
                ":id" => $id
            ];
            
            $stmt->execute($params);
            ConnectDb::closeConnexion();

            return true;
        } catch(RoomExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notDeleted();
        }
    }


    public function updateRoom($id, $room){
       
       try{
            $room =  json_decode($room, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
               return throw new RoomExceptions('Invalid JSON provided');
            }

            if (!isset($id)) {
               return throw new RoomExceptions('Invalid ID provided');
            }

            $pdo = ConnectDb::getInstance();

            $sql_research =  "SELECT id, name_room, capacity,address,district FROM rooms WHERE id = :id";

            $stmt = $pdo->prepare($sql_research);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$fetch){
                return throw new RoomExceptions('Invalid ID provided');
            }
       
            $params = [':id' => $id];
            $updates = [];

            foreach ($room as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if(empty($updates)){
                return throw new RoomExceptions('Missing Parameters');
            }

            $sql_update = "UPDATE rooms SET ".implode(', ',$updates) ." WHERE id = :id";
            $stmt = $pdo->prepare($sql_update);
            $stmt->execute($params);

            ConnectDb::closeConnexion();

            return $room;
        } catch(RoomExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notUpdated();
        }
    }

    public function getRoom($id){
        $db = ConnectDb::getInstance();
        $sql = "SELECT id, name_room, capacity,address,district FROM rooms WHERE id = :id";

        try{
            $stmt = $db->prepare($sql);
            $params = [
                ":id" => $id
            ];
            $stmt->execute($params);
            $fetch =  $stmt->fetch(PDO::FETCH_ASSOC);

            ConnectDb::closeConnexion();
            return $fetch;
        } catch(RoomExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getAllRooms(){
        try {
            $pdo = ConnectDb::getInstance();

            $sql = "SELECT id, name_room, capacity,address,district FROM rooms";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $fetch =  $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(!$fetch){
                return throw new RoomExceptions('Rooms not found');
            } else{
                return $fetch;
            }
        } catch(RoomExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }
}
