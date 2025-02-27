<?php

namespace App\Hooks;

use App\Models\Stamp;
use App\Services\StampIndexerService;

class StampHook
{
    private StampIndexerService $indexer;

    public function __construct()
    {
        $this->indexer = new StampIndexerService();
    }

    public function afterSave(Stamp $stamp): void
    {
        $this->indexer->indexStamp($stamp);
    }

    public function afterDelete(int $stampId): void
    {
        $this->indexer->removeStamp($stampId);
    }
}
