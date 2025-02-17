<?php

namespace App\Enum;

enum CollectionAccessLevelEnum: string
{
    case OWNER = 'owner';
    case MEMBER = 'member';
    case PUBLIC = 'public';
    case NONE = 'none';
}
