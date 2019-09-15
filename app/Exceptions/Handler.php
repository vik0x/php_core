<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->wantsJson()) { // add ´Accept: application/json´ in request
            return $this->handleApiException($request, $exception);
        }
        return parent::render($request, $exception);
    }

    private function handleApiException($request, Exception $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->customApiResponse($exception);
    }

    private function customApiResponse($exception)
    {
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $response = [];

        switch ($statusCode) {
            case 401:
                $response['message'] = trans('http_errors.e_401');
                break;
            case 403:
                $response['message'] = trans('http_errors.e_403');
                break;
            case 404:
                $response['message'] = trans('http_errors.e_404');
                break;
            case 405:
                $response['message'] = trans('http_errors.e_405');
                break;
            case 422:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            default:
                $message = $exception->getMessage();
                if (!config('app.debug')) {
                    $message = trans('http_errors.common_error');
                }
                $response['message'] = ($statusCode == 500) ? $message : $exception->getMessage();
                break;
        }

        if (config('app.debug')) {
            $response['exception'] = $exception;
            if (method_exists($exception, 'getTrace')) {
                $response['trace'] = $exception->getTrace();
            }

            if (method_exists($exception, 'getCode')) {
                $response['code'] = $exception->getCode();
            }
        }

        $response['status'] = $statusCode;
        return response()->json($response, $statusCode);
    }
}
