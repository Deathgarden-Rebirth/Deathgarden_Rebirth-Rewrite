<?php

use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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


Route::get('/auth/redirect', function () {
    return Socialite::driver('steam')->redirect();
})->name(LoginController::ROUTE_LOGIN);
Route::get('/auth/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    return redirect('/');
});

Route::get('/auth/callback', [LoginController::class, 'callback'])->name(LoginController::ROUTE_CALLBACK);

Route::get('{catalogVersion}/catalog', [\App\Http\Controllers\Api\Catalog\CatalogController::class, 'getCatalog']);

Route::get('/', function () {
    return \Inertia\Inertia::render('Dashboard');
});
