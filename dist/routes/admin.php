<?php

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\Tools\FileManagerController;
use App\Http\Controllers\Web\Admin\Tools\GameNewsController;
use App\Http\Controllers\Web\Admin\Tools\LogViewerController;
use App\Http\Controllers\Web\Admin\Tools\UsersController;
use App\Http\Controllers\Web\GameFileController;

Route::redirect('', 'admin/dashboard');
Route::redirect('logs', 'log-viewer')->name(LogViewerController::class);

Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

Route::get('file-manager', [GameFileController::class, 'index'])->name(FileManagerController::class);
Route::post('file-manager', [GameFileController::class, 'store'])->name('file.store');

Route::get('gamenews', [GameNewsController::class, 'index'])->name(GameNewsController::class);
Route::post('gamenews/create', [GameNewsController::class, 'create'])->name('gamenews.create');
Route::post('gamenews/{news}', [GameNewsController::class, 'submit'])->name('gamenews.post');

Route::get('users', [UsersController::class, 'index'])->name(UsersController::class);
Route::get('users/{user}', [UsersController::class, 'details'])->name('user.details');

Route::fallback(function () {
    return redirect(route('admin.dashboard'));
});
