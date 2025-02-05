<?php

namespace App\Repositories;

use App\Enum\CollectionAccessLevelEnum;
use App\Models\Collection;
use App\Helpers\QueryBuilder;
use App\Models\CollectionAccess;
use App\Models\User;

class CollectionRepository extends Repository
{
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();

        $this->userRepository = new UserRepository();
    }

    public function getAll($with = []): array
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryCollection = $queryBuilder->table('collections')->get();

        return array_map(function ($collection) use ($with) {
            $collection = new Collection($collection);

            foreach ($with as $relation) {
                switch ($relation) {
                    case 'author':
                        $collection->author = $this->getCollectionAuthor($collection);
                        break;
                    case 'stampCount':
                        $collection->stampCount = $this->getCollectionStampCount($collection);
                        break;
                }
            }

            return $collection;
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

    public function getCollectionStampCount(Collection $collection): int
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stampCount = $queryBuilder
            ->table('stamps')
            ->where('collection_id', '=', $collection->id)
            ->count();

        return $stampCount;
    }
}
