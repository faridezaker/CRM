<?php

namespace Tests\Unit;

use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\TestCase;

class RegisterUserEmailTest extends TestCase
{
    public function getRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    protected function validator(array $data)
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $factory = new ValidationFactory($translator);

        return $factory->make($data, $this->getRules());
    }

    /** @test */
    public function email_must_be_present_and_valid()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }

    /** @test */
    public function email_is_required()
    {
        $data = [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
    }
}
