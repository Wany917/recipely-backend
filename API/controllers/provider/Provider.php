<?php
namespace Recipely\Controllers\provider;

class Provider{
    private Int $id;
    private String $user_id;
    private String $lastname;
    private String $firstname;
    private String $specialty;
    private Int $experience;

    public function __construct(
        $firstname,
        $lastname,
        $specialty,
        $experience,
        $user_id,
    )
    {
     $this->lastname = $lastname;
     $this->firstname = $firstname;
     $this->specialty = $specialty;
     $this->experience = $experience;   
     $this->user_id = $user_id;
    }
   

    public function toArray(): array
    {
        return [
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'specialty' => $this->specialty,
            'experience' => $this->experience,
            'user_id' => $this->user_id
        ];
    }

}
