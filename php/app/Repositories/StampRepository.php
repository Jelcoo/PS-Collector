<?php

namespace App\Repositories;

use App\Models\User;
use App\Helpers\QueryBuilder;
use App\Models\Stamp;

class StampRepository extends Repository
{
    public function getStampById(int $id): ?Stamp
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryStamp = $queryBuilder->table('stamps')->where('id', '=', $id)->first();

        return $queryStamp ? new Stamp($queryStamp) : null;
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
}
