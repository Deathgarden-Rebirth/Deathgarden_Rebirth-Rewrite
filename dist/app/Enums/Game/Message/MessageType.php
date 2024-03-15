<?php

namespace App\Enums\Game\Message;

enum MessageType: string
{
    case InGameNews = 'InGameNews';
    case PopUpNews = 'PopUpNews';
}
