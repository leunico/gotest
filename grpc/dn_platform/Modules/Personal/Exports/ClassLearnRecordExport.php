<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use function App\formatSecond;

class ClassLearnRecordExport implements FromCollection
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
            ['序号', '班级名称', '班级ID', '班级人数', '班主任', '开班时间', '学习总时长'],
        ];

        foreach ($this->data as $index => $item) {
            $cell = [
                $index + 1,
                $item['name'],
                $item['id'],
                $item['students_count'],
                $item['teacher']->real_name,
                $item['entry_at'],
                formatSecond((int) $item['students']->reduce(function ($total, $item) {
                    return $total + (int) floor($item->learnRecords->pluck('duration')->sum());
                })/ 1000),
            ];

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
