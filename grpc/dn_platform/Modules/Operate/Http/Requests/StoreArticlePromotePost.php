<?php

namespace Modules\Operate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Operate\Entities\ArticlePromote;

class StoreArticlePromotePost extends FormRequest
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
            'title' => 'required|string|max:190',
            'article_id' => 'required|integer',
            'image_id' => 'required|integer|exists:files,id',
            'name' => 'string|max:100',
            'category' => 'required|integer|in:1,2',
            'wechat_number' => [
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $other = ArticlePromote::select('id', 'article_id', 'category', $attribute)
                        ->where('article_id', $this->article_id)
                        ->when($this->route('promote'), function ($query) {
                            $query->whereNotIn('id', [$this->route('promote')->id]);
                        })
                        ->get();

                    if ($other->isNotEmpty() && ! $other->contains('category', $this->category)) {
                        return $fail('本软文的大类型已经添加过了，不可以更改类型！');
                    }

                    if ($other->contains($attribute, $value)) {
                        return $fail('本软文Id对应微信号已经存在了！');
                    }
                }
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
            'title.required' => '软文标题必须填写',
            'category.required' => '类型必须选择',
            'article_id.required' => '软文编号必须填写',
            'image_id.required' => '添加二维码必须传',
            'image_id.exists' => '图片不存在',
            'status.required' => '生效状态必选选择',
            'category.required' => '类型必须选择',
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
