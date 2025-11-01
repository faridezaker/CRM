<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

use App\Http\Resources\UserResource;
use App\services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $service) {
        $this->authService = $service;
    }

    public function register(RegisterRequest $request)
    {
        $userData = $this->authService->register($request->validated());
        return response()->json([
            'user' => new UserResource($userData['user']),
            'message' => 'User successfully registered',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (! $result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], $result['code']);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $result['user'],
                'token' => $result['token'],
            ],
        ]);
    }

}
