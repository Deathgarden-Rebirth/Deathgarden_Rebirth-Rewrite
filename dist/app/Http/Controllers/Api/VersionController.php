<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\TexResponse;
use App\Models\Admin\Versioning\CurrentCatalogVersion;
use App\Models\Admin\Versioning\CurrentContentVersion;
use App\Models\Admin\Versioning\CurrentGameVersion;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class VersionController extends Controller
{
    const ROUTE_HEALTHCHECK = 'healthcheck';

    const ROUTE_TEX = 'tex';

    const ROUTE_LATEST_CLIENT_DATA = 'latest-client-data';

    const ROUTE_LATEST_CONTENT_VERSION = 'latest-content-version';

    public function healthcheck()
    {
        return json_encode(['Health' => 'Alive']);
    }

    public function tex(\Illuminate\Http\Request $request)
    {
        $userAgent = $request->userAgent();

        return json_encode(new TexResponse());

    }

    public function getLatestClientData()
    {
        return json_encode(['LatestSupportedVersion' => 'te-18f25613-36778-ue4-374f864b']);
    }

    public function getLatestContentVersion(string $version)
    {
        $currentVersion = CurrentContentVersion::get()?->contentVersion;
        if($currentVersion === $version)
            return ['LatestSupportedVersion' => CurrentCatalogVersion::get()?->catalogVersion];

        abort(404);
    }
}