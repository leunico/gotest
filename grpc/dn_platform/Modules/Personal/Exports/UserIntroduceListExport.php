<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Collection;

class UserIntroduceListExport implements FromCollection
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
        $values = [['序号', '用户名', '姓名', '手机号码', '是否付费用户', '拥有主题数量', '备注']];
        $this->collects->map(function ($item) use (&$values) {
            $val[0] = $item->id;
            $val[1] = $item->name;
            $val[2] = $item->real_name;
            $val[3] = $item->phone;
            $val[4] = empty($item->orders_count) ? '否' : '是';
            $val[5] = $item->courseUsers->pluck('lessons')->flatten()->count();
            $val[6] = $item->remark;
            $values[] = $val;
        });

        return collect($values);
    }
}
