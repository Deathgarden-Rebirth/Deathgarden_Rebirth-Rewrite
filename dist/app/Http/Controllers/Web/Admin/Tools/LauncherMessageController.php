<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Requests\Api\Admin\Tools\SaveLauncherMessageRequest;
use App\Models\Admin\LauncherMessage;
use Session;

class LauncherMessageController extends AdminToolController
{
    protected static string $name = 'Launcher Message';

    protected static string $description = 'Set the Launcher message';
    protected static string $iconComponent = 'icons.patch-exclamation';

    protected static Permissions $neededPermission = Permissions::FILE_UPLOAD;

    public function index()
    {
        return view('admin.tools.launcher-message', [
            'message' => LauncherMessage::getMessage(),
        ]);
    }

    public function saveMessage(SaveLauncherMessageRequest $request) {
        $newMessage = new LauncherMessage(
            $request->message,
            $request->url
        );

        $success = $newMessage->saveMessage();

        if($success === true)
            Session::flash('alert-success', 'Message Saved');
        else
            Session::flash('alert-error', 'Message Saving failed: ' . $success);

        return back();
    }
}