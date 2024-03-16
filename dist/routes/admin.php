<?php

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\GameFileController;

Route::redirect('', 'admin/dashboard');
Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

Route::get('file-manager', [GameFileController::class, 'index']);
Route::post('file-manager', [GameFileController::class, 'store'])->name('file.store');

Route::fallback(function () {
    return redirect(route('admin.dashboard'));
});
