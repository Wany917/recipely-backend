<?php

namespace Recipely\Controllers\user;

use Recipely\Traits\user\Getters;
use Recipely\Traits\user\Setters;

class User
{
    use Getters, Setters;
    
    private String  $id;
    private String $username;
    private String $firstname;
    private String $lastname;   
    private String $email;
    private String $password;
    private String $imgProfile;
    private String $accountType;
    private Int $userKey;
    private String $token;
    private Int $phoneNumber;
    private Array $interest;

    private string $specialty;
    private Int $experience;
    private bool $isProvider;
    private String $subscription;
    private bool $verified;
    private String $address;


    public function __construct($id,$username,$firstname, $lastname, $email, $password, $imgProfile, $accountType, $userKey,$token ,$phoneNumber, $interest, $specialty, $experience, $isProvider, $subscription, $verified, $address)
    {
        $this->setId($id);
        $this->setUsername($username);
        $this->setFirstname($firstname);
        $this->setLastname($lastname);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setImgProfile($imgProfile);
        $this->setAccountType($accountType);
        $this->setUserKey($userKey);
        $this->setToken($token);
        $this->setPhoneNumber($phoneNumber);
        $this->setInterest($interest);
        $this->setSpecialty($specialty);
        $this->setExperience($experience);
        $this->setIsProvider($isProvider);
        $this->setSubscription($subscription);
        $this->setVerified($verified);
        $this->setAddress($address);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'password' => $this->password,
            'imgProfile' => $this->imgProfile,
            'accountType' => $this->accountType,
            'userKey' => $this->userKey,
            'token' => $this->token,
            'phoneNumber' => $this->phoneNumber,
            'interest' => $this->interest,
            'specialty' => $this->specialty,
            'experience' => $this->experience,
            'isProvider' => $this->isProvider,
            'subscription' => $this->subscription,
            'verified' => $this->verified,
            'address' => $this->address
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

}
