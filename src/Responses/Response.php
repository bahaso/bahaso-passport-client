<?php

namespace Bahaso\PassportClient\Responses;


class Response
{
    public $status;
    public $success;
    public $message;
    public $data;

    public function __construct($code = 200, $success = true, $message = 'success', $data = [])
    {
        $this->status = $code;
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    public function setStatus($status)
    {
        return $this->status = $status;
    }

    public function setSuccess($success)
    {
        return $this->success = $success;
    }

    public function setMessage($message)
    {
        return $this->message = $message;
    }

    public function setData($data)
    {
        return $this->data = $data;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }
}
