<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;

class WechatUserTag extends Model
{
    const USEFUL_ON = 1;
    const USEFUL_OFF = 0;
    const CATEGORY_ART = 1;
    const CATEGORY_MUSIC = 2;
    const CATEGORYS = [
        'art' => 1,
        'music' => 2
    ];

    protected $fillable = [
        'wechat_tag_id',
        'name',
        'category',
        'useful'
    ];
}
