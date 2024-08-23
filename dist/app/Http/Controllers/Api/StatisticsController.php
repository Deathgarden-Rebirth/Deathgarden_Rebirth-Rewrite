<?php

namespace App\Http\Controllers\Api;

use App\Console\Commands\CacheOnlinePlayerStats;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function getOnlinePlayers() {
        return \Cache::get(CacheOnlinePlayerStats::CACHE_KEY);
    }
}
