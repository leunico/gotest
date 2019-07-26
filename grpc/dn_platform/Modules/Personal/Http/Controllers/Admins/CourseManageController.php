<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Modules\Personal\Http\Resources\CourseResource;
use Modules\Course\Entities\Course;
use Illuminate\Support\Facades\DB;
use Modules\Personal\Http\Repositories\CourseLessonLearnRecordRepository;
use Modules\Personal\Http\Requests\CourseLessonLearnRecordRequest;
use function App\formatSecond;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Personal\Exports\CourseLessonLearnRecordExport;

class CourseManageController extends Controller
{
    /**
     * @var \Modules\Personal\Http\Repositories\CourseLessonLearnRecordRepository
     */
    private $courseLessonLearnRecordRepository;

    public function __construct(CourseLessonLearnRecordRepository $courseLessonLearnRecordRepository)
    {
        $this->courseLessonLearnRecordRepository = $courseLessonLearnRecordRepository;
    }

    /**
     * 课程观看录播课数据
     *
     * @param \Modules\Personal\Http\Requests\CourseLessonLearnRecordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function learnRecord(CourseLessonLearnRecordRequest $request)
    {
        $query = $this->courseLessonLearnRecordRepository
            ->category((int) $request->category)
            ->keyword($request->keyword)
            ->with([
                'sections.learnRecords',
            ])
            ->distinct()
            ->select([
                DB::raw('`courses`.`title` AS course_title'),
                'course_lessons.*'
            ]);

        if ($request->has('export')) {
            $data = $query->get();

            return Excel::download(new CourseLessonLearnRecordExport($data), '课程观看录播课数据.xlsx');
        } else {
            $data = $query->paginate($request->per_page);
        }

        $list = $data->getCollection();
        foreach ($list as $index => $item) {
            $list[$index]->learn_records_total = formatSecond((int)$item->sections->reduce(function ($total, $item) {
                return $total + (int)floor($item->learnRecords->pluck('duration')->sum());
            }) / 1000);
            unset($list[$index]->sections);
        }

        $total = 0;
        foreach ($query->get() as $item) {
            $total = ((int)$item->sections->reduce(function ($total, $item) {
                return $total + (int)floor($item->learnRecords->pluck('duration')->sum());
            }) / 1000) + $total;
        }

        $response = [
            'pagination' => [
                'total' => (int) $data->total(),
                'current_page' => (int) $data->currentPage(),
                'last_page' => (int) $data->lastPage(),
                'per_page' => (int) $data->perPage(),
            ],
            'list' => $data->getCollection(),
            'list_total' => formatSecond($total)
        ];

        return $this->response()->success($response);
    }
}
