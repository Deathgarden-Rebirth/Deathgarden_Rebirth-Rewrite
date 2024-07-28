<?php

namespace App\Enums\Game;

enum CharacterState: string
{
    case None = 'None';
    case InArena = 'InArena';

    case Dead = 'Dead';
    case Escaped = 'Escaped';
    case Quitter = 'Quitter';
}
