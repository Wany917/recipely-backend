<?php
namespace Recipely\Controllers\chat;

use Recipely\Models\chat\ChatsModel;
class ChatsControllers{

    private String $id;
    public function createChat($chat_data): string{
       if(!$this->checkMsg($chat_data)){
            return json_encode(['error' => 'Missing data']);
        }

        do {
            $id = $this->generateId();
        } while ($this->checkId($id));

        $chat = new Chats(
            $id,
            $chat_data['sender_id'],
            $chat_data['receiver_id'],
            $chat_data['content'],
            $chat_data['is_read'],
            $chat_data['deleted_by_sender'],
            $chat_data['deleted_by_receiver']
        );

        $chatModel = new ChatsModel();
        $chatModel->insertChat($chat);
        
        return json_encode($chat);
    }

    private function generateId(): string{
        $this->id = bin2hex(random_bytes(16));
        return $this->id;
    }

    private function checkId($id): bool{
        $chatModel = new ChatsModel();
        $chat = $chatModel->getChatById($id);
        if($chat){
            return true;
        }
        return false;
    }

    private function checkMsg($chat_data): bool {
        return isset(
            $chat_data['content'], 
            $chat_data['sender_id'],
            $chat_data['receiver_id'],
            $chat_data['is_read'],
            $chat_data['deleted_by_sender'],
            $chat_data['deleted_by_receiver']
        );
    }
    
   
}