<?php

// Moderation Routes

use App\Http\Controllers\Api\ModerationController;

Route::post('check/username', [ModerationController::class, 'checkUsername']);