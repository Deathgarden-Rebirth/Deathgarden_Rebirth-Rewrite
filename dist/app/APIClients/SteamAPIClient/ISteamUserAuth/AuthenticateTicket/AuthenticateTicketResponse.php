<?php

namespace App\APIClients\SteamAPIClient\ISteamUserAuth\AuthenticateTicket;

class AuthenticateTicketResponse
{
    public int $steamId;

    public int $ownerSteamId;

    public bool $isVacBanned;

    public bool $isPublisherBanned;

    public function __construct(
        int $steamId,
        int $ownerSteamId,
        int $isVacBanned,
        int $isPublisherBanned
    )
    {
        $this->steamId = $steamId;
        $this->ownerSteamId = $ownerSteamId;
        $this->isVacBanned = $isVacBanned;
        $this->isPublisherBanned = $isPublisherBanned;
    }

    public function isBanned(): bool
    {
        return $this->isVacBanned || $this->isPublisherBanned;
    }

    public function isOriginalOwner(): bool
    {
        return $this->steamId === $this->ownerSteamId;
    }
}