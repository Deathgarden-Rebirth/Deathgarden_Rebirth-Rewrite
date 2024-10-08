<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Enums\Launcher\Patchline;
use App\Http\Controllers\Controller;
use App\Models\GameFile;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends AdminToolController
{
    protected static string $name = 'Pak Manager';

    protected static string $description = 'Deploy Pak Updates for the Launcher';
    protected static string $iconComponent = 'icons.file-manager';

    protected static Permissions $neededPermission = Permissions::FILE_UPLOAD;

    public function store(Request $request)
    {
        // Validate the incoming request with appropriate rules for file uploads
        $request->validate([
            'files.*' => 'required|file',
            'patchline' => 'required|integer',
            'gamepath.*' => 'required|string',
        ]);

        $duplicateFiles = [];
        $overwrittenFiles = [];
        $uploadedFiles = [];


        // Handle file upload logic
        if ($request->hasFile('files')) {
            for($i = 0; $i < count($request->file('files')); $i++) {
                $file = $request->file('files')[$i];

                $filename = $file->getClientOriginalName();
                $filehash = str(hash_file('sha256', $file->getRealPath()))->upper();

                $gameFile = GameFile::where('name', $filename)->where('patchline', $request->input('patchline'))->first() ?? new GameFile;

                if ($gameFile->hash == $filehash) {
                    $duplicateFiles[] = $gameFile->name;
                    continue;
                }

                if (isset($gameFile->id)) {
                    $overwrittenFiles[] = $gameFile->name;
                }

                $gameFile->name = $filename;
                $gameFile->hash = $filehash;
                $gameFile->patchline = $request->input('patchline');

                GameFile::getDisk()->putFileAs(strtolower($gameFile->patchline->name), $file, $gameFile->name);
                $uploadedFiles[] = $gameFile->name;

                $gameFile->game_path = $request->game_path[$i];
                $gameFile->action = $request->file_action[$i];
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

    public function index(Request $request) : View {
        $patchline = Patchline::tryFrom($request->input('patchline')) ?? Patchline::LIVE;

        $files = GameFile::latest()->where('patchline', $patchline)->get();

        return view('admin.tools.file-manager', [
            'patchline' => $patchline,
            'files' => $files,
        ]);
    }

    public function update(GameFile $file_manager) : RedirectResponse {
        $file_manager->action = (int)!$file_manager->action->value;

        if($file_manager->save()) {
            Session::flash('alert-success', 'File ' . $file_manager->name . ' was successfully marked for ' . ($file_manager->action->value ? 'add' : 'delete'));
        } else {
            Session::flash('alert-error', 'Failed to mark file ' . $file_manager->name . ' for ' . ($file_manager->action->value ? 'add' : 'delete'));
        }

        return redirect()->back();
    }

    public function destroy(GameFile $file_manager) : RedirectResponse {
        $file_manager->delete();

        Session::flash('alert-success', 'File ' . $file_manager->name . ' was successfully deleted');

        return redirect()->back();
    }
}
