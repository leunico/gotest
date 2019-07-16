<?php

namespace Modules\Examinee\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ExamineeTemplateExport implements FromCollection
{
    /**
     * 数据集
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    // protected $collects;

    public function __construct()
    {
        // $this->collects = $collects;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $cellData = [[
            '姓名',
            '性别',
            '证件类型',
            '证件号码',
            '出生日期',
            '家长姓名',
            '联系手机',
            '联系邮箱',
            '正面免冠2寸彩照',
            '证件照（正）',
            '证件照（反）',
            '考试事务咨询联络老师'
        ], [
            '小明',
            '男/女',
            '身份证/护照',
            '3607821993044558882',
            '2000/12/13',
            '李先生',
            '13588888888',
            '13588888888@qq.com',
            '链接',
            '链接',
            '链接',
            '文本描述',
            '（示例数据，请删除！）',
        ]];

        return collect($cellData);
    }
}
