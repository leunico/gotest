<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;

class WechatTemplate extends Model
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
        'tpl_id',
        'title',
        'content',
        'category',
        'useful'
    ];

    /**
     * 转换模板参数。
     *
     * @return array
     */
    public function getContentAttribute($value)
    {
        $data = [];
        preg_match_all('/\{\{(\S*)\.DATA\}\}/', $value, $fields);
        preg_match_all('/(.*?)：\{\{(.*?).DATA}\}/', $value, $keys);
        if (isset($fields[1]) && isset($keys[1]) && isset($keys[2])) {
            $keys[2] = array_flip($keys[2]);
            foreach ($fields[1] as $k => $item) {
                $data[$item] = in_array($item, ['first', 'remark']) ?
                    ($item == 'first' ? '标题' : '备注') :
                    (isset($keys[2][$item]) ? $keys[1][$keys[2][$item]] : '默认内容');
            }
        }

        return $data;
    }
}
