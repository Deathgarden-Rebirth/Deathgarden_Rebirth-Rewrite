<?php

namespace App\Enums\Game;

enum PlayerReportReason: string
{
    case Other = 'other';
    case DevImpersonation = 'PlayerDevImpersonation';
    case InappropriateName = 'InappropriateNameLanguage';

    case hackingExploiting = 'HackingExploiting';

    case Griefing = 'GriefingToxicBehavior';

    case LeavingGameHarrasment = 'LeavingGameAFKHarassment';

}