<?php

namespace App\Services;

use App\Models\Stamp;
use App\Config\Config;
use MeiliSearch\Client;

class StampIndexerService
{
    private Client $client;
    private string $indexName = 'stamps';

    public function __construct()
    {
        $this->client = new Client(Config::getKey('MEILI_URL'), Config::getKey('MEILI_KEY'));
    }

    public function configureIndex(): void
    {
        $index = $this->client->index($this->indexName);

        $index->updateSearchableAttributes([
            'name',
        ]);

        $index->updateFilterableAttributes([
            'collection_id',
            'used',
            'damaged',
        ]);

        $index->updateSortableAttributes([
            'name',
            'created_at',
        ]);

        $this->client->updateIndex($this->indexName, ['primaryKey' => 'id']);
    }

    public function indexStamp(Stamp $stamp): void
    {
        $stampData = $stamp->toArray();

        $this->client->index($this->indexName)->addDocuments([$stampData]);
    }

    public function indexStamps(array $stamps): void
    {
        $stampData = array_map(function (Stamp $stamp) {
            return $stamp->toArray();
        }, $stamps);

        if (!empty($stampData)) {
            $this->client->index($this->indexName)->addDocuments($stampData);
        }
    }

    public function removeStamp(int $stampId): void
    {
        $this->client->index($this->indexName)->deleteDocument($stampId);
    }

    public function indexExists(): bool
    {
        try {
            $this->client->getIndex($this->indexName);
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    public function search(string $query, array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $searchParams = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        if (!empty($filters)) {
            $searchParams['filter'] = $this->buildFilters($filters);
        }

        return $this->client->index($this->indexName)->search($query, $searchParams)->getHits();
    }

    private function buildFilters(array $filters): array
    {
        $filterExpressions = [];

        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $filterExpressions[] = "$field IN [" . implode(',', $value) . ']';
            } else {
                $filterExpressions[] = "$field = $value";
            }
        }

        return $filterExpressions;
    }
}
