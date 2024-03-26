<?php

namespace App\Enums\Game\Message;

enum GameNewsRedirectMode: string
{
    case None = 'None';
    case Store = 'Store';
    case WebsiteLink = 'WebsiteLink';
}
