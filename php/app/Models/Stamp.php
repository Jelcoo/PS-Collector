<?php

namespace App\Models;

class Stamp extends Model
{
    public int $id;
    public int $collection_id;
    public string $name;
    public bool $used;
    public bool $damaged;
    public string $created_at;

    public ?Collection $collection;
    public ?string $headerUrl;

    public function __construct(array $stamp)
    {
        $this->id = $stamp['id'];
        $this->collection_id = $stamp['collection_id'];
        $this->name = $stamp['name'];
        $this->used = $stamp['used'];
        $this->damaged = $stamp['damaged'];
        $this->created_at = $stamp['created_at'];
    }
}
