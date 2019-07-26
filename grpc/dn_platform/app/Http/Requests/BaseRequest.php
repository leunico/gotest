<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\Helpers;
use Illuminate\Http\Response;

class BaseRequest extends FormRequest
{
    use Helpers;

    /**
     * @return boolean
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, $this->response()->error($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
