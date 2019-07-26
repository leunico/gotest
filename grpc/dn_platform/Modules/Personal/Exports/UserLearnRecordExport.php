<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use function App\formatSecond;

class UserLearnRecordExport implements FromCollection
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
            ['序号', '用户名', '姓名', '手机号', '年级', '性别', '分类', '学习总时长', '最近学习时间', '最近学习时长'],
        ];

        foreach ($this->data as $item) {
            $cell = [
                $item['id'],
                $item['name'],
                $item['real_name'],
                $item['phone'],
                User::$gradeMap[$item['grade']] ?? '',
                User::$sexMap[$item['sex']] ?? '',
                (bool) $item['userCategory']->count() ? '付费用户' : '非付费用户',
                formatSecond((int) floor($item['learnRecords']->pluck('duration')->sum() / 1000)),
                $item['learnRecords']->isEmpty() ? '' : $item['learnRecords']->last()->entry_at->format('Y-m-d H:i:s'),
                $item['learnRecords']->isEmpty() ? formatSecond(0) : formatSecond((int) floor($item['learnRecords']->last()->duration / 1000)),
            ];

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
