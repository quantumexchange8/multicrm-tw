<?php

namespace App\Services\Auth;

use Spatie\DataTransferObject\DataTransferObject;
use App\Services\Setting\Auth\UserRegisterSetting;

class CreateUserDto extends DataTransferObject
{
    public $first_name;
    public $middle_name;
    public $last_name;
    public $chinese_name;
    public $email;
    public $password;
    public $group;
    public $phone;
    public $dob;
    public $address_1;
    public $address_2;
    public $country;
    public $postcode;
    public $nationality;
    public $race;
    public $doc_identity_front;
    public $doc_identity_back;
    public $doc_address;
    public $referral_code;
    public $register_ip;

    public static function fromValidatedRequest(array $validatedData): CreateUserDto
    {
        return new static([
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'] ?? null,
            'last_name' => $validatedData['last_name'] ?? null,
            'chinese_name' => $validatedData['chinese_name'] ?? null,
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
            'phone' => $validatedData['phone'],
            'dob' => $validatedData['dob'],
            'address_1' => $validatedData['address_1'] ?? null,
            'address_2' => $validatedData['address_2'] ?? null,
            'country' => $validatedData['country'] ?? null,
            'postcode' => $validatedData['postcode'] ?? null,
            'nationality' => $validatedData['nationality'] ?? null,
            'race' => $validatedData['race'] ?? null,
            'doc_identity_front' => $validatedData['doc_identity_front'] ?? null,
            'doc_identity_back' => $validatedData['doc_identity_back'] ?? null,
            'doc_address' => $validatedData['doc_address'] ?? null,
            'referral_code' => $validatedData['referral_code'] ?? null,
            'register_ip' => $validatedData['register_ip'],
        ]);
    }
}
