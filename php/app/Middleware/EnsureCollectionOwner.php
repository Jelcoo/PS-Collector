<?php

namespace App\Middleware;

use App\Helpers\JwtHelper;
use App\Enum\CollectionAccessLevelEnum;
use App\Repositories\CollectionRepository;

class EnsureCollectionOwner extends Middleware implements MiddlewareInterface
{
    private CollectionRepository $collectionRepository;

    public function __construct()
    {
        $this->collectionRepository = new CollectionRepository();
    }

    public function verify(array $params = []): bool
    {
        $user = JwtHelper::getSessionUser();
        $collectionExists = $this->collectionRepository->collectionExists($params[0]);
        if (!$collectionExists) {
            $this->notFound();
        }
        $getCollectionAccess = $this->collectionRepository->getCollectionAccess($params[0], $user);
        if ($getCollectionAccess !== CollectionAccessLevelEnum::OWNER) {
            $this->forbidden();
        }

        return true;
    }
}
