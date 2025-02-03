<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use Rakit\Validation\Validator;

class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    public function register(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validation = (new Validator)->validate($data, [
            'username' => 'required|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return $validation->errors()->toArray();
        }

        $createdUser = $this->userRepository->createUser([
            'username' => $data['username'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        return $createdUser->toArray();
    }
}
