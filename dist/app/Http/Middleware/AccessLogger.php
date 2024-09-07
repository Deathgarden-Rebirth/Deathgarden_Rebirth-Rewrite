<?php

namespace App\Http\Middleware;

use App\Models\User\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class AccessLogger
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        if (!Str::contains($request->userAgent(), 'TheExit'))
            return $next($request);

        if(config('database.enable-query-logging'))
            DB::enableQueryLog();

        $response = $next($request);

        $log = new stdClass();
        $log->startTime = $startTime;
        $log->method = $request->method();
        $log->url = $request->fullUrl();
        $log->headers = $request->headers->all();
        $log->queryParams = $request->getQueryString();
        $log->body = $request->all();

        $channels = ['dg_requests'];

        $logChannel = static::getSessionLogConfig();

        $channels[] = $logChannel;

        $log->response = new stdClass();
        $log->response->statusCode = $response->getStatusCode();
        $log->response->body = json_decode($response->getContent());
        $log->queries = DB::getQueryLog();

        $logMessage = $log->method . ' ' . $log->url . "\n" . json_encode($log);

        if ($response->isSuccessful())
            Log::stack($channels)->info($logMessage);
        else
            Log::stack($channels)->warning($logMessage);

        return $response;
    }

    public static function getSessionLogConfig(): LoggerInterface
    {
        $user = Auth::user();
        if (!Session::has('sessionLogConfig')) {
            $logConfig = [
                'driver' => 'single',
                'path' => static::getSessionLogPath($user),
            ];
            Session::put('sessionLogConfig', $logConfig);
        } else
            $logConfig = Session::get('sessionLogConfig');

        if($user !== null && str_starts_with(basename($logConfig['path']), '__UNKNOWN')) {
            $oldPath = $logConfig['path'];
            $newPath = static::getSessionLogPath($user);

            if(!file_exists(dirname($newPath)))
                mkdir(dirname($newPath), 0777, true);

            rename($oldPath, $newPath);
            $logConfig['path'] = $newPath;
            Session::put('sessionLogConfig', $logConfig);
        }

        return Log::build($logConfig);
    }

    public static function getSessionLogPath(?User $user): string {
        $username = $user?->last_known_username ?? '__UNKNOWN';
        static::cleanupUsername($username);
        $userid = $user?->id ?? 'no-id';
        $session = Str::substr(Session::getId(), 0, 12);

        return storage_path("logs/sessions/{$username}_$userid/{$username}_$session.log");
    }

    public static function cleanupUsername(string &$username): void
    {
        $username = preg_replace('/[^\w\-\.]/', '', $username);
    }
}
