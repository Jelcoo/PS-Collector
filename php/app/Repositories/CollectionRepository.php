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

    public function getAllForUser(int|null $userId, $with = []): array
    {
        if (is_null($userId)) {
            $query = $this->getConnection()->prepare('SELECT * FROM collections WHERE access = \'public\'');
        } else {
            $query = $this->getConnection()->prepare('SELECT DISTINCT c.* FROM collections c LEFT JOIN collection_access ca ON c.id = ca.collection_id WHERE c.access = \'public\' OR (ca.user_id = :user_id AND ca.role IN (\'owner\', \'member\'))');
            $query->bindValue(':user_id', $userId);
        }

        $query->execute();
        $queryCollection = $query->fetchAll();

        return array_map(function ($collection) use ($with) {
            $collection = new Collection($collection);
            $collection = $this->with($collection, $with);

            return $collection;
        }, $queryCollection);
    }

    public function getCollectionAccess(int $collectionId, int|null $userId): CollectionAccessLevelEnum
    {
        $query = $this->getConnection()->prepare('SELECT CASE WHEN c.access = \'public\' THEN \'public\' WHEN ca.role IS NOT NULL THEN ca.role ELSE \'none\' END as access_level FROM collections c LEFT JOIN collection_access ca ON c.id = ca.collection_id AND ca.user_id = :user_id WHERE c.id = :collection_id');
        $query->bindValue(':user_id', $userId);
        $query->bindValue(':collection_id', $collectionId);
        $query->execute();

        $accessLevel = $query->fetch();

        if (!$accessLevel) {
            return CollectionAccessLevelEnum::NONE;
        }

        return CollectionAccessLevelEnum::from($accessLevel['access_level']);
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

    public function collectionExists(int $id): bool
    {
        $queryBuilder = new QueryBuilder($this->getConnection());
        $queryCollection = $queryBuilder->table('collections')->where('id','=', $id)->first();

        return $queryCollection ? true : false;
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
