<?php

namespace App\Repositories;

use App\Models\Collection;
use App\Helpers\QueryBuilder;

class CollectionRepository extends Repository
{
    public function getAll(): array
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryCollection = $queryBuilder->table('collections')->get();

        return array_map(function ($collection) {
            return new Collection($collection);
        }, $queryCollection);
    }

    public function getCollectionById(int $id): ?Collection
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryCollection = $queryBuilder->table('collections')->where('id', '=', $id)->first();

        return $queryCollection ? new Collection($queryCollection) : null;
    }

    public function createCollection(array $data): Collection
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $collectionId = $queryBuilder->table('collections')->insert($data);
        $collection = $this->getCollectionById((int) $collectionId);

        return $collection;
    }
}
