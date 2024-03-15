<?php

namespace App\Http\Responses\Api\Messages\News;

use App\Enums\Game\Faction;
use App\Enums\Game\Message\GameNewsRedirectMode;
use App\Enums\Game\Message\MessageType;
use DateTime;

class GameNews
{
    public string $id;

    public MessageType $messageType;

    public Faction $faction = Faction::None;

    public bool $isOneTimeGameNews = false;

    public bool $shouldQuitTheGame = false;

    public bool $onlyForPlayersThatCompletedAtLeastOneMatch = false;

    public GameNewsRedirectMode $redirectMode = GameNewsRedirectMode::None;

    public ?string $redirectItem = '';

    public ?string $redirectUrl = '';

    public ?string $embeddedBackgroundImage = null;

    public ?string $embeddedInGameNewsBackgroundImage = null;

    public ?string $embeddedInGameNewsThumbnailImage = null;

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public ?int $displayXTimes = null;

    public ?int $maxPlayerLevel = null;

    public array $translations = [];

    public function __construct(string $id, MessageType $type)
    {
        $this->id = $id;
        $this->messageType = $type;
    }
}