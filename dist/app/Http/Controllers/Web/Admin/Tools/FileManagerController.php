<?php

namespace App\Http\Controllers\Web\Admin\Tools;

use App\Enums\Auth\Permissions;
use App\Enums\Launcher\Patchline;
use App\Models\GameFile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
            'is_additional' => 'required|integer',
        ]);

        $duplicateFiles = [];
        $overwrittenFiles = [];
        $uploadedFiles = [];


        // Handle file upload logic
        if (!$request->hasFile('files')) {
            Session::flash('alert-error', 'Failed to upload files. No files were provided.');
            return redirect()->back();
        }

        for ($i = 0; $i < count($request->file('files')); $i++) {
            $file = $request->file('files')[$i];

            $filename = $file->getClientOriginalName();
            $filehash = str(hash_file('sha256', $file->getRealPath()))->upper();

            $gameFile = GameFile::where('filename', $filename)->where('patchline', $request->input('patchline'))->latest()->first() ?? new GameFile;

            if ($gameFile->hash == $filehash) {
                $duplicateFiles[] = $gameFile->filename;
                continue;
            }

            if (isset($gameFile->id)) {
                $gameFile->action = 0;
                $gameFile->save();
                
                $gameFile->child_id = $gameFile->id;
                $gameFile = $gameFile->replicate();
                $overwrittenFiles[] = $gameFile->filename;
            }

            $gameFile->filename = $filename;
            $gameFile->hash = $filehash;
            $gameFile->patchline = $request->input('patchline');

            if (filled($request->input('game_mod_name'))) {
                $gameFile->name = $request->input('game_mod_name')[$i];
            }

            if (filled($request->input('game_mod_description'))) {
                $gameFile->description = $request->input('game_mod_description')[$i];
            }
            
            $gameFile->is_additional = $request->input('is_additional');

            GameFile::getDisk()->putFileAs(strtolower($gameFile->patchline->name), $file, $gameFile->filename);
            $uploadedFiles[] = $gameFile->filename;


            $gameFile->game_path = $request->game_path[$i];
            $gameFile->action = $request->file_action[$i];
            $gameFile->save();
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
            Session::flash('alert-warning', 'The following files with the same hash already exist: <br>' . implode('<br>', $duplicateFiles));
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

        $allFiles = GameFile::where('patchline', $patchline)
                            ->where('is_additional', (bool)$request->input('additional_files'))
                            ->withFileHistory();


        return view('admin.tools.file-manager', [
            'patchline' => $patchline,
            'showAdditionalFiles' => (bool)$request->input('additional_files'),
            'files' => $allFiles,
        ]);
    }

    public function update(GameFile $file_manager) : RedirectResponse {
        $file_manager->action = (int)!$file_manager->action->value;

        if($file_manager->save()) {
            Session::flash('alert-success', 'File ' . $file_manager->filename . ' was successfully marked for ' . ($file_manager->action->value ? 'add' : 'delete'));
        } else {
            Session::flash('alert-error', 'Failed to mark file ' . $file_manager->filename . ' for ' . ($file_manager->action->value ? 'add' : 'delete'));
        }

        return redirect()->back();
    }

    public function destroy(GameFile $file_manager) : RedirectResponse {
        $file_manager->delete();

        Session::flash('alert-success', 'File ' . $file_manager->filename . ' was successfully deleted');

        return redirect()->back();
    }
}
