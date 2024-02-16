<?php

namespace App\APIClients\SteamAPIClient\ISteamUser\GetPlayerSummaries;

use Carbon\Carbon;
use Illuminate\Support\Arr;

class GetPlayerSummariesResponse
{
    /**
     * @var SteamPlayer[]
     */
    protected array $players;

    public function __construct(array $players)
    {
        foreach ($players as $player) {
            $this->players[] = new SteamPlayer(
                $player->steamid,
                $player->communityvisibilitystate,
                $player->profilestate,
                $player->personaname,
                $player->commentpermission,
                $player->profileurl,
                $player->avatar,
                $player->avatarmedium,
                $player->avatarfull,
                $player->avatarhash,
                Carbon::createFromTimestamp($player->lastlogoff),
                $player->personastate,
                $player->realname,
                $player->primaryclanid,
                Carbon::createFromTimestamp($player->timecreated),
                $player->personastateflags,
                $player->loccountrycode,
                $player->locstatecode,
            );
        }
    }

    public function getPlayer(int $steamId): SteamPlayer|false
    {
        foreach ($this->players as $player) {
            if($player->steamId == $steamId)
                return $player;
        }

        return false;
    }
}

class SteamPlayer {

    public function __construct(
        public readonly string $steamId,
        public readonly int $communityVisibilityState,
        public readonly int $profilestate,
        public readonly string $personName,
        public readonly int $commentPermission,
        public readonly string $profileUrl,
        public readonly string $avatar,
        public readonly string $avatarMedium,
        public readonly string $avatarFull,
        public readonly string $avatarHash,
        public readonly Carbon $lastLogOff,
        public readonly int $personState,
        public readonly string $realName,
        public readonly string $primaryClanId,
        public readonly Carbon $timeCreated,
        public readonly int $personaStateFlags,
        public readonly string $CountryCode,
        public readonly string $stateCode,
    )
    {
    }
}