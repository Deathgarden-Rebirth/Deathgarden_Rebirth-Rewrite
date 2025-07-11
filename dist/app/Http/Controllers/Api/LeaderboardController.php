<?php

namespace App\Http\Controllers\Api;

use App\Enums\Game\Faction;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class LeaderboardController extends Controller
{
    public function getScores()
    {
        return json_encode([
            'leaderboardReset' => Carbon::now()->addDay()->getTimestamp(),
            'leaderboardSize' => 5,
            'playerScores' => [
                '9caa08f6-9360-4bbe-9158-9a07f46fe64b' => [
                    [
                        'id' => '9caa08f6-9360-4bbe-9158-9a07f46fe64b',
                        'score' => 10000,
                        'rank' => 1,
                        'playerName' => 'Vari'
                    ],
                    [
                        'id' => '9ca98d64-dcd0-4193-81b5-6cfe94bce727',
                        'score' => 9999,
                        'rank' => 2,
                        'playerName' => 'Miraak'
                    ],
                    [
                        'id' => '9ce44319-a66c-45b0-8c5b-1ba3ff1710f8',
                        'score' => 9998,
                        'rank' => 3,
                        'playerName' => 'Medkit'
                    ],
                    [
                        'id' => 'e5f4a267-189b-4b63-b665-5c20610cd3a4',
                        'score' => 5000,
                        'rank' => 4,
                        'playerName' => 'Schmock 1'
                    ],
                    [
                        'id' => '320cfa83-2f48-404a-a0b9-14d616c98be5',
                        'score' => 4999,
                        'rank' => 5,
                        'playerName' => 'Schmock 2'
                    ],
                ]
            ],
            'topScores' => [
                [
                    'id' => '9caa08f6-9360-4bbe-9158-9a07f46fe64b',
                    'score' => 10000,
                    'rank' => 1,
                    'playerName' => 'Vari'
                ],
                [
                    'id' => '9ca98d64-dcd0-4193-81b5-6cfe94bce727',
                    'score' => 9999,
                    'rank' => 2,
                    'playerName' => 'Miraak'
                ],
                [
                    'id' => '9ce44319-a66c-45b0-8c5b-1ba3ff1710f8',
                    'score' => 9998,
                    'rank' => 3,
                    'playerName' => 'Medkit'
                ],
                [
                    'id' => 'e5f4a267-189b-4b63-b665-5c20610cd3a4',
                    'score' => 5000,
                    'rank' => 4,
                    'playerName' => 'Schmock 1'
                ],
                [
                    'id' => '320cfa83-2f48-404a-a0b9-14d616c98be5',
                    'score' => 4999,
                    'rank' => 5,
                    'playerName' => 'Schmock 2'
                ],
            ]
        ], JSON_FORCE_OBJECT);
    }
}
