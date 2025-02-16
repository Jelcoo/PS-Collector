<?php

namespace App\Repositories;

use App\Enum\CollectionAccessLevelEnum;
use App\Models\Collection;
use App\Helpers\QueryBuilder;
use App\Models\CollectionAccess;
use App\Models\Stamp;
use App\Models\User;

class CollectionRepository extends Repository
{
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();

        $this->userRepository = new UserRepository();
    }

    public function getAll($with = [], int $page = null, int $perPage = null): array
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryCollection = $queryBuilder->table('collections');

        if ($page && $perPage) {
            $offset = ($page - 1) * $perPage;
            $queryCollection->limit($perPage, $offset);
        }

        $queryCollection = $queryCollection->get();

        return array_map(function ($collection) use ($with) {
            $collection = new Collection($collection);
            $collection = $this->with($collection, $with);

            return $collection;
        }, $queryCollection);
    }

    public function getCollectionCount(): int
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        return $queryBuilder->table('collections')->count();
    }

    public function getCollectionById(int $id, array $with = []): ?Collection
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryCollection = $queryBuilder->table('collections')->where('id', '=', $id)->first();

        $collection = $queryCollection ? new Collection($queryCollection) : null;
        if ($collection) {
            $collection = $this->with($collection, $with);
        }

        return $collection;
    }

    public function createCollection(array $data, int $userId): Collection
    {
        $queryBuilder = new QueryBuilder($this->getConnection());
        $queryBuilder->startTransaction();

        $collectionId = $queryBuilder->table('collections')->insert($data);

        $queryBuilder->table('collection_access')->insert([
            'collection_id' => $collectionId,
            'user_id' => $userId,
            'role' => CollectionAccessLevelEnum::OWNER->value,
        ]);

        $queryBuilder->commit();

        $collection = $this->getCollectionById((int) $collectionId);

        return $collection;
    }

    public function getCollectionAuthor(Collection $collection): ?User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $collectionAccess = $queryBuilder
            ->table('collection_access')
            ->where('collection_id', '=', $collection->id)
            ->where('role', '=', CollectionAccessLevelEnum::OWNER->value)
            ->first();
        $collectionAccess = $collectionAccess ? new CollectionAccess($collectionAccess) : null;
        
        $user = $collectionAccess ? $this->userRepository->getUserById($collectionAccess->user_id) : null;

        return $user;
    }

    public function getCollectionStamps(Collection $collection): array
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stamps = $queryBuilder
            ->table('stamps')
            ->where('collection_id', '=', $collection->id)
            ->get();

        return array_map(function ($stamp) {
            return new Stamp($stamp);
        }, $stamps);
    }

    public function getCollectionStampCount(Collection $collection): int
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stampCount = $queryBuilder
            ->table('stamps')
            ->where('collection_id', '=', $collection->id)
            ->count();

        return $stampCount;
    }

    public function with(Collection $collection, array $with): Collection
    {
        foreach ($with as $relation) {
            switch ($relation) {
                case 'author':
                    $collection->authorName = $this->getCollectionAuthor($collection)->username;
                    break;
                case 'stamps':
                    $collection->stamps = $this->getCollectionStamps($collection);
                    break;
                case 'stampCount':
                    $collection->stampCount = $this->getCollectionStampCount($collection);
                    break;
            }
        }

        return $collection;
    }
}
