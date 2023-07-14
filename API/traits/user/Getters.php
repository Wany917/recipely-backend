<?php 
namespace Recipely\Traits\user;

trait Getters{
    public function getId()
    {
        return $this->id;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getImgProfile()
    {
        return $this->imgProfile;
    }

    public function getAccountType()
    {
        return $this->accountType;
    }

    public function getUserKey()
    {
        return $this->userKey;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getInterest()
    {
        return $this->interest;
    }

    public function getSubscription()
    {
        return $this->subscription;
    }

    public function getSpecialty()
    {
        return $this->specialty;
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function getIsProvider()
    {
        return $this->isProvider;
    }

    public function getToken()
    {
        return $this->token;
    }
}