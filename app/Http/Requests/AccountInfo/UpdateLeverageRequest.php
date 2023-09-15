<?php

namespace App\Http\Requests\AccountInfo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeverageRequest extends FormRequest
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
            'account_no' => 'required',
            'terms' => 'required'
        ];
    }

    public function attributes(): array
    {
        return [
            'leverage' => 'Leverage',
            'account_no' => 'Account No',
            'terms' => 'Terms & Conditions'
        ];
    }
}
