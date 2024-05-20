<?php

namespace App\Enums\Api\Ban;

enum BanStatus
{
    // No Previus Bans on Record
    case Good;

    // Was Banned before, but it expired
    case Warning;

    // Currently Banned
    case Banned;
}
