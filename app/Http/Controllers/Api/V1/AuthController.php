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

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Creates a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="marketing_code", type="integer", example="45466456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
     *             ),
     *             @OA\Property(property="message", type="string", example="User created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The email has already been taken."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="The email has already been taken.")
     *                 )
     *             ),
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $userData = $this->authService->register($request->validated());

        return response()->json([
            'user' => new UserResource($userData['user']),
            'message' => 'User successfully registered',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Auth"},
     *     summary="Login user",
     *     description="Authenticate user and return JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string", example="admin"),
     *                     @OA\Property(property="email", type="string", example="admin@example.com")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJh...")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="data", type="object", nullable=true, example=null),
     *             @OA\Property(property="message", type="string", example="invalid_credentials")
     *         )
     *     )
     * )
     */
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
