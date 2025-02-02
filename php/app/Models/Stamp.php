<?php

namespace App\Models;

class Stamp
{
    public int $id;
    public int $collection_id;
    public string $name;
    public bool $used;
    public bool $damaged;
    public string|null $image_uuid;
    public string $created_at;
}
