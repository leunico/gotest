<?php

namespace Modules\Personal\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class UserAllExport implements FromCollection
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
            ['序号', '年级', '发布作品数量', '学习数量'],
        ];

        foreach ($this->data as $item) {
            $cell = [
                $item['id'],
                $item['title'],
                $item['work_count'],
                $item['learn_count'],
            ];

            $cellData[] = $cell;
        }

        return collect($cellData);
    }
}
