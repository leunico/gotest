<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use function App\formatSecond;

class CourseLessonLearnRecordExport implements FromCollection
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
            ['序号', '系列课', '主题', '学习总时长'],
        ];

        foreach ($this->data as $index => $item) {
            $cell = [
                $index + 1,
                $item['course_title'],
                $item['title'],
                formatSecond((int)$item->sections->reduce(function ($total, $item) {
                    return $total + (int)floor($item->learnRecords->pluck('duration')->sum());
                }) / 1000),
            ];

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
