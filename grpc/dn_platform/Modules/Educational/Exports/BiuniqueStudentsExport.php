<?php

namespace Modules\Educational\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\User;

class BiuniqueStudentsExport implements FromCollection
{
    /**
     * 数据集
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $collects;

    public function __construct(Collection $collects)
    {
        $this->collects = $collects;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $values = [['序号', '用户名', '姓名', '手机号', '性别', '年级', '剩余星星', '已消耗星星']];
        $this->collects->map(function ($item) use (&$values) {
            $val[0] = $item->user_id;
            $val[1] = $item->name;
            $val[2] = $item->real_name;
            $val[3] = $item->phone;
            $val[4] = isset(User::$sexMap[$item->sex]) ? User::$sexMap[$item->sex] : '-';
            $val[5] = isset(User::$gradeMap[$item->grade]) ? User::$gradeMap[$item->grade] : '-';
            $val[6] = $item->star_amount;
            $val[7] = $item->appointment_stars;
        });

        return collect($values);
    }
}
