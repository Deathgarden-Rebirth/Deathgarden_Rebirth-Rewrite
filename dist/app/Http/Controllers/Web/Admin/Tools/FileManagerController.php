<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerController extends AdminToolController
{
    protected static string $name = 'File Manager';

    protected static string $description = 'Add and edit game files the launcher installs.';
    protected static string $iconComponent = 'icons.file-manager';

    protected static Permissions $neededPermission = Permissions::FILE_UPLOAD;

    public function index()
    {
        return view('admin.tools.game-news');
    }
}
