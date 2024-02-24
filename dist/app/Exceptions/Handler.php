<?php

namespace App\Exceptions;

use App\Http\Middleware\AccessLogger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected $internalDontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        BackedEnumCaseNotFoundException::class,
        ModelNotFoundException::class,
        MultipleRecordsFoundException::class,
        RecordsNotFoundException::class,
        SuspiciousOperationException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $sessionLogConfig = AccessLogger::getSessionLogConfig();

            $request = request();

            $log = new \stdClass();
            $log->method = $request->method();
            $log->url = $request->fullUrl();
            $log->headers = $request->headers->all();
            $log->queryParams = $request->getQueryString();
            $log->body = $request->all();
            $log->error = new \stdClass();
            $log->error->message = $e->getMessage();
            $log->error->file = $e->getFile();
            $log->error->line = $e->getLine();
            $log->error->trace = $e->getTrace();

            $channels = ['dg_requests_errors', $sessionLogConfig];

            $logMessage = $log->method.' '.$log->url."\n".json_encode($log, JSON_PRETTY_PRINT);
            Log::stack($channels)->error($logMessage);
        });
    }
}
