<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ConverEmptyStringToNullByGet extends TransformsRequest
{
    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        return is_string($value) && $value === '' && request()->isMethod('GET') ? null : $value;
    }
}
