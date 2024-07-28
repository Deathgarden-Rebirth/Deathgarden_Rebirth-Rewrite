<?php

namespace App\Http\Requests\Api\Moderation;

use App\Enums\Game\CharacterState;
use App\Enums\Game\Faction;
use App\Models\User\User;

class ReportPlayerInfoListEntry
{
    public function __construct(
        public string $playerId,
        public CharacterState $characterState,
        public Faction $faction,
        public int $totalXpEarned,
        public float $playtimeInSeconds,
        public bool $isReportedPlayer,
        public bool $isReportingPlayer,
    )
    {}

    public static function makeFromArray(array $array)
    {
        return new ReportPlayerInfoListEntry(
            $array['playerId'],
            CharacterState::tryFrom($array['characterState']),
            Faction::tryFrom($array['faction']),
            $array['totalXpEarned'],
            $array['playtimeInSeconds'] ?? $array['playtimeInSec'],
            $array['isReportedPlayer'],
            $array['isReportingPlayer'] ?? $array['isReporterPlayer']);
    }

    public function getPlayerName(): string|null {
        return User::find($this->playerId, 'last_known_username')?->last_known_username;
    }
}