<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

abstract class AdminToolController extends Controller
{
    protected static string $name;

    protected static string $description;

    protected static string $iconComponent;

    protected static Permissions $neededPermission;

    public function __construct()
    {
        $this->overrideTitle(static::$name);
    }

    /**
     * @return AdminToolController[]|string[]
     */
    final public static function getAllTools(): array
    {
        return [
            GameNewsController::class,
            FileManagerController::class,
            LogViewerController::class,
            UsersController::class,
            InboxMailerController::class,
        ];
    }

    final public static function getName(): string
    {
        return static::$name;
    }

    final public static function getDescription(): string
    {
        return static::$description;
    }

    final public static function getIconComponent(): string
    {
        return static::$iconComponent;
    }

    final public static function getNeededPermission(): string
    {
        return static::$neededPermission->value;
    }

    final protected function overrideTitle(string $title): void {
        View::share('title', $title);
    }
}
