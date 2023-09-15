<?php
namespace App\Services\Auth;

use Spatie\DataTransferObject\DataTransferObject;

class UserLoginDto extends DataTransferObject
{
    public $email;
    public $password;

    public static function fromValidatedRequest(Array $validatedData): UserLoginDto
    {
        return new static([
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);
    }

    public function validationAttributes(): array
    {
        return [
            'email' => __('Email'),
            'password' => __('Password'),
        ];
    }
}
