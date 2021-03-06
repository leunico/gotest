<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Requests;

use App\Http\Requests\BaseRequest;
use Modules\Personal\Models\Homeworks;
use Illuminate\Validation\Rule;

class HomeworksSubmitRequest extends BaseRequest
{
    private $types = [
        Homeworks::TYPE_WORK,
        Homeworks::TYPE_IMAGE,
        Homeworks::TYPE_VIDEO,
    ];

    public function rules(): array
    {
        return [
            'course_id' => [
                'required', 'integer',
            ],
            'chapter_id' => [
                'required', 'integer',
            ],
            'lesson_id' => [
                'required', 'integer',
            ],
            'type' => [
                'required', Rule::in($this->types),
            ],
            'entry_id' => [
                'require', 'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => '必填',
            'course_id.integer' => '类型非法',
            'chapter_id.required' => '必填',
            'chapter_id.integer' => '类型非法',
            'lesson_id.required' => '必填',
            'lesson_id.integer' => '类型非法',
            'type.required' => '必填',
            'type.in' => '输入非法',
            'entry_id.required' => '必填',
            'entry_id.string' => '类型非法',
        ];
    }
}
