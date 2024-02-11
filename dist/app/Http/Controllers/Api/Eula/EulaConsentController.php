<?php

namespace App\Http\Controllers\Api\Eula;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EulaConsentController extends Controller
{
    public function get()
    {
        $user = Auth::user();
        if($user->eula_consent)
            return true;
        return false;
    }

    public function put()
    {
        $user = Auth::user();
        dd($user);
    }
}
