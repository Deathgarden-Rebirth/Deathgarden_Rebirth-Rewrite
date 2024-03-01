<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\GameFile;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class GameFileController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->can(\App\Enums\Auth\Permissions::FILE_UPLOAD->value)) {
            throw new AuthorizationException();
        }
        
        // Validate the incoming request with appropriate rules for file uploads
        $request->validate([
            'files.*' => 'required|file',
            'gamepath.*' => 'required|string',
        ]);

        //dd($request);

        $duplicateFiles = [];
        $overwrittenFiles = [];
        $uploadedFiles = [];

        // Handle file upload logic
        if ($request->hasFile('files')) {
            for($i = 0; $i < count($request->file('files')); $i++) {
                $file = $request->file('files')[$i];

                $filename = $file->getClientOriginalName();
                $filehash = str(hash_file('sha256', $file->getRealPath()))->upper();

                $gameFile = GameFile::where('name', $filename)->first() ?? new GameFile;

                if ($gameFile->hash == $filehash) {
                    $duplicateFiles[] = $gameFile->name;
                    continue;
                }

                if (isset($gameFile->id)) {
                    $overwrittenFiles[] = $gameFile->name;
                }

                $gameFile->name = $filename;
                $gameFile->hash = $filehash;
                
                $file->storeAs('', $gameFile->name, ['disk' => 'dg_public']);
                $uploadedFiles[] = $gameFile->name;
                $gameFile->game_path = $request->game_path[$i];
                $gameFile->save();
            }
        }

        if (count($uploadedFiles) === 0) {
            Session::flash('alert-error', 'No files were uploaded');
        }

        // Optionally, you can return a response
        if (count($overwrittenFiles) > 0) {
            Session::flash('alert-warning', 'Files uploaded successfully. The following files was overwritten: <br>' . implode('<br>', $overwrittenFiles));
            //return response()->json(['message' => 'Only some files was uploaded successfully. The following files already exist: ' . implode(', ', $overwrittenFiles)]);
        }

        if (count($duplicateFiles) > 0) {
            Session::flash('alert-warning', 'The following files already exist: <br>' . implode('<br>', $duplicateFiles));
            //return response()->json(['message' => 'Only some files was uploaded successfully. The following files already exist: ' . implode(', ', $duplicateFiles)]);
        }

        if (count($uploadedFiles) > 0) {
            Session::flash('alert-success', 'Files uploaded successfully <br>' . implode('<br>', $uploadedFiles));
            //return response()->json(['message' => 'Files uploaded successfully']);
        }

        return redirect()->back();
        //return response()->json(['message' => 'Files uploaded successfully']);
    }

    public function index() : View {
        if (!auth()->user()->can(\App\Enums\Auth\Permissions::FILE_UPLOAD->value)) {
            throw new AuthorizationException();
        }

        return view('file-manager', ['files' => GameFile::latest()->get()]);
    }
}
