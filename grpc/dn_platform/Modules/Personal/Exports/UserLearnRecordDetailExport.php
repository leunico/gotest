<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Course\Entities\Course;
use function App\formatSecond;

class UserLearnRecordDetailExport implements FromCollection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $cellData = [
            ['序号', '课程类型', '系列', '主题', '环节', '进入时间', '离开时间', '学习时长'],
        ];

        foreach ($this->data as $item) {
            $cell = [
                $item['id'],
                Course::$courseMap[$item['courseSection']['courseLesson']['course']['category']] ?? '',
                $item['courseSection']['courseLesson']['course']['title'],
                $item['courseSection']['courseLesson']['title'],
                $item['courseSection']['title'],
                $item['entry_at']->format('Y-m-d H:i:s'),
                $item['leave_at']->format('Y-m-d H:i:s'),
                formatSecond((int) floor($item['duration'] / 1000)),
            ];

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
