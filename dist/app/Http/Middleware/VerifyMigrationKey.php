<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class VerifyMigrationKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->input('migration_key'); // Assuming the key is passed as a GET parameter
        
        if (!$key) {
            return response()->json(['error' => 'Migration key is missing'], 401);
        }
        
        $storedKey = Storage::get('migration_key.txt'); // Retrieve the stored key from the file
        
        if ($key !== $storedKey) {
            return response()->json(['error' => 'Invalid migration key'], 401);
        }

        Storage::delete('migration_key.txt');

        return $next($request);
    }
}
