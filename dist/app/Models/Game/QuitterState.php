<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @mixin IdeHelperQuitterState
 */
class QuitterState extends Model
{
    use HasFactory;

    protected $attributes = [
        'stay_match_streak' => 0,
        'stay_match_streak_previous' => 0,
        'quits' => 0,
        'quit_match_streak' => 0,
        'quit_match_streak_previous' => 0,
        'strikes_left' => 1,
        'has_quit_once' => false,
    ];
}
