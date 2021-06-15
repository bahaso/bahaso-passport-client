<?php

namespace Bahaso\PassportClient\Entities\Contracts;


interface UserInterface
{
    public function getId();
    public function getFullName();
    public function getEmail();
}
