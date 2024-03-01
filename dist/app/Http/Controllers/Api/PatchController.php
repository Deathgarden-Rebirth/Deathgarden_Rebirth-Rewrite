<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatchController extends Controller
{
    public function getCurrentPatch()
    {
        $disk = Storage::disk('patches');
        $patchFiles = $disk->allFiles('paks');

        if (count($patchFiles) <= 0)
            return response('No Patches Found', 404);

        $filePath = $disk->path($patchFiles[0]);

        return response()->download($filePath);
    }

    public function getSignature()
    {
        $disk = Storage::disk('patches');
        if(!$disk->exists('TheExit.sig'))
            return response('No Patches Found', 404);

        return response()->download($disk->path('TheExit.sig'));
    }

    public function getBattleyePatch()
    {
        $disk = Storage::disk('patches');
        if(!$disk->exists('bottleEye/BEClient_x64.dll'))
            return response('No Patch Found', 404);

        return response()->download($disk->path('bottleEye/BEClient_x64.dll'));
    }

    public function getGameFileList() : JsonResponse
    {
        return response()->json(GameFile::select(['name', 'hash', 'game_path'])->latest()->get(), 200);
    }
}
