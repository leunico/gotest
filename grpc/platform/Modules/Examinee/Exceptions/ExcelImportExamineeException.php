<?php

namespace Modules\Examinee\Exceptions;

class ExcelImportExamineeException extends \Exception
{
    /**
     * Create a new ExcelImportExamineeException exception.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = '处理Excel错误.')
    {
        parent::__construct($message);
    }
}
