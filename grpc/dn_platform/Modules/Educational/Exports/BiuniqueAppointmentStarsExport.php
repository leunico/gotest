<?php

namespace Modules\Educational\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Modules\Course\Entities\BiuniqueCourse;

class BiuniqueAppointmentStarsExport implements FromCollection
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
        $values = [['序号', '课程类型', '课程名称', '课时名称', '上课老师', '上课时间', '消耗星星数量']];
        $this->collects->map(function ($item) use (&$values) {
            $val[0] = $item->id;
            $val[1] = isset(BiuniqueCourse::$categoryMap[$item->biuniqueCourse->category]) ? BiuniqueCourse::$categoryMap[$item->biuniqueCourse->category] : '-';
            $val[2] = $item->biuniqueCourse->title;
            $val[3] = $item->biuniqueLesson ? $item->biuniqueLesson->title : '';
            $val[4] = $item->teacher_name;
            $val[5] = $item->appointment_date;
            $val[6] = $item->star_cost;
            $values[] = $val;
        });

        return collect($values);
    }
}
