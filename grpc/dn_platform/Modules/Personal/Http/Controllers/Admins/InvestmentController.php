<?php

namespace Modules\Personal\Http\Controllers\Admins;

use function App\formatSecond;
use function App\responseFailed;
use function App\responseSuccess;
use App\Rules\ArrayExists;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Course\Entities\Course;
use Modules\Personal\Entities\Investment;
use Modules\Personal\Entities\LearnRecord;
use Modules\Personal\Exports\InvestmentExport;
use Modules\Personal\Http\Requests\InvestmentRequest;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $perPage = $request->per_page ?? 15;
        $export = $request->input('export');

        $query = Investment::when(!empty($keyword), function ($query) use ($keyword) {
            $userIds = User::name($keyword)->pluck('id');
            $query =  $query->where(function ($s) use ($userIds, $keyword) {
                $s->whereIn('user_id', $userIds)->orWhere('name', 'like', "%$keyword%");
            });

            return $query;
        })->with('user','creator');

        if (empty($export)) {
            $investments = $query->paginate($perPage);
        } else {
            $investments = $query->get();
        }
        $userIds = $investments->pluck('user_id');

        //获取学习总时长，最后学习时间
        $learnRecords = LearnRecord::whereIn('user_id', $userIds)
            ->select(DB::raw('SUM(duration) as total_duration, max(entry_at) as last_study_at, user_id'))
            ->groupBy('user_id')->get();

        //获取最后学习时长
        $learnDurationGroup = DB::table('learn_records')
            ->select(DB::raw('max(id) as id, user_id'))
            ->groupBy('user_id');

        $lastLearnDurations = DB::table('learn_records')
            ->joinSub($learnDurationGroup, 'b', function ($join) {
                $join->on('learn_records.id', '=', 'b.id');
            })->whereIn('learn_records.user_id', $userIds)
            ->get();

        //获取观看课程
        $learn_courses = DB::table('learn_records as lr')
            ->leftJoin('course_sections as cs', function ($q) {
                $q->on('cs.id', '=', 'lr.section_id')->whereNull('cs.deleted_at');
            })->leftJoin('course_lessons as cl', function ($q) {
                $q->on('cl.id', '=', 'cs.course_lesson_id')->whereNull('cl.deleted_at');
            })->leftJoin('courses', function ($q) {
                $q->on('courses.id', '=', 'cl.course_id')->whereNull('courses.deleted_at');
            })->select('user_id', 'course_id','courses.title')
            ->groupBy('user_id')->groupBy('course_id')
            ->get();

        $investments->map(function ($item) use ($learnRecords, $lastLearnDurations, $learn_courses) {
            $learnRecord = $learnRecords->first(function ($learn) use ($item) {
                return $learn->user_id == $item->user_id;
            });

            $item->course = implode('/', $learn_courses->where('user_id', $item->user_id)->pluck('title')->toArray());

            $lastLearnDuration = $lastLearnDurations->first(function ($lastLearn) use ($item) {
                return $item->user_id == $lastLearn->user_id;
            });

            $item->study_total_duration = formatSecond($learnRecord ? (int) floor($learnRecord->total_duration / 1000) : 0);
            $item->last_study_at = $learnRecord ? $learnRecord->last_study_at : null;
            $item->last_study_duration = formatSecond($lastLearnDuration ? (int) floor($lastLearnDuration->duration / 1000) : 0);

            return $item;
        });
        if (empty($export)) {
            return responseSuccess($investments);
        } else{
            return Excel::download(new InvestmentExport($investments), '投资机构数据.xlsx');
        }
    }

    public function store(InvestmentRequest $request)
    {
        try {
            DB::beginTransaction();
            // create user
            $user = new User();
            $user->name = $request->username;
            $user->phone = $request->mobile ?? null;
            $user->password = bcrypt($request->password);
            $user->creator_id = Auth::user()->id;
            $user->real_name = $request->username;
            $user->save();

            $user->givePermissionTo('course-unlock-no-limit');


            $investment = new Investment();
            $investment->user_id = $user->id;
            $investment->password = $request->password;
            $investment->name = $request->name;
            $investment->creator_id = Auth::id();
            $investment->remark = $request->remark ?? '';
            $investment->save();
            DB::commit();

            return responseSuccess(['investment_id' => $investment->id]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return responseFailed($exception->getMessage());
        }
    }

    public function update(InvestmentRequest $request, Investment $investment)
    {
        try {
            DB::beginTransaction();
            $investment->name = $request->name;
            $investment->password = $request->password;
            $investment->remark = $request->remark;
            $investment->save();

            $user = $investment->user;
            $user->name = $request->username;
            $user->password = bcrypt($request->password);
            $user->phone = $request->mobile ?? null;
            $user->save();

            $user->givePermissionTo('course-unlock-no-limit');
            DB::commit();

            return responseSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed($exception->getMessage());
        }
    }


    public function assignCourses(Request $request, Investment $investment)
    {
        $rules = [
            'course_ids' => [
                'required',
                'array',
                new ArrayExists(Course::where('status', 1)),
            ]
        ];

        $this->validate($request, $rules,[], ['course_ids' => '课程id']);


        $user = $investment->user;

        $courseData = [];
        foreach ($request->course_ids as $courseId) {
            $courseData[$courseId] = [
                'memo' => '投资机构',
                'creator_id' => Auth::id(),
                'status' => 1
            ];
        }

        $user->courses()->sync($courseData);

        return responseSuccess();
    }

    public function show(Investment $investment)
    {
        $investment->load(['user.courses' => function ($query) {
            $query->select('courses.id', 'title', 'category');
        }],'creator');

        return responseSuccess($investment);
    }
}
