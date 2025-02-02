<?php

namespace App\Enum;

enum CollectionAccessLevelEnum: string
{
    case OWNER = 'owner';
    case INVITED = 'invited';
    case MEMBER = 'member';
}
