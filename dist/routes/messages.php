<?php

use App\Http\Controllers\Api\Messages\NewsController;

Route::get('gamenews/messages', [NewsController::class, 'getGameNews']);