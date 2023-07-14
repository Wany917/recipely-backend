<?php

namespace Recipely\Controllers\recipe;

class Recipe {
    private String $id;
    private String $name;
    private String $description;
    private Int $serves;
    private Int $prep_time;
    private String $creator;
    private String $img_md;
    private String $img_sm;
    private String $video;

    public function __construct(String $id, String $name, String $description, Int $serves, Int $prep_time, String $creator, String $img_md, String $img_sm, String $video)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->serves = $serves;
        $this->prep_time = $prep_time;
        $this->creator = $creator;
        $this->img_md = $img_md;
        $this->img_sm = $img_sm;
        $this->video = $video;
    }

    public function toArray(): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'serves' => $this->serves,
            'prep_time' => $this->prep_time,
            'creator' => $this->creator,
            'img_md' => $this->img_md,
            'img_sm' => $this->img_sm,
            'video' => $this->video
        ];
    }

}