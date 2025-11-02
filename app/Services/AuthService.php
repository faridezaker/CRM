<?php

namespace App\Services;


use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\SalesPersonRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

use Illuminate\Support\Facades\Hash;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository,protected SalesPersonRepositoryInterface $salesPersonRepository) {}

    public function register(array $data)
    {
       try {
            $data['password'] = bcrypt($data['password']);

            $user = $this->userRepository->register($data);

            if (! empty($data['marketing_code'])) {
                $this->salesPersonRepository->Create([
                    'user_id' => $user->id,
                    'marketing_code' => $data['marketing_code']
                ]);
            }

            return [
                'status' => true,
                'user' => new UserResource($user),
                'message' => 'User registered successfully',
            ];

        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Registration failed. Please try again later.',
            ];
        }
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
