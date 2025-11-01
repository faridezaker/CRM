<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {
    public function Register(array $data)
    {
        return User::create($data);
    }

    public function Login()
    {
        return auth()->user();
    }

}
