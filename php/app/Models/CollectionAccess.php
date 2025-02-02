<?php

namespace App\Models;

use App\Enum\CollectionAccessLevelEnum;

class CollectionAccess
{
    public int $collection_id;
    public int $user_id;
    public CollectionAccessLevelEnum $role;
}
