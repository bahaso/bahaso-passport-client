<?php

namespace Bahaso\PassportClient\Requests;


use Bahaso\PassportClient\Requests\Contracts\PassportRequest;

class SignInRequest implements PassportRequest
{
    public $username = "";
    public $email = "";
    public $password = "";
    public $client_id = "";
    public $client_secret = "";
    public $grant_type = "";
    public $scope = "";

    public function __construct($client_id, $client_secret, $username, $password, $grant_type, $scope)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->username = $username;
        $this->password = $password;
        $this->grant_type = $grant_type;
        $this->scope = $scope;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getClientSecret()
    {
        return $this->client_secret;
    }

    public function getGrantType()
    {
        return $this->grant_type;
    }

    public function getScope()
    {
        return $this->scope;
    }
}
