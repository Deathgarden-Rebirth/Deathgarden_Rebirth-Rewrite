<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Requests\Api\Admin\Tools\HandleModerationRequest;
use App\Models\Admin\BadChatMessage;
use Redirect;
use Session;

class ChatMessageController extends AdminToolController
{
    protected static string $name = 'Chat Profanity Alarm';

    protected static string $description = 'View Triggered Chat Messages';

    protected static string $iconComponent = 'icons.chat';

    protected static Permissions $neededPermission = Permissions::CHAT_REPORTS;

    public function index()
    {
        $badMessages = BadChatMessage::orderBy('handled')
            ->paginate();

        return view('admin.tools.chat-messages', ['messages' => $badMessages]);
    }

    public function handleMessage(HandleModerationRequest $request, BadChatMessage $message)
    {
        $message->consequences = $request->consequences;
        $message->handled = true;
        $message->handledBy()->associate(\Auth::user());
        $message->save();

        Session::flash('alert-success', 'Handled message saved successfully.');
        return Redirect::back();
    }

    public static function getNotificationText(): ?string
    {
        $unhandledCount = BadChatMessage::where('handled', '=', false)->count();

        if($unhandledCount > 0)
            return 'There are Unhandled Chat Messages';
        return null;
    }
}