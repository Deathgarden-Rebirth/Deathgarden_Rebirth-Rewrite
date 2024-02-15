<?php

namespace App\Enums\Game;

enum Runner: string
{
    case Smoke = 'Smoke';
    case Ink = 'Ink';
    case Ghost = 'Ghost';
    case Sawbones = 'Sawbones';
    case Switch = 'Switch';
    case Dash = 'Dash';

    public function getTag(): string
    {
        return 'Runner.'.$this->value;
    }
}
