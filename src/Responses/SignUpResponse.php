<?php

namespace Bahaso\PassportClient\Responses;


class SignUpResponse extends Response
{
    public function __construct($code = 200, $success = true, $message = 'success', $data = [])
    {
        parent::__construct($code, $success, $message, $data);
    }
}
