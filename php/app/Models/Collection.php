<?php

namespace App\Models;

use App\Enum\CollectionAccessEnum;

class Collection
{
    public int $id;
    public string $name;
    public CollectionAccessEnum $access;
    public string $created_at;
}
