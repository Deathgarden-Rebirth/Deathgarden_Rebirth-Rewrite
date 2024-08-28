<?php

namespace App\Http\Controllers\Api;

use App\Enums\Auth\Roles;
use App\Enums\Launcher\Patchline;
use App\Http\Controllers\Controller;
use App\Models\GameFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ReflectionEnumBackedCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    public function getFileWithPatchline(string $patchline_name, string $hash) : BinaryFileResponse
    {
        $patchline = Patchline::tryFromName(str($patchline_name)->upper());

        $gameFile = GameFile::select(['name'])->where('patchline', $patchline->value)->where('hash', $hash)->latest()->first();
        $disk = Storage::disk('patches');

        $filePath = DIRECTORY_SEPARATOR . str($patchline_name)->lower() . DIRECTORY_SEPARATOR . $gameFile->name;

        if (empty($gameFile) || !$disk->exists($filePath)) {
            return response()->json('File not found', 404);
        }

        return response()->download($disk->path($filePath));
    }

    function getFile(string $hash) : BinaryFileResponse {
        return $this->getFileWithPatchline('live', $hash);
    }

    public function getGameFileList(string $patchline_name = null) : JsonResponse
    {
        $patchline = Patchline::tryFromName(str($patchline_name)->upper());
        
        if (empty($patchline_name)) {
            $patchline = Patchline::LIVE;
        }


        if (!$patchline) {
            return response()->json(['error' => 'Invalid patchline'], 404);
        }

        //Hardcoded patchline permissions, can be easily expanded/changed in the future
        switch ($patchline) {
            case Patchline::DEV->name:
                if (!auth()->user()?->hasRole([Roles::ADMIN])) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                break;
            case Patchline::PLAYTESTER->name:
                if (!auth()->user()?->hasRole([Roles::ADMIN])) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                break;
        }

        $gameFiles = GameFile::select(['name', 'hash', 'game_path', 'action'])->where('patchline', $patchline->value)->latest()->get();

        if (count($gameFiles) <= 0)
            return response()->json(['error' => 'No files found'], 404);

        return response()->json($gameFiles, 200);
    }
}
