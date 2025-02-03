<?php

namespace App\Models;

use App\Enum\CollectionAccessLevelEnum;

class CollectionAccess extends Model
{
    public int $collection_id;
    public int $user_id;
    public CollectionAccessLevelEnum $role;

    public function __construct(array $collectionAccess)
    {
        $this->collection_id = $collectionAccess['collection_id'];
        $this->user_id = $collectionAccess['user_id'];
        $this->role = CollectionAccessLevelEnum::from($collectionAccess['role']);
    }
}
