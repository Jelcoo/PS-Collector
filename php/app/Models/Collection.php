<?php

namespace App\Models;

use App\Enum\CollectionAccessEnum;
use App\Enum\CollectionAccessLevelEnum;

class Collection extends Model
{
    public int $id;
    public string $name;
    public CollectionAccessEnum $access;
    public string $created_at;

    public ?string $authorName;
    public ?array $stamps;
    public ?int $stampCount;
    public ?CollectionAccessLevelEnum $userAccess;
    public ?array $members;

    public function __construct(array $collection)
    {
        $this->id = $collection['id'];
        $this->name = $collection['name'];
        $this->access = CollectionAccessEnum::from($collection['access']);
        $this->created_at = $collection['created_at'];
    }
}
