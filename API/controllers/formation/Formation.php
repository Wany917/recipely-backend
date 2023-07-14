<?php 

namespace Recipely\Controllers\formation;

class Formation {
    private Int $id;
    private String $name;
    private String $description;
    private String $id_provider;
    private String $img;

    public function __construct(Int $id, String $name, String $description, String $id_provider, String $img)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->id_provider = $id_provider;
        $this->img = $img;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description, 
            'id_provider' => $this->id_provider,
            'img' => $this->img
        ];
    }
}