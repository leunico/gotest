<?php

namespace Modules\Operate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ArrayExists;
use Modules\Operate\Entities\WechatUserTag;

class StoreOfficialAccountPost extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author lizx
     */
    public function rules()
    {
        $rules = [
            'push_at' => 'required|date',
            'url' => 'required|url',
            'tpl_params' => 'required|array',
            'wechat_template_id' => 'required|integer|exists:wechat_templates,id',
            'category' => 'required|in:1,2',
            'tags' => [
                'required',
                'array',
                new ArrayExists(new WechatUserTag)
            ]
        ];

        return $rules;
    }

    /**
     * Get rule messages.
     *
     * @return array
     * @author lizx
     */
    public function messages()
    {
        return [
            'push_at.required' => '推送时间必须选',
            'push_at.date' => '推送时间必须传时间格式',
            'url.required' => '跳转链接是必须的',
            'url.url' => '不是标准的链接',
            'tpl_params.required' => '缺少模板参数',
            'wechat_template_id.exists' => '不存在的微信模板',
            'tags.required' => '推送的标签用户需选择',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
