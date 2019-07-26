<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;

class UserAllDetailExport implements FromCollection
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
            ['序号', '用户名', '姓名', '手机号', '年级', '性别', '来源', '分类', '登陆次数', '作品数量', '最近学习时间'],
        ];

        foreach ($this->data as $item) {
            $cell = [
                $item['id'],
                $item['name'],
                $item['real_name'],
                $item['phone'],
                User::$gradeMap[$item['grade']] ?? '',
                User::$sexMap[$item['sex']] ?? '',
                $item['channel'] ? $item['channel']['title'] : '',
                $item['user_category'] ? '付费用户' : '非付费用户',
                $item['login_count'],
                count($item['works']),
                $item['learnRecords']->isEmpty() ? '' : $item['learnRecords']->last()->entry_at->format('Y-m-d H:i:s'),
            ];

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
