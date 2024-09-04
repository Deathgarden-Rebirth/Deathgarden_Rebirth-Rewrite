<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Requests\Api\Admin\Tools\SaveLauncherMessageRequest;
use App\Http\Requests\Api\Admin\Tools\SaveVersioningRequest;
use App\Models\Admin\LauncherMessage;
use App\Models\Admin\Versioning\CurrentCatalogVersion;
use App\Models\Admin\Versioning\CurrentContentVersion;
use App\Models\Admin\Versioning\CurrentGameVersion;
use App\Models\Admin\Versioning\LauncherVersion;
use Session;

class VersioningController extends AdminToolController
{
    protected static string $name = 'Versioning';

    protected static string $description = 'Manage the Launcher & Game Versions.';
    protected static string $iconComponent = 'icons.arrow-left-right';

    protected static Permissions $neededPermission = Permissions::FILE_UPLOAD;

    public function index()
    {
        return view('admin.tools.versioning', [
            'message' => LauncherMessage::getMessage(),
        ]);
    }

    public function save(SaveVersioningRequest $request) {
        $errorMessage = '';
        $successMessage= '';

        $success = (new CurrentGameVersion($request->gameVersion))->save();
        if($success === true)
            $successMessage .= "Game version saved\n\n";
        else
            $errorMessage .= "Game version saving failed: ' . $success\n\n";

        $success = (new CurrentContentVersion($request->contentVersion))->save();
        if($success === true)
            $successMessage .= "Content version saved\n\n";
        else
            $errorMessage .= "Content version saving failed: ' . $success\n\n";

        (new CurrentCatalogVersion($request->catalogVersion))->save();
        if($success === true)
            $successMessage .= "Catalog version saved\n\n";
        else
            $errorMessage .= "Catalog version saving failed: ' . $success\n\n";

        (new LauncherVersion($request->launcherVersion))->save();
        if($success === true)
            $successMessage .= "Launcher version saved\n\n";
        else
            $errorMessage .= "Launcher version saving failed: ' . $success\n\n";

        if($success !== '')
            Session::flash('alert-success', nl2br(trim($successMessage)));
        if($errorMessage !== '')
            Session::flash('alert-error', nl2br(trim($errorMessage)));

        return back();
    }
}