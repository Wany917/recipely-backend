<?php 
namespace Recipely\Traits\user;
trait Setters{
    public function setId(String $id): void{
        $this->id = $id;
    }
    public function setUsername(String $username):void
    {
        $this->username = $username;
    }
    public function setFirstname(String $firstname):void
    {
        $this->firstname = $firstname;
    }

    public function setLastname(String $lastname):void
    {
        $this->lastname = $lastname;
    }

    public function setEmail(String $email):void
    {
        $this->email = $email;
    }

    public function setPassword(String $password):void
    {
        $this->password = $password;
    }

    public function setImgProfile(String $imgProfile):void
    {
        $this->imgProfile = $imgProfile;
    }

    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
    }

    public function setUserKey($userKey)
    {
        $this->userKey = $userKey;
    }

    public function setPhoneNumber($phoneNumber):void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function setInterest($interest)
    {
        $this->interest = $interest;
    }

    public function setSubscription(String $subscription):void
    {
        $this->subscription = $subscription;
    }

    public function setSpecialty(String $specialty):void
    {
        $this->specialty = $specialty;
    }

    public function setExperience($experience)
    {
        $this->experience = $experience;
    }

    public function setIsProvider($isProvider)
    {
        $this->isProvider = $isProvider;
    }

    public function setToken(String $token):void
    {
        $this->token = $token;
    }

    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    public function setAddress(String $address):void
    {
        $this->address = $address;
    }
}