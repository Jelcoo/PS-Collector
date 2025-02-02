<?php

namespace App\Enum;

enum CollectionAccessEnum: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case SHARED = 'shared';
}
