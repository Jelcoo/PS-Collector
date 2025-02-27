<?php

namespace App\Controllers;

use App\Config\Config;
use App\Repositories\StampRepository;
use App\Services\StampIndexerService;

class SearchController
{
    private StampIndexerService $stampIndex;
    private StampRepository $stampRepository;

    public function __construct()
    {
        $this->stampIndex = new StampIndexerService();
        $this->stampRepository = new StampRepository();
    }

    public function search(int $collectionId): array
    {
        if ($this->stampIndex->indexExists() === false || Config::getKey('MEILI_REINDEX') === true) {
            $this->stampIndex->configureIndex();
            $allStamps = $this->stampRepository->getAllStamps(['header']);
            $this->stampIndex->indexStamps($allStamps);
        }

        $query = $_GET['query'] ?? '';
        $used = isset($_GET['used']) ? filter_var($_GET['used'], FILTER_VALIDATE_BOOLEAN) : null;
        $damaged = isset($_GET['damaged']) ? filter_var($_GET['damaged'], FILTER_VALIDATE_BOOLEAN) : null;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        $filters = [];

        $filters['collection_id'] = $collectionId;

        if ($used !== null) {
            $filters['used'] = $used ? 'true' : 'false';
        }
        if ($damaged !== null) {
            $filters['damaged'] = $damaged ? 'true' : 'false';
        }

        $searchResults = $this->stampIndex->search($query, $filters, $limit, $offset);

        return [
            'results' => $searchResults,
            'total' => count($searchResults),
            'query' => $query,
            'filters' => $filters,
        ];
    }
}
