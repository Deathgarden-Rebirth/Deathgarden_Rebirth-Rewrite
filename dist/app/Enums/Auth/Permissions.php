<?php

namespace App\Enums\Auth;

enum Permissions: string
{
    case ADMIN_AREA = 'admin-area';
    case VIEW_LOG = 'view-log';
    case FILE_UPLOAD = 'file-upload';
    case GAME_NEWS = 'game-news';
    case VIEW_USERS = 'view-users';
    case EDIT_USERS = 'edit-users';
    case USER_BANS = 'user-bans';
    // Access to the homepage while its in maintenance mode
    case VIEW_MAINTENANCE = 'view-maintenance-mode';
    case INBOX_MAILER = 'inbox-mailer';
    case CHAT_REPORTS = 'chat-reports';
    case PLAYER_REPORTS = 'player-reports';
}
