<?php

namespace App\Middleware;

use App\Helpers\JwtHelper;
use App\Repositories\StampRepository;
use App\Enum\CollectionAccessLevelEnum;
use App\Repositories\CollectionRepository;

class EnsureCollectionStampAccess extends Middleware implements MiddlewareInterface
{
    private CollectionRepository $collectionRepository;
    private StampRepository $stampRepository;

    public function __construct()
    {
        $this->collectionRepository = new CollectionRepository();
        $this->stampRepository = new StampRepository();
    }

    public function verify(array $params = []): bool
    {
        $user = JwtHelper::getSessionUser();

        $stamp = $this->stampRepository->getStampById($params[0]);
        if (!$stamp) {
            $this->notFound();
        }

        $stampCollection = $stamp->collection_id;
        $getCollectionAccess = $this->collectionRepository->getCollectionAccess($stampCollection, $user);
        if ($getCollectionAccess === CollectionAccessLevelEnum::NONE) {
            $this->forbidden();
        }

        return true;
    }
}
