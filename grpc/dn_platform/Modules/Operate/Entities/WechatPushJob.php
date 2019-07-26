<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;

class WechatPushJob extends Model
{
    const CATEGORY_ART = 1;
    const CATEGORY_MUSIC = 2;
    const IS_PUSH_UNTEMPLATE = 3;
    const IS_PUSH_UNOPENID = 2;
    const IS_PUSH_OK = 1;
    const IS_PUSH_NO = 0;
    const CATEGORYS = [
        'art' => 1,
        'music' => 2
    ];

    protected $fillable = [];

    protected $casts = [
        'tpl_params' => 'json'
    ];

    public static $pushStatusMap = [
        '0' => '待推送',
        '1' => '已推送',
        '2' => '没有推送用户',
        '3' => '模板错误',
    ];

    /**
     * 获取推送状态。
     *
     * @return string
     */
    public function getStrPushAttribute()
    {
        return isset(self::$pushStatusMap[$this->is_push]) ? self::$pushStatusMap[$this->is_push] : '-';
    }

    public function getStrCategory()
    {
        $categorys = array_flip(self::CATEGORYS);

        return isset($categorys[$this->category]) ? $categorys[$this->category] : '';
    }

    public function isArt()
    {
        return $this->category === self::CATEGORY_ART;
    }

    public function isMusic()
    {
        return $this->category === self::CATEGORY_MUSIC;
    }

    public function tags()
    {
        return $this->belongsToMany(WechatUserTag::class, 'wechat_push_job_tags', 'wechat_push_job_id', 'tag_id');
    }

    public function template()
    {
        return $this->belongsTo(WechatTemplate::class, 'wechat_template_id');
    }

    public function tagsPivot()
    {
        return $this->hasMany(WechatPushJobTag::class);
    }

    public function wechatPushUserLogs()
    {
        return $this->hasMany(WechatPushUserLog::class);
    }
}
