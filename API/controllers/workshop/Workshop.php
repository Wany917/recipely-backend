<?php

namespace Recipely\Controllers\workshop;

class Workshop {
    private String $id;
    private Int $id_room;
    private String $id_provider;
    private String $name_workshop;
    private String $desc;

    public function __construct(String $id,Int $id_room, String $id_provider, String $name_workshop,String $desc)
    {
        $this->id = $id;
        $this->name_workshop = $name_workshop;
        $this->id_room = $id_room;
        $this->id_provider = $id_provider;
        $this->desc = $desc;
    }
   
    public function toArray(): array{
        return [
            "id" => $this->id,
            "id_room" => $this->id_room,
            "id_provider" => $this->id_provider,
            "name_workshop" => $this->name_workshop,
            "desc" => $this->desc
        ];
    }
}