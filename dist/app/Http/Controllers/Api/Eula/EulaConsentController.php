<?php

namespace App\Http\Controllers\Api\Eula;

use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Eula\EulaConsentResponse;
use Illuminate\Support\Facades\Auth;

class EulaConsentController extends Controller
{
    public function get(\Request $request)
    {
        $user = Auth::user();
        $response = new EulaConsentResponse($user->id, $user->eula_consent);

        return json_encode($response);
    }

    public function put()
    {
        $user = Auth::user();
        $user->eula_consent = true;
        $user->save();

        return json_encode(new EulaConsentResponse($user->id, true));
    }
}
