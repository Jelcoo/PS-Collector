<?php

namespace App\Models;

class Model
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
