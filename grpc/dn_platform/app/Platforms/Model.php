<?php


namespace App\Platforms;


use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    // use \Awobaz\Compoships\Compoships;

    protected $connection = 'platform';
}