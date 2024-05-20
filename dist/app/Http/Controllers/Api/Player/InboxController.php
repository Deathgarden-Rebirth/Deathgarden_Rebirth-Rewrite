<?php

namespace App\Http\Controllers\Api\Player;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function count(Request $request) {
        if (!Auth::check())
            abort(403, 'You are not logged in');

        $flag = $request->input('flag');

        //TODO: Add Model Retrieving logic

        return ['count' => 0];
    }
}
