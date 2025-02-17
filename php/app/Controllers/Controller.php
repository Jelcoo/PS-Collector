<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\JwtHelper;
use App\Repositories\UserRepository;

class Controller
{
    private UserRepository $userRepository;
    private User|null $session = null;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    protected function getSession()
    {
        if (isset($this->session)) {
            return $this->session;
        }

        $sessionUserId = JwtHelper::getSessionUser();
        if (!is_null($sessionUserId)) {
            $this->session = $this->userRepository->getUserById($sessionUserId);
        }

        return $this->session;
    }

    protected function getWithRelations()
    {
        $with = $_GET['with'] ?? '';

        return explode(',', $with);
    }
}
