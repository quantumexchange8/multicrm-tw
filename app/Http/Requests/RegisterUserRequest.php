<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [

            //Page 1
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'phone' => 'required|string|max:100|unique:'.User::class,
            'password' => ['required', 'confirmed', Password::min(6)->letters()->mixedCase()->numbers()],

            //Page 2
            'first_name' => 'required|regex:/^[a-zA-Z0-9\p{Han}. ]+$/u|max:255',
            'chinese_name' => 'nullable|regex:/^[a-zA-Z0-9\p{Han}. ]+$/u',
            'dob' => 'required',
            'country' => 'required',

            //Page 3
            'account_platform' => 'required',
            'account_type' => 'required',
            'leverage' => 'required',

            //Page 4
            'front_identity' => 'nullable|image|max:5120',
            'back_identity' => 'nullable|image|max:5120',
            'verification_via' => 'required',
            'verification_code' => 'required|string',
            'referral_code' => 'nullable|string',
            'terms' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            //Page 1
            'email' => 'Email',
            'phone' => 'Phone',
            'password' =>'Password',
            'password_confirmation' =>'Confirm Password',

            //Page 2
            'name' => 'Full Name',
            'chinese_name' => 'Full Name',
            'dob' => 'Date of Birth',
            'country' => 'Country',

            //Page 3
            'account_platform' => 'Account Platform',
            'account_type' => 'Account Type',
            'leverage' => 'Leverage',

            //Page 4
            'front_identity' => 'Proof of Identity (FRONT)',
            'back_identity' => 'Proof of Identity (BACK)',
            'verification_via' => 'Verification Via',
            'verification_code' => 'Verification Code',
            'terms' => 'Term and Conditions',
        ];
    }
}
