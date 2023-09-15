<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
            'account_no' => ['required', 'numeric'],
            'amount' => ['required', 'numeric', 'min:30'],
            'currency' => ['required'],
        ];

//        if ($this->input('deposit_method') == 1) {
//            $rules['account_no'] = 'required|numeric';
//            $rules['currency'] = 'required|string';
//        } else {
//            $rules['txid'] = 'required';
//        }
//
//        return $rules;
    }

    public function attributes(): array
    {
        return [
            'amount' => 'Deposit Amount',
            'account_no' => 'Account No',
            'currency' => 'Currency',
        ];
    }
}
