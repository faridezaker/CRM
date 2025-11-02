<?php

namespace Tests\Unit;

use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\TestCase;

class RegisterUserNameTest extends TestCase
{
    public function getRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'marketing_code'=>'nullable|integer',
        ];
    }

    protected function validator(array $data)
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $factory = new ValidationFactory($translator);

        return $factory->make($data, $this->getRules());
    }

    /** @test */
    public function name_is_required()
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validator($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->messages());
    }
}
