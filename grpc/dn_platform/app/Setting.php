<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'name',
        'namespace',
        'note',
        'contents',
        'created_at',
        'updated_at'
    ];

    protected $casts  = [
        'contents' => 'json'
    ];

    /**
     * 获取该模型的路由的自定义键名。
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }
}
