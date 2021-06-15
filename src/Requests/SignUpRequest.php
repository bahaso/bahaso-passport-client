<?php

namespace Bahaso\PassportClient\Requests;


use Bahaso\PassportClient\Requests\Contracts\PassportRequest;

class SignUpRequest implements PassportRequest
{
    private $fullname;
    private $email;
    private $password;
    public $client_id = "";
    public $client_secret = "";
    public $grant_type = "";
    public $scope = "";

    /**
     * SignUpRequest constructor.
     * @param $fullname
     * @param $email
     * @param $password
     * @param $grant_type
     */
    public function __construct($fullname, $email, $password, $client_id, $client_secret, $grant_type, $scope)
    {
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->grant_type = $grant_type;
        $this->scope = $scope;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->client_secret;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    public function getGrantType()
    {
        return $this->grant_type;
    }
}
