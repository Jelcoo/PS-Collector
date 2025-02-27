<?php

namespace App\Hooks;

use App\Models\Stamp;
use App\Repositories\StampRepository;
use App\Services\StampIndexerService;

class StampHook
{
    private StampIndexerService $indexer;
    private StampRepository $stampRepository;

    public function __construct()
    {
        $this->indexer = new StampIndexerService();
        $this->stampRepository = new StampRepository();
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
