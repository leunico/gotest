<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    const LANGUAGE_CPULSPULS = 1;
    const RESULT_INIT = 14; //初始值
    const RESULT_WAITING = 0;
    const RESULT_COMPILE_ERR = 11;

    protected $table = 'solution';

    protected $primaryKey  = 'solution_id';

    public $timestamps = false;

    public static $languages = [
        self::LANGUAGE_CPULSPULS => 'C++'
    ];

    public function sourceCode()
    {
        return $this->hasOne(SourceCode::Class, 'solution_id');
    }

    public function customInput()
    {
        return $this->hasOne(CustomInput::Class, 'solution_id');
    }
}
