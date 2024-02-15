<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class AccessLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Str::contains($request->userAgent(), 'TheExit'))
            return $next($request);

        $log = new stdClass();
        $log->method = $request->method();
        $log->url = $request->fullUrl();
        $log->headers = $request->headers->all();
        $log->queryParams = $request->getQueryString();
        $log->body = $request->all();

        try {
            $response = $next($request);
            $log->response = new stdClass();
            $log->response->statusCode = $response->getStatusCode();
            $log->response->body = json_decode($response->getContent());

            $logMessage = $log->method.' '.$log->url."\n".json_encode($log);

            if($response->isSuccessful())
                Log::channel('dg_requests')->info($logMessage);
            else
                Log::channel('dg_requests')->warning($logMessage);

            return $response;
        } catch (\Exception $e) {
            $log->exception = new stdClass();
            $log->exception->code = $e->getCode();
            $log->exception->message = $e->getMessage();
            $log->exception->trace = $e->getTrace();

            $logMessage = $log->method.' '.$log->url."\n".json_encode($log);
            Log::channel('dg_requests')->error($logMessage);
            throw $e;
        }
    }
}
