<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class UserLoginExport implements FromCollection
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
            ['日期', '人数'],
        ];

        foreach ($this->data as $date => $count) {
            $cell = [
                $date,
                $count,
            ];

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
