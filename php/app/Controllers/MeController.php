<?php

namespace App\Controllers;

use App\Config\Config;
use App\Repositories\UserRepository;
use App\Validation\UniqueRule;
use Rakit\Validation\Validator;

class MeController extends Controller
{
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    public function index(): array
    {
        return [
            'user' => $this->getSession()->toArray(),
        ];
    }

    public function update(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule());
        $validation = $validator->validate($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        $user = $this->getSession();
        
        $userEmail = $this->userRepository->getUserByUsernameOrEmail($data['email']);
        if ($userEmail && $userEmail->id !== $user->id) {
            return [
                'status' => 422,
                'errors' => [
                    'email' => ['Email already exists'],
                ],
            ];
        }

        try {
            $user = $this->userRepository->updateUser($user->id, [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email'=> $data['email'],
            ]);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Failed to update user',
            ];
        }

        return [
            'message' => 'User updated successfully',
        ];
    }

    public function updatePassword(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule());
        $validation = $validator->validate($data, [
            'oldPassword' => 'required|min:6',
            'newPassword' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        $user = $this->getSession();

        if (!password_verify($data['oldPassword'], $user->password)) {
            return [
                'status' => 422,
                'errors' => [
                    'oldPassword' => ['Current password is incorrect'],
                ],
            ];
        }

        try {
            $user = $this->userRepository->updateUser($user->id, [
                'password' => password_hash($data['newPassword'], PASSWORD_DEFAULT),
            ]);
        } catch (\Exception) {
            return [
                'status' => 500,
                'message' => 'Failed to update user',
            ];
        }

        return [
            'message' => 'Password updated successfully',
        ];
    }
}
