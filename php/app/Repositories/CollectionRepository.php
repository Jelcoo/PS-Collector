<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Stamp;
use App\Models\Collection;
use App\Helpers\QueryBuilder;
use App\Models\CollectionAccess;
use App\Enum\CollectionAccessLevelEnum;
use App\Helpers\JwtHelper;

class CollectionRepository extends Repository
{
    private UserRepository $userRepository;
    private StampRepository $stampRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->stampRepository = new StampRepository();
    }

    public function getAllForUser(?int $userId, $with = []): array
    {
        if (is_null($userId)) {
            $query = $this->getConnection()->prepare('SELECT * FROM collections WHERE access = \'public\'');
        } else {
            $query = $this->getConnection()->prepare("
SELECT DISTINCT c.*
FROM collections c
LEFT JOIN collection_access ca
    ON c.id = ca.collection_id
WHERE c.access = 'public'
OR (ca.user_id = :user_id
    AND ca.role IN ('owner', 'member'))");
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

    public function getCollectionAccess(int $collectionId, ?int $userId): CollectionAccessLevelEnum
    {
        $query = $this->getConnection()->prepare("
SELECT
    CASE
        WHEN ca.role = 'owner' THEN 'owner'
        WHEN c.access = 'public' THEN 'public'
        WHEN ca.role IS NOT NULL THEN ca.role
        ELSE 'none'
    END as access_level
FROM collections c
LEFT JOIN collection_access ca
    ON c.id = ca.collection_id
    AND ca.user_id = :user_id
WHERE c.id = :collection_id");
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
        $queryCollection = $queryBuilder->table('collections')->where('id', '=', $id)->first();

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

    public function updateCollection(int $id, array $data): Collection
    {
        $queryBuilder = new QueryBuilder($this->getConnection());
        $queryBuilder->table('collections')->where('id', '=', $id)->update($data);

        return $this->getCollectionById($id);
    }

    public function deleteCollection(int $id): void
    {
        $queryBuilder = new QueryBuilder($this->getConnection());
        $queryBuilder->table('collections')->where('id', '=', $id)->delete();
    }

    public function addMemberToCollection(int $collectionId, int $userId): void
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryBuilder->table('collection_access')->insert([
            'collection_id' => $collectionId,
            'user_id' => $userId,
            'role' => CollectionAccessLevelEnum::MEMBER->value,
        ]);
    }

    public function removeMemberFromCollection(int $collectionId, int $userId): void
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryBuilder->table('collection_access')->where('collection_id', '=', $collectionId)->where('user_id', '=', $userId)->delete();
    }

    public function getCollectionAuthor(int $collectionId): ?User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $collectionAccess = $queryBuilder
            ->table('collection_access')
            ->where('collection_id', '=', $collectionId)
            ->where('role', '=', CollectionAccessLevelEnum::OWNER->value)
            ->first();
        $collectionAccess = $collectionAccess ? new CollectionAccess($collectionAccess) : null;

        $user = $collectionAccess ? $this->userRepository->getUserById($collectionAccess->user_id) : null;

        return $user;
    }

    public function getCollectionStamps(int $collectionId): array
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stamps = $queryBuilder
            ->table('stamps')
            ->where('collection_id', '=', $collectionId)
            ->get();

        return array_map(function ($stamp) {
            return new Stamp($stamp);
        }, $stamps);
    }

    public function getCollectionStampCount(int $collectionId): int
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $stampCount = $queryBuilder
            ->table('stamps')
            ->where('collection_id', '=', $collectionId)
            ->count();

        return $stampCount;
    }

    public function getCollectionMembers(int $collectionId): array
    {
        $query = $this->getConnection()->prepare("
SELECT ca.user_id, ca.collection_id, u.username, ca.role
FROM `collection_access` ca
LEFT JOIN users u
    ON ca.user_id = u.id
WHERE ca.collection_id = :collection_id;");
        $query->bindValue(':collection_id', $collectionId);
        $query->execute();

        return $query->fetchAll();
    }

    public function with(Collection $collection, array $with): Collection
    {
        foreach ($with as $relation) {
            switch ($relation) {
                case 'author':
                    $collection->authorName = $this->getCollectionAuthor($collection->id)->username;
                    break;
                case 'stamps':
                    $collection->stamps = $this->stampRepository->getStampsByCollection($collection->id, ['header']);
                    break;
                case 'stampCount':
                    $collection->stampCount = $this->getCollectionStampCount($collection->id);
                    break;
                case 'access':
                    $collection->userAccess = $this->getCollectionAccess($collection->id, JwtHelper::getSessionUser());
                    break;
                case 'members':
                    if ($this->getCollectionAccess($collection->id, JwtHelper::getSessionUser()) === CollectionAccessLevelEnum::OWNER) {
                        $collection->members = $this->getCollectionMembers($collection->id);
                    }
                    break;
            }
        }

        return $collection;
    }
}
