<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\User;

class LearnRecordExport implements FromCollection
{
    protected $data;

    protected $grades;

    public function __construct($data, $grades)
    {
        $this->data = $data;
        $this->grades = $grades;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $title = ['日期'];

        foreach ($this->grades as $grade) {
            $title[] = User::$gradeMap[$grade];
        }

        $title[] = '总人数';

        $cellData[] = $title;

        foreach ($this->data as $date => $item) {
            $cell = [
                $date,
            ];

            $sum = 0;

            foreach ($item as $value) {
                $cell[] = $value;
                $sum += $value;
            }

            $cell[] = $sum;

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
