<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VersionController extends Controller
{
    public function healthcheck()
    {
        return json_encode(['Health' => 'Alive']);
    }

    public function getLatestClientData()
    {
        return json_encode(['LatestSupportedVersion' => 'te-18f25613-36778-ue4-374f864b']);
    }

    public function getLatestContentVersion(string $version)
    {
        $supportedVersion = match($version) {
            '0' => 'dev030',
            '2.0' => 'dev020',
            '2.2' => 'te-23ebf96c-27498-ue4-7172a3f5',
            '2.5' => 'te-40131b9e-33193-ue4-fbccc218',
            '3.0' => 'dev030',
            default => 'te-18f25613-36778-ue4-374f864b'
        };

        return ['LatestSupportedVersion' => $supportedVersion];
    }
}