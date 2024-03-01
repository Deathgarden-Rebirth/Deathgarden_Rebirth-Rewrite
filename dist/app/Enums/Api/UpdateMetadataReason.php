<?php

namespace App\Enums\Api;

enum UpdateMetadataReason: string
{
    case CharacterDirty = 'Character dirty';

    case OnCloseLoadout = 'OnCloseLoadoout';

    case CharacterOverrideEvent = 'OnRequestCharacterOverrideEvent';

    case SetLastPlayedFaction = 'SetLastPlayedFaction';

    case SetLastPlayerCharacterId = 'SetLastPlayedCharacterId';
}
