<?php

namespace Recipely\Controllers\chat;
use DateTime;
class Chats
{
    private String $id;
    private String $sender_id;
    private String $receiver_id;
    private String $content;
    private Bool $is_read;
    private Bool $deleted_by_sender;
    private Bool $deleted_by_receiver;

    public function __construct($id, $sender_id, $receiver_id, $content, $is_read, $deleted_by_sender, $deleted_by_receiver)
    {
        $this->id = $id;
        $this->sender_id = $sender_id;
        $this->receiver_id = $receiver_id;
        $this->content = $content;
        $this->is_read = $is_read;
        $this->deleted_by_sender = $deleted_by_sender;
        $this->deleted_by_receiver = $deleted_by_receiver;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'content' => $this->content,
            'is_read' => $this->is_read,
            'deleted_by_sender' => $this->deleted_by_sender,
            'deleted_by_receiver' => $this->deleted_by_receiver,
        ];
    }
}
