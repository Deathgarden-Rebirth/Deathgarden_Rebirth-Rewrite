<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Http\Requests\Api\Admin\Tools\HandleModerationRequest;
use App\Models\Game\Moderation\PlayerReport;
use Auth;
use Redirect;
use Session;

class PlayerReportsController extends AdminToolController
{
    protected static string $name = 'Reports';
    protected static string $description = 'See player reports';
    protected static string $iconComponent = 'icons.flag';
    protected static Permissions $neededPermission = Permissions::VIEW_USERS;

    public function index()
    {
        $reports = PlayerReport::orderBy('handled')->paginate();

        return view('admin.tools.user-reports', ['reports' => $reports]);
    }

    public function handleReport(HandleModerationRequest $request, PlayerReport $report) {
        $report->consequences = $request->consequences;
        $report->handled = true;
        $report->handledBy()->associate(Auth::user());
        $report->save();

        Session::flash('alert-success', 'Handled report saved successfully.');
        return Redirect::back();
    }

    public static function getNotificationText(): ?string
    {
        $unhandledCount = PlayerReport::where('handled', '=', false)->count();

        if($unhandledCount > 0)
            return 'There are Unhandled Chat Messages';
        return null;
    }
}