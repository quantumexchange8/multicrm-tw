<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use App\Http\Controllers\LocalizationController;

class UserRegister extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function messages()
    {
        return [
            // 'name.required' => 'The first name field is required.',
            'name.required' => __('validation.first name'), //using multi language
            'name.display_length' => 'Min length 2 to 30'
        ];
    }

    public function rules()
    {

        return [
            'first_name'            => 'required',
            'middle_name'            => 'nullable',
            'last_name'            => 'required',
            'email'           => 'required|email|max:128|unique:users,email',
            'phone'           => 'required|string|unique:users,phone',
            'password'        => 'required|string|min:6|confirmed',
            'address_1'           => 'required|string',
            'address_2'           => 'nullable|string',
            'postcode'           => 'required|string',
            'country'           => 'required|string',
            'nationality'           => 'required|string',
            'race'           => 'required|string',
            'leverage' => 'required|integer',
            'currency' => 'required|string',
            'group' => 'required|string',
            'doc_identity_front' => 'required|file',
            'doc_address' => 'required|file',
            'referral' => 'nullable|exists:users,ib_id',
            'acknowledge' => 'required',
        ];
    }
}
