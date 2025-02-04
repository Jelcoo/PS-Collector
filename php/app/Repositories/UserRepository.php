<?php

namespace App\Repositories;

use App\Models\User;
use App\Helpers\QueryBuilder;

/**
 * @method self useDatabase(string $database)
 * @method self useTable(string $table)
 */
class UserRepository extends Repository
{
    public function getUserById(int $id): ?User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryUser = $queryBuilder->table('users')->where('id', '=', $id)->first();

        return $queryUser ? new User($queryUser) : null;
    }

    public function getUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryUser = $queryBuilder->table('users')->where('username', '=', $usernameOrEmail)->orWhere('email', '=', $usernameOrEmail)->first();

        return $queryUser ? new User($queryUser) : null;
    }

    public function createUser(array $data): User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $userId = $queryBuilder->table('users')->insert($data);
        $user = $this->getUserById($userId);

        return $user;
    }
}
