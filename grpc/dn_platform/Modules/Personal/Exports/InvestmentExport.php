<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;
use function App\formatSecond;

class InvestmentExport implements FromCollection
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

        $exportData = [
            ['投资机构', '用户名' , '手机号' , '备注' , '创建人', '最近一次登陆时间', '学习总时长', '观看课程', '最近一次学习时间', '最近一次学习时长']
        ];

        foreach ($this->data as $item) {
            $exportData[] = [
                $item->name,
                $item->user->name,
                $item->user->mobile,
                $item->remark,
                $item->creator->name,
                $item->user->last_login_at,
                $item->study_total_duration,
                $item->course,
                $item->last_study_at,
                $item->last_study_duration
            ];
        }

        return collect($exportData);
    }
}
