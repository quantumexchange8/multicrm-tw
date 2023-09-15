<?php

namespace App\Http\Requests\AccountInfo;

use Illuminate\Foundation\Http\FormRequest;

class AddTradingAccountRequest extends FormRequest
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
            'leverage' => 'required|numeric',
            'currency' => 'required',
            'group' => 'required',
            'terms' => 'required',
            'additionalNotes' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'leverage' => 'Leverage',
            'currency' => 'Currency',
            'group' => 'Account Type',
            'terms' => 'Terms & Conditions',
            'additionalNotes' => 'Additional Notes',
        ];
    }
}
