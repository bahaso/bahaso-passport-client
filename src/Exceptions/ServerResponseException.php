<?php

namespace Bahaso\PassportClient\Exceptions;


use Exception;

class ServerResponseException extends Exception
{
    protected $httpCode;
    protected $success = false;
    protected $errors;
    protected $headers;
    protected $data;

    public function __construct($httpCode = 400, $message = "Bad Response", $errors = [], $data = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($message, $code, $previous);
        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->errors = $errors;
        $this->headers = $headers;
        $this->data = $data;

    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $response = [];

            $errors = $this->getErrors();

            $response['status'] = $this->getHttpCode();
            $response['success'] = false;
            $response['message'] = $this->getMessage();
            if ($errors) $response['errors'] = $errors;

            return response()
                ->json($response);
        }

        return response($this);
    }
}
