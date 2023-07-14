<?php
namespace Recipely\Models\chat;

use PDOException,InvalidArgumentException;
use Recipely\Lib\JsonRes;
use  Recipely\Controllers\chat\Chats;
use Recipely\Config\ConnectDb;

class ChatsModel{
    public function insertChat(Chats $chat){
        try{
            $pdo = ConnectDb::getInstance()->ConnectDb();
            $request = $pdo->prepare(
                "INSERT INTO chats (id, sender_id, receiver_id, content, is_read, deleted_by_sender, deleted_by_receiver) 
                VALUES (:id, :sender_id, :receiver_id, :content, :is_read, :deleted_by_sender, :deleted_by_receiver)"
            );
            $request->execute($chat->toArray());

            $res = new JsonRes(201, ["X-Server:" => "API_TEST"],
            [
                "Success:" => true,
                "message" => "Chat sended !",
                "chat" => $chat->toArray()
            ]);
            return $res->send();
        } catch(PDOException $e){
            $error = new JsonRes(400, ["X-Server:" => "API_TEST"],
            [
                "Success:" => false,
                "message" => "Chat not sended :(",
                $e
            ]);
            return $error->send();
        }
    }

    public function updateChat($id,$chat){
        try{
            // Decode the JSON to a PHP associative array
            $chat = json_decode($chat, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('Invalid JSON provided');
            }
    
            if (!isset($id)) {
                throw new InvalidArgumentException('Missing id');
            }
    
            $pdo = ConnectDb::getInstance()->ConnectDb();
            $request = $pdo->prepare(
                "UPDATE chats SET sender_id = :sender_id, receiver_id = :receiver_id, content = :content, is_read = :is_read, deleted_by_sender = :deleted_by_sender, deleted_by_receiver = :deleted_by_receiver WHERE id = :id"
            );
            $request->execute($chat);
    
            $res = new JsonRes(200, ["X-Server:" => "API_TEST"],
            [
                "Success:" => true,
                "message" => "Chat updated !",
                "chat" => $chat
            ]);
            return $res->send();
        } catch(PDOException $e){
            $error = new JsonRes(400, ["X-Server:" => "API_TEST"],
            [
                "Success:" => false,
                "message" => "Chat not updated :(",
                $e
            ]);
            return $error->send();
        }
    }

    public function deleteChat($id){
        try{
            if (!isset($id)) {
                throw new InvalidArgumentException('Missing id');
            }
    
            $pdo = ConnectDb::getInstance()->ConnectDb();
            $request = $pdo->prepare(
                "DELETE FROM chats WHERE id = :id"
            );
            $request->execute([':id' => $id]);
    
            $res = new JsonRes(200, ["X-Server:" => "API_TEST"],
            [
                "Success:" => true,
                "message" => "Chat deleted !",
                "chat" => $id
            ]);
            return $res->send();
        } catch(PDOException $e){
            $error = new JsonRes(400, ["X-Server:" => "API_TEST"],
            [
                "Success:" => false,
                "message" => "Chat not deleted :(",
                $e
            ]);
            return $error->send();
        }
    }

    public function getMessage($id){
        try{
            if (!isset($id)) {
                throw new InvalidArgumentException('Missing id');
            }
    
            $pdo = ConnectDb::getInstance()->ConnectDb();
            $request = $pdo->prepare(
                "SELECT * FROM chats WHERE id = :id"
            );
            $request->execute([':id' => $id]);
            $chat = $request->fetch();
    
            $res = new JsonRes(200, ["X-Server:" => "API_TEST"],
            [
                "Success:" => true,
                "message" => "Chat found !",
                "chat" => $chat
            ]);
            return $res->send();
        } catch(PDOException $e){
            $error = new JsonRes(400, ["X-Server:" => "API_TEST"],
            [
                "Success:" => false,
                "message" => "Chat not found :(",
                $e
            ]);
            return $error->send();
        }
    }

    public function getAllMessages($sender_id,$receiver_id){
        try{
            if (!isset($sender_id)) {
                throw new InvalidArgumentException('Missing sender_id');
            }
            if (!isset($receiver_id)) {
                throw new InvalidArgumentException('Missing receiver_id');
            }
    
            $pdo = ConnectDb::getInstance()->ConnectDb();
            $request = $pdo->prepare(
                "SELECT * FROM chats WHERE sender_id = :sender_id AND receiver_id = :receiver_id OR sender_id = :receiver_id AND receiver_id = :sender_id"
            );
            $request->execute([':sender_id' => $sender_id, ':receiver_id' => $receiver_id]);
            $chats = $request->fetchAll();
    
            $res = new JsonRes(200, ["X-Server:" => "API_TEST"],
            [
                "Success:" => true,
                "message" => "Chats found !",
                "chats" => $chats
            ]);
            return $res->send();
        } catch(PDOException $e){
            $error = new JsonRes(400, ["X-Server:" => "API_TEST"],
            [
                "Success:" => false,
                "message" => "Chats not found :(",
                $e
            ]);
            return $error->send();
        }
    }

    public function getChatById($id): bool{
       try{
            if (!isset($id)) {
                throw new InvalidArgumentException('Missing id');
            }
    
            $pdo = ConnectDb::getInstance()->ConnectDb();
            $request = $pdo->prepare(
                "SELECT * FROM chats WHERE id = :id"
            );
            $request->execute([':id' => $id]);
            $chat = $request->fetch();
    
            if($chat){
                return true;
            } else {
                return false;
            }
       } catch(PDOException $e){
            return $e;
       }
    }
}