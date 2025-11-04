<?php
namespace App\Repositories\Interfaces;

interface UserRepositoryInterface {
    public function Register(array $data);

    public function Login();
}
