<?php

namespace App\Repositories;

use App\Models\Stamp;
use App\Helpers\QueryBuilder;

class StampRepository extends Repository
{
    public function getAllStamps(): array
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stamps = $queryBuilder->table('stamps')->get();

        return array_map(function ($stamp) {
            return new Stamp($stamp);
        }, $stamps);
    }

    public function getStampById(int $id, array $with = []): ?Stamp
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryStamp = $queryBuilder->table('stamps')->where('id', '=', $id)->first();

        $stamp = $queryStamp ? new Stamp($queryStamp) : null;
        if ($stamp) {
            $stamp = $this->with($stamp, $with);
        }

        return $stamp;
    }

    public function getStampsByCollection(int $collectionId, array $with = []): array
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stamps = $queryBuilder->table('stamps')->where('collection_id', '=', $collectionId)->get();

        return array_map(function ($stamp) use ($with) {
            $stamp = new Stamp($stamp);
            $stamp = $this->with($stamp, $with);

            return $stamp;
        }, $stamps);
    }

    public function stampExists(int $id): bool
    {
        $queryBuilder = new QueryBuilder($this->getConnection());
        $queryCollection = $queryBuilder->table('stamps')->where('id', '=', $id)->first();

        return $queryCollection ? true : false;
    }

    public function createStamp(array $data): Stamp
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stampId = $queryBuilder->table('stamps')->insert($data);
        $stamp = $this->getStampById((int) $stampId);

        return $stamp;
    }

    public function updateStamp(int $stampId, array $data): Stamp
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryBuilder->table('stamps')->where('id', '=', $stampId)->update($data);

        return $this->getStampById($stampId);
    }

    public function deleteStamp(int $stampId): void
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryBuilder->table('stamps')->where('id', '=', $stampId)->delete();
    }

    public function with(Stamp $stamp, array $with): Stamp
    {
        foreach ($with as $relation) {
            switch ($relation) {
                case 'collection':
                    $stamp->collection = $this->getCollectionRepository()->getCollectionById($stamp->collection_id, ['access']);
                    break;
                case 'header':
                    $assets = $this->getAssetRepository()->getAssetsByModel($stamp, 'header');
                    $asset = $assets[0] ?? null;
                    if (!is_null($asset)) {
                        $stamp->headerUrl = $asset->getUrl();
                    }

                    break;
            }
        }

        return $stamp;
    }
}
