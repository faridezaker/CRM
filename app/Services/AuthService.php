<?php

namespace App\Services;


use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\UserRepositoryInterface;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository) {}

    public function register(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        $user = $this->userRepository->register($data);

        return ['user' => $user];
    }

    public function login($data)
    {
        try {
            if (! $token = JWTAuth::attempt([
                'email' => $data['email'],
                'password' => $data['password'],
            ])) {
                return [
                    'status' => false,
                    'message' => 'Invalid credentials',
                    'code' => 401,
                ];
            }

            //$user = Auth::user();
            $user = $this->userRepository->login();

            return [
                'status' => true,
                'user' => new UserResource($user),
                'token' => $token,
            ];

        } catch (JWTException $e) {
            return [
                'status' => false,
                'message' => 'Could not create token',
                'code' => 500,
            ];
        }
    }
}
