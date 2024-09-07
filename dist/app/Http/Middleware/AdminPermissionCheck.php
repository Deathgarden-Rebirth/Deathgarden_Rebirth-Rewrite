<?php

namespace App\Http\Middleware;

use App\Attributes\IgnorePermissionCheck;
use App\Enums\Auth\Permissions;
use App\Http\Controllers\Web\Admin\Tools\AdminToolController;
use Auth;
use Closure;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\ClassString;
use ReflectionClass;
use Route;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionCheck
{
    /**
     * Check Permissions automatically if we are in a Admin Tool Controller.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ClassString<AdminToolController> $currentControllerClass */
        $currentControllerClass = Route::getCurrentRoute()->getControllerClass();

        if(!class_exists($currentControllerClass) || !is_subclass_of($currentControllerClass, AdminToolController::class))
            return $next($request);

        $currentMethod = Route::getCurrentRoute()->getActionMethod();
        $reflector = new ReflectionClass($currentControllerClass);

        // Skip permission check if the attribute exists on this function
        if(count($reflector->getMethod($currentMethod)->getAttributes(IgnorePermissionCheck::class)) > 0)
            return $next($request);

        if(!Auth::user()->can($currentControllerClass::getNeededPermission()))
            abort(403);

        return $next($request);
    }
}
