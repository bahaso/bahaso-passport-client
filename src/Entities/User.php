<?php

namespace Bahaso\PassportClient\Entities;


use Bahaso\PassportClient\Entities\Contracts\UserInterface;
use Illuminate\Contracts\Support\Arrayable;

class User implements UserInterface, Arrayable
{
    protected $id;
    protected $username;
    protected $fullname;
    protected $email;

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $fullname
     * @param $email
     */
    public function __construct($id, $username, $fullname, $email)
    {
        $this->id = $id;
        $this->username = $username;
        $this->fullname = $fullname;
        $this->email = $email;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'fullname' => $this->fullname,
            'email' => $this->email
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFullName()
    {
        return $this->fullname;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
