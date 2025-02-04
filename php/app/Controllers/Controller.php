<?php

namespace App\Controllers;

use App\Helpers\JwtHelper;
use App\Models\User;
use App\Repositories\UserRepository;

class Controller
{
    private UserRepository $userRepository;
    private User $session;

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
        $this->session = $this->userRepository->getUserById($sessionUserId);

        return $this->session;
    }
}
