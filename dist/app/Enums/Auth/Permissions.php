<?php

namespace App\Enums\Auth;

enum Permissions: string
{
    case VIEW_LOG = 'view-log';
    case FILE_UPLOAD = 'file-upload';
}
