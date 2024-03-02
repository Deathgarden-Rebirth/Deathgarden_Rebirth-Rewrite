<?php

namespace App\APIClients\SteamAPIClient\ISteamUser\GetPlayerSummaries;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class GetPlayerSummariesResponse
{
    /**
     * @var SteamPlayer[]
     */
    protected array $players;

    public function __construct(array $players)
    {
        foreach ($players as $player) {
            /** @var Collection $player */
            $player = collect($player);
            $this->players[] = new SteamPlayer(
                $player->get('steamid'),
                $player->get('communityvisibilitystate'),
                $player->get('profilestate'),
                $player->get('personaname'),
                $player->get('commentpermission'),
                $player->get('profileurl'),
                $player->get('avatar'),
                $player->get('avatarmedium'),
                $player->get('avatarfull'),
                $player->get('avatarhash'),
                $player->get('lastlogoff') ? Carbon::createFromTimestamp($player->get('lastlogoff')) : null,
                $player->get('personastate'),
                $player->get('realname'),
                $player->get('primaryclanid'),
                $player->get('timecreated') ? Carbon::createFromTimestamp($player->get('timecreated')) : null,
                $player->get('personastateflags'),
                $player->get('loccountrycode'),
                $player->get('locstatecode'),
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
        public readonly ?int $communityVisibilityState,
        public readonly ?int $profilestate,
        public readonly ?string $personName,
        public readonly ?int $commentPermission,
        public readonly ?string $profileUrl,
        public readonly ?string $avatar,
        public readonly ?string $avatarMedium,
        public readonly ?string $avatarFull,
        public readonly ?string $avatarHash,
        public readonly ?Carbon $lastLogOff,
        public readonly ?int $personState,
        public readonly ?string $realName,
        public readonly ?string $primaryClanId,
        public readonly ?Carbon $timeCreated,
        public readonly ?int $personaStateFlags,
        public readonly ?string $CountryCode,
        public readonly ?string $stateCode,
    )
    {
    }
}