<?php namespace Recipely\Controllers\room;
class Room{
    private Int $id;
    private Int $capacity;
    private Int $district;
    private String $address;
    private String $name_room;

    public function __construct(
        $id,
        $capacity,
        $district,
        $address,
        $name_room
    )
    {
     $this->id = $id;
     $this->capacity = $capacity;
     $this->district = $district;
     $this->address = $address;
     $this->name_room = $name_room;   
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'capacity' => $this->capacity,
            'district' => $this->district,
            'address' => $this->address,
           'name_room' => $this->name_room
        ];
    }

    public function toJson(): String
    {
        return json_encode($this->toArray());
    }

}

