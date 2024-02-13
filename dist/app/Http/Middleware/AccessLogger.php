<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

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

        Log::channel('db_requests')->info($log->method.' '.$log->url."\n".json_encode($log));

        return $next($request);
    }
}
