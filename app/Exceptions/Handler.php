<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::prepareJsonResponse($request, $exception);
    }

    protected function convertExceptionToArray(Throwable $e)
    {
        $status_code = 500;
        if ($this->isHttpException($e)) {
            $status_code = $e->getStatusCode();
        }

        $data = [];
        if ($e instanceof ValidationException) {
            $data = $e->errors();
        }
        
        return config("app.debug", false) ? [
            "code" => $status_code,
            "message" => $e->getMessage(),
            "data" => $data,
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "trace" => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ["args"]);
            })->all(),
        ] : [
            "code" => $status_code,
            "message" => $this->isHttpException($e) ? $e->getMessage() : "Server Error",
            "data" => $data,
        ];
    }
}
