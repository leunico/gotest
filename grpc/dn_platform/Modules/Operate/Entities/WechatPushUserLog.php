<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;

class WechatPushUserLog extends Model
{
    protected $fillable = [
        'category',
        'wechat_push_job_id',
        'openid'
    ];
}
