<?php

namespace App\Repositories;

use App\Models\User;
use App\Helpers\QueryBuilder;

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

    public function getUserByPasswordResetToken(string $token): ?User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryUser = $queryBuilder->table('users')->where('password_reset_token', '=', $token)->first();

        return $queryUser ? new User($queryUser) : null;
    }

    public function createUser(array $data): User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $userId = $queryBuilder->table('users')->insert($data);
        $user = $this->getUserById((int) $userId);

        return $user;
    }

    public function updateUser(int $userId, array $data): User
    {
        $queryBuilder = new QueryBuilder($this->getConnection());

        $queryBuilder->table('users')->where('id', '=', $userId)->update($data);

        return $this->getUserById($userId);
    }
}
