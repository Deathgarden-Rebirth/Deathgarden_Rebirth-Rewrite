<?php

namespace App\Http\Controllers\Api;

use App\Enums\Game\Faction;
use App\Enums\Game\Leaderboard\Window;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Leaderboard\GetScoresRequest;
use App\Http\Responses\Api\Leaderboard\GetScoresResponse;
use App\Http\Responses\Api\Leaderboard\LeaderboardEntry;
use App\Models\Admin\Archive\ArchivedPlayerProgression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function getScores(GetScoresRequest $request)
    {
        return match ($request->window) {
            Window::PersonalBest => static::handlePersonalBestWindow($request),
            Window::Lobby => static::handleLobbyWindow($request),
        };
    }

    public static function handlePersonalBestWindow(GetScoresRequest $request)
    {
        $response = new GetScoresResponse();

        $topScores = [];
        $startDate = static::getCurrentLeaderboardStartDate();

        static::getLeaderboardTopScores(5, $request->faction, $startDate)
            ->each(function (ArchivedPlayerProgression $item, int $rank) use (&$topScores) {
                $topScores[] = new LeaderboardEntry(
                    $item->user,
                    $item->gained_experience,
                    $rank + 1,
                );
            });

        foreach ($request->playerIds as $playerId) {
            $entries = static::getLeaderboardEntriesForPlayer($playerId, $request->faction, $startDate);
            $response->playerScores[$playerId] = [];

            $entries->each(function (ArchivedPlayerProgression $item) use (&$response, $playerId) {
                $response->playerScores[$playerId][] = new LeaderboardEntry(
                    $item->user,
                    $item->gained_experience,
                    $item->rank,
                );
            });
        }

        $response->topScores = $topScores;
        $response->leaderboardSize = static::getLeaderboardSize($request->faction, $startDate);
        $response->leaderboardReset = $startDate->addMonth()->firstOfMonth();

        return $response;
    }

    public static function handleLobbyWindow(GetScoresRequest $request)
    {
        $response = new GetScoresResponse();

        $startDate = static::getCurrentLeaderboardStartDate();

        static::getLeaderboardEntriesForPlayer($request->playerIds, $request->faction, $startDate)
            ->each(function (ArchivedPlayerProgression $item) use (&$response, &$lobbyScores) {
                $response->playerScores[$item->user_id][] = new LeaderboardEntry(
                    $item->user,
                    $item->gained_experience,
                    $item->rank,
                );
            });

        $response->leaderboardSize = static::getLeaderboardSize($request->faction, $startDate);
        $response->leaderboardReset = $startDate->addMonth()->firstOfMonth();

        return $response;
    }

    /**
     * @param string|array $userId Select the given user id. if its an array it only selects them and no one around.
     * @param Faction $faction
     * @param Carbon $startDate Player scores before this Time won't get selected.
     * @param ?Carbon $endDate scores after this time won't be selected, defaults to now when null.
     * @return Collection<int, ArchivedPlayerProgression>
     */
    public static function getLeaderboardEntriesForPlayer(string|array $userId, Faction $faction, Carbon $startDate, ?Carbon $endDate = null): Collection
    {
        $endDate ??= Carbon::now();

        $subSubQuery = static::getBaseSelectQuery($faction, $startDate, $endDate);

        $query = ArchivedPlayerProgression::query();
        $query->orderBy('gained_experience', 'desc');
        $query->select(['user_id', 'gained_experience'])
            ->addSelect(DB::raw('ROW_NUMBER() OVER (ORDER BY gained_experience DESC) AS "rank"'));
        $query->fromSub($subSubQuery, 'a');

        $mainQuery = ArchivedPlayerProgression::query();
        $mainQuery->fromSub($query, 'b');

        if (is_array($userId)) {
            $mainQuery->whereIn('user_id', $userId);
        } else {
            $mainQuery->where('rank', '<=', function (\Illuminate\Database\Query\Builder $whereQuery) use ($userId, $query) {
                $whereQuery->where('user_id', $userId);
                $whereQuery->fromSub($query, 'c');
                $whereQuery->selectRaw('(c.rank + 1)');

            });
        }
        $mainQuery->orderByDesc('rank');
        $mainQuery->limit(is_array($userId) ? count($userId) : 5);

        return $mainQuery->get();
    }

    /**
     * @param int $count
     * @param Faction $faction
     * @param Carbon $startDate Player scores before this Time won't get selected.
     * @param ?Carbon $endDate scores after this time won't be selected, defaults to now when null.
     * @return Collection<int, ArchivedPlayerProgression>
     */
    public static function getLeaderboardTopScores(int $count, Faction $faction, Carbon $startDate, ?Carbon $endDate = null): Collection
    {
        $endDate ??= Carbon::now();

        $query = static::getBaseSelectQuery($faction, $startDate, $endDate);
        $query->limit($count);

        return $query->get();
    }

    public static function getCurrentLeaderboardStartDate(): Carbon
    {
        return Carbon::today()->firstOfMonth()->addHours(21);
    }

    /**
     * @param Faction $faction
     * @param Carbon $startDate Player scores before this Time won't get selected.
     * @param ?Carbon $endDate scores after this time won't be selected, defaults to now when null.
     * @return int
     */
    public static function getLeaderboardSize(Faction $faction, Carbon $startDate, ?Carbon $endDate = null): int
    {
        $endDate ??= Carbon::now();

        $query = ArchivedPlayerProgression::query();
        $query->whereDate('created_at', '>=', $startDate);
        $query->whereDate('created_at', '<=', $endDate);
        $query->whereIn('played_character', $faction->getCharacterList());
        $query->distinct('user_id');
        $query->selectRaw('user_id');

        return $query->count('user_id');
    }

    protected static function getBaseSelectQuery(Faction $faction, Carbon $startDate, Carbon $endDate): Builder
    {
        $subSubQuery = ArchivedPlayerProgression::query();
        $subSubQuery->select(['user_id'])->addSelect(DB::raw('MAX(gained_experience) as gained_experience'));
        $subSubQuery->whereDate('created_at', '>=', $startDate);
        $subSubQuery->whereDate('created_at', '<=', $endDate);
        $subSubQuery->whereIn('played_character', $faction->getCharacterList());
        $subSubQuery->groupBy('user_id');
        $subSubQuery->orderByDesc('gained_experience');

        return $subSubQuery;
    }
}