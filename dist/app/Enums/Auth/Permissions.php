<?php

namespace App\Enums\Auth;

enum Permissions: string
{
    case ADMIN_AREA = 'admin-area';
    case VIEW_LOG = 'view-log';
    case FILE_UPLOAD = 'file-upload';
    case GAME_NEWS = 'game-news';
}
