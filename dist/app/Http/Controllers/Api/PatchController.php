<?php

namespace App\Http\Controllers\Api;

use App\Enums\Launcher\Patchline;
use App\Http\Controllers\Controller;
use App\Models\GameFile;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatchController extends Controller
{
    public function getFileWithPatchline(string $patchlineName, string $hash) : BinaryFileResponse|JsonResponse
    {
        $patchline = Patchline::tryFromName(str($patchlineName)->upper());

        $gameFile = GameFile::select(['name'])->wherePatchline($patchline)->whereHash($hash)->latest()->first();
        $disk = Storage::disk('patches');

        if($gameFile === null)
            return response()->json('File not found', 404);

        $filePath = DIRECTORY_SEPARATOR . str($patchlineName)->lower() . DIRECTORY_SEPARATOR . $gameFile->name;

        if (!$disk->exists($filePath)) {
            return response()->json('File not found', 404);
        }

        return response()->download($disk->path($filePath));
    }

    function getFile(string $hash) : BinaryFileResponse|JsonResponse {
        return $this->getFileWithPatchline(Patchline::LIVE->name, $hash);
    }

    public function getGameFileList(string $patchlineName = null) : JsonResponse
    {
        $patchline = Patchline::tryFromName(str($patchlineName)->upper()) ?? Patchline::LIVE;

        if (!$patchline) {
            return response()->json(['error' => 'Invalid patchline'], 404);
        }

        $neededRole = $patchline->getNeededRole();

        if ($neededRole !== null && ( !Auth::check() || !Auth::user()->hasRole($neededRole->value))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $gameFiles = GameFile::select(['name', 'hash', 'game_path', 'action'])->where('patchline', $patchline->value)->latest()->get();

        if (count($gameFiles) <= 0)
            return response()->json(['error' => 'No files found'], 404);

        return response()->json($gameFiles, 200);
    }
}
