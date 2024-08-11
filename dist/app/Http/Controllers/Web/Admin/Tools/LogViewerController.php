<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogViewerController extends AdminToolController
{
    protected static string $name = 'Logs';

    protected static string $description = 'View Backend Logs';

    protected static string $iconComponent = 'icons.logs';

    protected static Permissions $neededPermission = Permissions::VIEW_LOG;
}
