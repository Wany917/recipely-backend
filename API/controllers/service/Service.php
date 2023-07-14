<?php 

namespace Recipely\Controllers\service;

class Service {
    private Int $id;
    private String $type_service;
    private Float $price;

    public function __construct(Int $id, String $type_service, Float $price)
    {
        $this->id = $id;
        $this->type_service = $type_service;
        $this->price = $price;
    }

    public function toArray(): array{
        return [
            'id' => $this->id,
            'type_service' => $this->type_service,
            'price' => $this->price
        ];
    }
}