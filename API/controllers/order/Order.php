<?php

namespace Recipely\Controllers\order;

class Order {
    private Int $id;
    private String $id_client;
    private String $id_service;

    public function __construct(Int $id, String $id_client, String $id_service)
    {
        $this->id = $id;
        $this->id_client = $id_client;
        $this->id_service = $id_service;
    }

    public function toArray(): array{
        return [
            'id' => $this->id,
            'id_client' => $this->id_client,
            'id_service' => $this->id_service
        ];
    }
}