<?php

namespace App\Controllers;

use App\Config\Config;
use App\Helpers\JwtHelper;
use App\Validation\UniqueRule;
use Rakit\Validation\Validator;
use App\Repositories\UserRepository;
use App\Services\EmailService;

class AuthController extends Controller
{
    private UserRepository $userRepository;
    private EmailService $emailService;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->emailService = new EmailService();
    }

    public function register(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule());
        $validation = $validator->validate($data, [
            'username' => 'required|unique:users,username|max:255',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $createdUser = $this->userRepository->createUser([
                'username' => $data['username'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ]);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        $jwtToken = JwtHelper::generateToken($createdUser);

        return [
            'token' => $jwtToken,
            'user' => $createdUser->toArray(),
        ];
    }

    public function login(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validation = $validator->validate($data, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $user = $this->userRepository->getUserByUsernameOrEmail($data['email']);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        if (!$user || !password_verify($data['password'], $user->password)) {
            return [
                'status' => 401,
                'error' => 'Invalid login credentials',
            ];
        }

        $jwtToken = JwtHelper::generateToken($user);

        return [
            'token' => $jwtToken,
            'user' => $user->toArray(),
        ];
    }

    public function resetPassword(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validation = $validator->validate($data, [
            'email' => 'required_without:token',
            'token' => 'required_with:password',
            'password' => 'required_with:token|min:6',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        if (isset($data['token'])) {
            try {
                $user = $this->userRepository->getUserByPasswordResetToken($data['token']);
            } catch (\Exception) {
                return [
                    'status' => 500,
                    'error' => 'Something went wrong',
                ];
            }

            if (!$user) {
                return [
                    'status' => 404,
                    'error' => 'Invalid token',
                ];
            }

            $this->userRepository->updateUser($user->id, [
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'password_reset_token' => null,
            ]);

            return [
                'message' => 'Your password has been reset.',
            ];
        } else {
            try {
                $user = $this->userRepository->getUserByUsernameOrEmail($data['email']);
            } catch (\Exception) {
                return [
                    'status' => 500,
                    'error' => 'Something went wrong',
                ];
            }

            if (!is_null($user)) {
                $token = bin2hex(openssl_random_pseudo_bytes(28));

                $this->userRepository->updateUser($user->id, [
                    'password_reset_token' => $token,
                ]);

                $this->emailService
                    ->addRecipient($user->email)
                    ->setContent(
                        'Reset your password',
                        'Click <a href="' . Config::getKey('APP_URL') . '/auth/password?token=' . $token . '">here</a> to reset your password.'
                    )
                    ->send();
            }

            return [
                'message' => 'If an account with that email exists, you will receive an email with instructions on how to reset your password.',
            ];
        }
    }
}
