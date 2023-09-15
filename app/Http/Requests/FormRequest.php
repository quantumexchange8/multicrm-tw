<?php

namespace App\Http\Requests;

use App\Exceptions\CustomException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = collect((new ValidationException($validator))->errors());

            throw new CustomException($errors->first()[0]);
        }

        parent::failedValidation($validator);
    }
}
