<?php

namespace Modules\Operate\Entities;

use App\Platforms\User;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [];

    /**
     * 数字音乐一对一
     */
    const TAG_MUSIC_CONTEST = 'music_contest';

    const AFFAIR_MAP = [
        1 => '小乐老师',
        2 => '小恩老师'
    ];
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function platform_user()
    {
        return $this->belongsTo(User::class, 'mobile', 'mobile');
    }

    public function wechat_user()
    {
        return $this->belongsTo(WechatUser::class, 'unionid', 'unionid');
    }
}
