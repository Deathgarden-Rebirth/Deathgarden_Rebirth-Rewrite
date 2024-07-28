<?php

namespace App\Http\Requests\Api\Moderation;

use App\Enums\Game\CharacterState;
use App\Enums\Game\Faction;

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
            $array['playtimeInSec'],
            $array['isReportedPlayer'],
            $array['isReporterPlayer']);
    }
}