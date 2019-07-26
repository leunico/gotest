<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Personal\Entities\Conversation;

class ConversationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => [
                'string', 'max:16777215',
            ],
            'conversation_at' => [
                'required', 'date_format:"Y-m-d H:i:s"',
            ],
            'type' => [
                'required', Rule::in(array_keys(Conversation::$conversationMap)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'conversation_at.required' => '必填',
            'conversation_at.date_format' => '输入非法',
            'type.required' => '必填',
            'type.in' => '输入非法',
            'content.required' => '必填',
            'content.max' => '内容过长',
        ];
    }
}
