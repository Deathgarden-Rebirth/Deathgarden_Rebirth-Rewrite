<?php

use App\Http\Controllers\Api\Catalog\CatalogController;
use App\Http\Controllers\Api\Matchmaking\MatchmakingController;
use App\Http\Controllers\Web\Homepage\HomepageController;
use App\Http\Controllers\Api\PatchController;
use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/auth/redirect', [LoginController::class, 'redirect'])->name(LoginController::ROUTE_LOGIN);

Route::get('/auth/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect('/');
})->name('logout');

Route::get('/auth/callback', [LoginController::class, 'callback'])->name(LoginController::ROUTE_CALLBACK);

Route::prefix('patch')->group(function () {
    Route::get('files/{hash}', [PatchController::class, 'getFile'])->name('patch.file');
    Route::get('{patchlineName}/files/{hash}', [PatchController::class, 'getFileWithPatchline']);
});

Route::get('{catalogVersion}/catalog', [CatalogController::class, 'getCatalog']);

Route::get('download', [HomepageController::class, 'download'])->name('download');
Route::get('download-launcher', [HomepageController::class, 'downloadLauncher'])->name('download.launcher');
Route::get('patch-notes', [HomepageController::class, 'patchNotes'])->name('patch-notes');
Route::get('how-to-play', [HomepageController::class, 'howToPlay'])->name('how-to-play');
Route::get('known-issues', [HomepageController::class, 'knownIssues'])->name('known-issues');
Route::get('eula', [HomepageController::class, 'eula'])->name('eula');
Route::get('credits', [HomepageController::class, 'credits'])->name('credits');
Route::get('/', [HomepageController::class, 'index'])->name('homepage');

Route::middleware('verify_migration_key')->get('/migrate-database', function () {
    Artisan::call('migrate --no-interaction');
    print Artisan::output();
    Artisan::call('db:seed');
    print Artisan::output();
    Artisan::call('optimize:clear');
    print Artisan::output();
    Artisan::call('vendor:publish --tag=log-viewer-assets --force');
    print Artisan::output();
});


Route::post('file/{gameVersion}/{seed}/{mapName}', [MatchmakingController::class, 'seedFilePost']);
Route::get('file/{gameVersion}/{seed}/{mapName}', [MatchmakingController::class, 'seedFileGet']);
