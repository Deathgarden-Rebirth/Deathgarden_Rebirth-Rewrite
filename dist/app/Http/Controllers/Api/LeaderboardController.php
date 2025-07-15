<?php

namespace App\Http\Controllers\Api;

use App\Enums\Game\Faction;
use App\Enums\Game\Hunter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Leaderboard\GetScoresRequest;
use App\Models\Admin\Archive\ArchivedPlayerProgression;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class LeaderboardController extends Controller
{
    const HUNTER_CHARACTER_LIST = [
        Hunter::Inquisitor,
        Hunter::Stalker,
        Hunter::Poacher,
        Hunter::Mass
    ];

    public function getScores(GetScoresRequest $request)
    {
        $user = \Auth::user();

        $dateFilter = Carbon::now()->startOfMonth();

        dump($request->playerIds);

        $subSubQuery = ArchivedPlayerProgression::query();
        $subSubQuery->select(['user_id'])->addSelect(DB::raw('MAX(gained_experience) as gained_experience'));
        $subSubQuery->whereDate('created_at', '>=', $dateFilter);
        $subSubQuery->groupBy('user_id');
        $subSubQuery->orderByDesc('gained_experience');

        $query = ArchivedPlayerProgression::query();
        $query->orderBy('gained_experience', 'desc');
        $query->select(['user_id', 'gained_experience'])
            ->addSelect(DB::raw('ROW_NUMBER() OVER (ORDER BY gained_experience DESC) AS "rank"'));
        $query->fromSub($subSubQuery, 'a');

        $whereQuery = DB::query();
        $whereQuery->where('user_id', $request->playerIds[0]);
        $whereQuery->fromSub($query, 'c');
        $whereQuery->selectRaw('c.rank + 1 AS "user_rank"');

        $mainQuery = ArchivedPlayerProgression::query();
        $mainQuery->fromSub($query, 'b');
        $mainQuery->where('rank', '<=', function (Builder $whereQuery) use ($request, $query) {
            $whereQuery->where('user_id', $request->playerIds[0]);
            $whereQuery->fromSub($query, 'c');
            $whereQuery->selectRaw('(c.rank + 1)');
        });
        $mainQuery->orderByDesc('rank');
        $mainQuery->limit(5);

        dd($mainQuery->toRawSql());



        $username = $user->last_known_username . '_' . Str::substr($user->id, 0, 8);
        return <<<JSON
{
  "topScores": [
    {
      "id": "35c58208-2853-4ec5-9a72-be9aef984df0",
      "score": 35265,
      "rank": 1,
      "playerName": "Clonebones_35c58208"
    },
    {
      "id": "20c8047d-838d-4838-b8fb-64b9ed8253fe",
      "score": 23355,
      "rank": 2,
      "playerName": "Cookiemonster_20c8047d"
    },
    {
      "id": "0609e627-610e-42a3-8111-4ebc5a44121a",
      "score": 21070,
      "rank": 3,
      "playerName": "Dash_0609e627"
    },
    {
      "id": "30e5b06e-6b2c-464a-a990-d5de469fb5df",
      "score": 18575,
      "rank": 4,
      "playerName": "Switch_30e5b06e"
    },
    {
      "id": "08be1a3f-e4c0-4e4f-b2c3-c19ead033da0",
      "score": 16730,
      "rank": 5,
      "playerName": "Bob_08be1a3f"
    }
  ],
  "playerScores": {
    "$user->id": [
      {
        "id": "13d86f42-8446-49b5-a0c4-077ba23a43e4",
        "score": 12130,
        "rank": 40,
        "playerName": "Clonebones_13d86f42"
      },
      {
        "id": "1f58fbae-23b2-481f-b679-0f1f7ab4f31a",
        "score": 12120,
        "rank": 41,
        "playerName": "Inked_1f58fbae"
      },
      {
        "id": "cdd7bea9-f20a-4cc0-ae82-3297614933b7",
        "score": 12085,
        "rank": 42,
        "playerName": "Fog_cdd7bea9"
      },
      {
        "id": "7c6c1e40-4e6c-47a7-a2a3-480f8b774994",
        "score": 12085,
        "rank": 43,
        "playerName": "Dash_7c6c1e40"
      },
      {
        "id": "$user->id",
        "score": 12015,
        "rank": 44,
        "playerName": "$username"
      },
      {
        "id": "0e38856d-97a5-4222-b546-f3f9360102b4",
        "score": 12010,
        "rank": 45,
        "playerName": "Ghost_0e38856d"
      }
    ]
  },
  "leaderboardSize": 8497,
  "leaderboardReset": 1753210800
}
JSON;

    }
}