<?php

namespace Bahaso\PassportClient\Requests;


use Bahaso\PassportClient\Requests\Contracts\PassportRequest;

class SocialAuthRequest implements PassportRequest
{
    public $access_token = "";
    public $client_id = "";
    public $client_secret = "";
    public $grant_type = "";
    public $scope = "";

    public function __construct($access_token, $client_id, $client_secret, $grant_type, $scope)
    {
        $this->access_token = $access_token;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->grant_type = $grant_type;
        $this->scope = $scope;
    }

    public function getAccessToken()
    {
        return $this->access_token;
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
