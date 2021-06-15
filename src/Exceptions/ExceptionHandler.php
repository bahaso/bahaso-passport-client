<?php

namespace Bahaso\PassportClient\Exceptions;


use Illuminate\Foundation\Exceptions\Handler;

class ExceptionHandler extends Handler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(\Exception $e)
    {
        return parent::report($e);
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $exception)
    {
        if ($request->wantsJson() || $request->ajax()) {
            if ($exception instanceof ServerResponseException || $exception instanceof PassportClientException) {
                // TODO : buat object response
                $response = [];

                $data = $exception->getData();
                $errors = $exception->getErrors();

                $response['status'] = $exception->getHttpCode();
                $response['success'] = false;
                $response['message'] = $exception->getMessage();
                if ($data) $response['data'] = $data;
                if ($errors) $response['errors'] = $errors;

                return response()
                    ->json($response);
            }
            else {
                // TODO : buat object response
                $response = [];

                $response['status'] = $exception->getCode();
                $response['success'] = false;
                $response['message'] = $exception->getMessage();

                return response()
                    ->json($response);
            }
        }
        return parent::render($request, $exception);
    }
}
