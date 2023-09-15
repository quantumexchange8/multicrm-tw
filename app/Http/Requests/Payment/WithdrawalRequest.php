<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
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
            'account_no' => 'required|string',
            'account_type' => 'required|string',
            'amount' => 'required|numeric|min:30',
            'channel' => 'required|string',
            'terms' => 'required',
        ];
    }

    public function attributes(): array
    {
        $attributeNames = [
            'amount' => 'Amount',
            'channel' => 'Withdrawal Method',
            'terms' => 'Term and Conditions',
        ];

        if ($this->input('channel') =='bank') {
            $attributeNames['account_no'] = 'Bank Account';
            $attributeNames['account_type'] = 'Bank';
        } else {
            $attributeNames['account_no'] = 'Cryptocurrency Address';
            $attributeNames['account_type'] = 'Cryptocurrency';
        }

        return $attributeNames;
    }
}
