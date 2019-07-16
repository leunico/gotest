<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException as IlluminateValidationException;

class ValidationException extends IlluminateValidationException
{
    /**
     * Create a new exception instance.
     *
     * @param \Illuminate\Validation\ValidationException $exception
     * @author lizx
     */
    public function __construct(IlluminateValidationException $exception)
    {
        parent::__construct($exception->validator, $exception->response, $exception->errorBag);

        $errors = $exception->errors();
        $this->status = $exception->status;
        $this->redirectTo = $exception->redirectTo;
        $this->message = trans('[错误]：' . array_pop($errors)[0]);
    }
}
