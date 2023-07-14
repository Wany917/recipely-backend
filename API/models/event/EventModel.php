<?php 

namespace Recipely\Models\event;

use PDO,Exception;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\event\Event;
use Recipely\Lib\Response;
use Recipely\Utils\EventExceptions;

class EventModel{
    public function insertEvent(Event $event){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
                "INSERT INTO events (type_event, date, time, location, id_client) 
                VALUES (:type_event, :date, :time, :location, :id_client)"
            );

            $params = [
                ':type_event' => $event->toArray()['type_event'],
                ':date' => $event->toArray()['date_event'],
                ':time' => $event->toArray()['time'],
                ':location' => $event->toArray()['location'],
                ':id_client' => $event->toArray()['id_client']
            ];            

            $stmt->execute($params);

            $lastInsertedId = $pdo->lastInsertId();
            $stmt = $pdo->prepare(
                "SELECT type_event, date, time, location, id_client FROM events WHERE id = :id"
            );
            $eventData = $stmt->execute([':id' => $lastInsertedId]);
            $eventData = $stmt->fetch(PDO::FETCH_ASSOC);

            ConnectDb::closeConnexion();
            return $eventData;
        } catch(EventExceptions $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function updateEvent($id, $event_data){
        try{
            $eventData = json_decode($event_data, true);

            if(json_last_error() != JSON_ERROR_NONE){
               return false;
            }
            if(!isset($id)){
               return false;
            }

            $pdo = ConnectDb::getInstance();
            $sql_search = "SELECT id,type_event, date, time, location, id_client FROM events WHERE id = :id";

            $stmt = $pdo->prepare($sql_search);
            $stmt->execute([':id' => $id]);
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$fetch){
               return false;
            } else {
                $params = [':id' => $id];
                $updates = [];

                // les données recu sont tu types string convertir la en tableau et fait ton foreach
                foreach($eventData as $key => $value){
                    if($key !== 'id'){
                       $updates[] = $key . ' = :' . $key;
                       $params[':' . $key] = $value;
                    }
                }
                
                if(sizeof($updates) > 0){
                    $sql_update = "UPDATE events SET " . implode(', ', $updates) . " WHERE id = :id";
                    $stmt = $pdo->prepare($sql_update);
                    $stmt->execute($params);

                    $stmt = $pdo->prepare($sql_search);
                    $stmt->execute([':id' => $id]);
                    $eventData = $stmt->fetch(PDO::FETCH_ASSOC);

                    ConnectDb::closeConnexion();
                    return $eventData;
                }
            }
        } catch(EventExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notUpdated();
        }
    }

    public function deleteEvent($id){
        try{
            if(!isset($id)){
                return "Missing id";
            }
            if($this->checkValue($id, 'id', 'events')){
                return "Event not found";
            }else{
                $pdo = ConnectDb::getInstance();
                $sql_search = "SELECT id,type_event, date, time, location, id_client FROM events WHERE id = :id";
    
                $stmt = $pdo->prepare($sql_search);
                $stmt->execute([':id' => $id]);
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if(!$fetch){
                    return "Event not found";
                } else {
                    $sql_delete = "DELETE FROM events WHERE id = :id";
                    $stmt = $pdo->prepare($sql_delete);
                    $stmt->execute([':id' => $id]);
    
                    ConnectDb::closeConnexion();
                    return "Event deleted";
                }
            }

        } catch(EventExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notDeleted();
        }
    }

    public function getAllEvents(){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT id,type_event, date, time, location, id_client FROM events");
            $stmt->execute();

            $fetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return empty($fetch) ? false : $fetch = $fetch;
            ConnectDb::closeConnexion();
        } catch(EventExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getEvent($id){
        try{
            if(!isset($id)){
                return false;
            }
            if($this->checkValue($id, 'id', 'events')){
                return false;
            }else{
                $pdo = ConnectDb::getInstance();
                $stmt = $pdo->prepare("SELECT id,type_event, date, time, location, id_client FROM events WHERE id = :id");
                $stmt->execute([':id' => $id]);
    
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                ConnectDb::closeConnexion();
                return $fetch;
            }
        } catch(EventExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }
    
    public function checkValue($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if(empty($fetch)){
            return true;
        } else{
            return false;
        }
    }
}