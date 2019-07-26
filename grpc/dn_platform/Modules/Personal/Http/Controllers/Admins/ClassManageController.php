<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Personal\Http\Repositories\ClassLearnRecordRepository;
use Modules\Personal\Http\Requests\ClassLearnRecordRequest;
use Modules\Personal\Http\Resources\StudyClassResource;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Personal\Exports\ClassLearnRecordExport;
use Modules\Personal\Http\Requests\ClassLearnRecordDetailRequest;
use App\User;
use Modules\Educational\Entities\StudyClass;
use Modules\Personal\Http\Repositories\ClassLearnRecordDetailRepository;
use Modules\Personal\Http\Resources\LearnRecordsResource;
use Modules\Personal\Exports\UserLearnRecordDetailExport;

class ClassManageController extends Controller
{
    /**
     * @var \Modules\Personal\Http\Repositories\ClassLearnRecordRepository
     */
    private $classLearnRecordRepository;

    /**
     * @var \Modules\Personal\Http\Repositories\ClassLearnRecordDetailRepository
     */
    private $classLearnRecordDetailRepository;



    public function __construct(ClassLearnRecordRepository $classLearnRecordRepository, ClassLearnRecordDetailRepository $classLearnRecordDetailRepository)
    {
        $this->classLearnRecordRepository = $classLearnRecordRepository;
        $this->classLearnRecordDetailRepository = $classLearnRecordDetailRepository;
    }

    /**
     * 班级观看录播课数据
     *
     * @param \Modules\Personal\Http\Requests\ClassLearnRecordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function learnRecord(ClassLearnRecordRequest $request)
    {
        $query = $this->classLearnRecordRepository
            ->date($request->start_date, $request->end_date)
            ->keyword($request->keyword)
            ->teacher((int) $request->teacher_id)
            ->with([
                'teacher',
                'students.learnRecords',
            ])
            ->withCount([
                'students'
            ]);

        if ($request->has('export')) {
            $data = $query->get();
            return Excel::download(new ClassLearnRecordExport($data), '班级观看录播课数据.xlsx');
        } else {
            $data = $query->paginate($request->per_page);
        }

        return $this->response()->paginator($data, StudyClassResource::class);
    }

    /**
     * 班级观看录播课详情
     *
     * @param \Modules\Personal\Http\Requests\ClassLearnRecordDetailRequest $request
     * @param \Modules\Educational\Entities\StudyClass $studyClass
     * @return \Illuminate\Http\JsonResponse
     */
    public function learnRecordDetail(ClassLearnRecordDetailRequest $request, StudyClass $studyClass)
    {
        if ($studyClass->big_course_id) {
            $courseIds = $studyClass->bigCourse->courses->pluck('id');
        } else {
            $courseIds = collect([$studyClass->course_id]);
        }

        $query = $this->classLearnRecordDetailRepository
            ->courseCategory($request->course_category !== null ? (int) $request->course_category : null)
            ->date($request->start_date, $request->end_date)
            ->select(['learn_records.*'])
            // ->where('entry_at', '>=', $studyClass->created_at)
            ->whereIn('courses.id', $courseIds)
            ->with(['courseSection.courseLesson.course', 'user'])
            ->whereIn('learn_records.user_id', $studyClass->students->pluck('id'))
            ->orderBy('id', 'DESC');

        if ($request->has('export')) {
            $data = $query->get();
            return Excel::download(new UserLearnRecordDetailExport($data), '班级观看录播课详情.xlsx');
        } else {
            $data = $query->paginate($request->per_page);
        }

        return $this->response()->paginator($data, LearnRecordsResource::class);
    }
}
