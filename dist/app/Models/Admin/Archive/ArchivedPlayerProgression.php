<?php

namespace App\Models\Admin\Archive;

use App\Enums\Game\Characters;
use App\Enums\Game\CharacterState;
use App\Models\Game\Matchmaking\Game;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperArchivedPlayerProgression
 */
class ArchivedPlayerProgression extends Model
{
    protected $casts = [
        'played_character' => Characters::class,
        'character_state' => CharacterState::class,
        'experience_events' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function archivedGame(): BelongsTo {
        return $this->belongsTo(ArchivedGame::class);
    }

    public static function archivePlayerProgression(
        Game $game,
        User &$user,
        bool $hasQuit,
        Characters $playedCharacter,
        CharacterState $characterState,
        int $gainedExperience,
        array $experienceEvents,
        int $gainedCurrencyA,
        int $gainedCurrencyB,
        int $gainedCurrencyC,
    ): void {
        if(ArchivedPlayerProgression::archivedPlayerExists($game->id, $user->id))
            return;

        $archived = new ArchivedPlayerProgression();

        $archived->user_id = $user->id;
        $archived->archived_game_id = $game->id;
        $archived->played_character = $playedCharacter;
        $archived->character_state = $characterState;
        $archived->has_quit = $hasQuit;
        $archived->gained_experience = $gainedExperience;
        $archived->experience_events = $experienceEvents;
        $archived->gained_currency_a = $gainedCurrencyA;
        $archived->gained_currency_b = $gainedCurrencyB;
        $archived->gained_currency_c = $gainedCurrencyC;
        $archived->save();
    }

    public static function archivedPlayerExists(string $matchId, string $userId): bool {
        return ArchivedPlayerProgression::where('user_id', '=', $userId)
            ->where('archived_game_id', '=', $matchId)
            ->exists();
    }
}
