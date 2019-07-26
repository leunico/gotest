<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Modules\Personal\Http\Requests\StatisticsRequest;
use Illuminate\Support\Carbon;
use Modules\Personal\Entities\LoginLog;
use Modules\Personal\Entities\Work;
use App\User;
use Modules\Personal\Entities\LearnRecord;
use Modules\Personal\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Personal\Exports\UserLoginExport;
use Modules\Personal\Exports\HomeworkExport;
use Modules\Personal\Exports\LearnRecordExport;
use Modules\Personal\Exports\UserAllExport;
use Modules\Personal\Exports\UserAllDetailExport;
use Modules\Personal\Http\Repositories\UserAllDetailRepository;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    private $expireSecond = 600;

    /**
     * @var \Modules\Personal\Http\Repositories\UserAllDetailRepository
     */
    private $userAllDetailRepository;

    public function __construct(UserAllDetailRepository $userAllDetailRepository)
    {
        $this->userAllDetailRepository = $userAllDetailRepository;
    }

    /**
     * 用户登录数据统计
     *
     * @param \Modules\Personal\Http\Requests\StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userLogin(StatisticsRequest $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::parse('-6 days')->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($startDate->gt($endDate)) {
            return $this->response()->error('非法输入');
        }

        $logs = LoginLog::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $data = [];

        while ($startDate->lte($endDate)) {
            $date = $startDate->format('Y-m-d');
            $data[$date] = collect($logs->get($date, []))->unique('user_id')->values()->count();

            $startDate->addDay();
        }

        if ($request->has('export')) {
            return Excel::download(new UserLoginExport($data), "用户登录数据统计-{$startDate}至{$endDate}.xlsx");
        }

        return $this->response()->success($data);
    }

    /**
     * 交作业数据统计
     *
     * @param \Modules\Personal\Http\Requests\StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function homework(StatisticsRequest $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::parse('-6 days')->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($startDate->gt($endDate)) {
            return $this->response()->error('非法输入');
        }

        $works = Work::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $data = [];

        while ($startDate->lte($endDate)) {
            $date = $startDate->format('Y-m-d');
            $data[$date] = collect($works->get($date, []))->unique('user_id')->values()->count();

            $startDate->addDay();
        }

        if ($request->has('export')) {
            return Excel::download(new HomeworkExport($data), "交作业数据统计-{$startDate}至{$endDate}.xlsx");
        }

        return $this->response()->success($data);
    }

    /**
     * 用户学习数据统计
     *
     * @param \Modules\Personal\Http\Requests\StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function learnRecord(StatisticsRequest $request)
    {
        $grades = array_keys(User::$gradeMap);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::parse('-6 days')->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $startGrade = $request->start_grade !== null ? $request->start_grade : array_first($grades);
        $endGrade = $request->end_grade !== null ? $request->end_grade : array_last($grades);

        if ($startDate->gt($endDate)) {
            return $this->response()->error('非法输入');
        }

        if (!in_array($startGrade, $grades) || !in_array($endGrade, $grades)) {
            return $this->response()->error('非法输入');
        }

        $validGrades = [];

        for ($i = array_first($grades); $i <= array_last($grades); ++$i) {
            if ($i >= $startGrade && $i <= $endGrade && in_array($i, $grades)) {
                $validGrades[] = $i;
            }
        }

        $learnRecords = LearnRecord::leftJoin('users', 'users.id', '=', 'learn_records.user_id')
            ->whereBetween('learn_records.entry_at', [$startDate, $endDate])
            ->whereBetween('users.grade', [$startGrade, $endGrade])
            ->get(['learn_records.*', 'users.grade'])
            ->groupBy(function ($item) {
                return $item->entry_at->format('Y-m-d');
            });

        $dataList = [];

        while ($startDate->lte($endDate)) {
            $date = $startDate->format('Y-m-d');

            $data = collect($learnRecords->get($date, []))->unique('user_id')->values();

            $tmpData = [];

            // 拼接年级
            for ($i = 0; $i < count($validGrades); ++$i) {
                $tmpData[$validGrades[$i]] = $data->where('grade', $validGrades[$i])->count();
            }

            $dataList[$date] = (object) $tmpData;

            $startDate->addDay();
        }

        if ($request->has('export')) {
            return Excel::download(new LearnRecordExport($dataList, $validGrades), "用户学习数据统计-{$startDate}至{$endDate}.xlsx");
        }

        return $this->response()->success($dataList);
    }

    /**
     * 用户数据统计总表
     *
     * @param \Modules\Personal\Http\Requests\StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userAll(StatisticsRequest $request)
    {
        $grades = array_keys(User::$gradeMap);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::parse('-6 days')->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        $key = "user_all:{$startDate}:{$endDate}";

        if (($users = Cache::get($key)) === null) {
            $users = User::orderBy('grade', 'asc')->get()->groupBy('grade');
            Cache::set($key, $users, Carbon::now()->addSeconds($this->expireSecond));
        }

        $data = [];

        foreach ($grades as $index => $grade) {
            $userIds = collect($users->get($grade, []))->pluck('id');

            if ($userIds->isEmpty()) {
                $data[] = [
                    'id' => $index + 1,
                    'grade' => $grade,
                    'title' => User::$gradeMap[$grade],
                    'work_count' => 0,
                    'learn_count' => 0,
                    'user_count' => 0,
                ];
                continue;
            }

            $works = Work::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->whereIn('user_id', $userIds)
                ->get();

            $learnRecords = LearnRecord::whereBetween('entry_at', [$startDate, $endDate])
                ->whereIn('user_id', $userIds)
                ->get();

            $data[] = [
                'id' => $index + 1,
                'grade' => $grade,
                'title' => User::$gradeMap[$grade],
                'work_count' => $works->count(),
                'learn_count' => $learnRecords->count(),
                'user_count' => $works->pluck('user_id')->merge($learnRecords->pluck('user_id'))->unique()->values()->count(),
            ];
        }

        $data = collect($data);

        if ($request->has('sort')) {
            $sorts = explode(',', $request->sort);

            if (count($sorts) === 2 &&
                in_array(array_first($sorts), ['learn_count', 'work_count']) &&
                in_array(strtolower(array_last($sorts)), ['asc', 'desc'])) {
                if (array_last($sorts) === 'asc') {
                    $data = $data->sortBy(array_first($sorts))->values();
                } else {
                    $data = $data->sortByDesc(array_first($sorts))->values();
                }
            }
        }

        if ($request->has('export')) {
            return Excel::download(new UserAllExport($data), "用户数据统计总表-{$startDate}至{$endDate}.xlsx");
        }

        return $this->response()->success($data);
    }

    /**
     * 用户数据统计总表详情
     *
     * @param \Modules\Personal\Http\Requests\StatisticsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userAllDetail(StatisticsRequest $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        if ($startDate->gt($endDate)) {
            return $this->response()->error('非法输入');
        }

        $key = "user_all_detail:{$request->grade}:{$startDate}:{$endDate}";

        if (($userIds = Cache::get($key)) === null) {
            // 查出这段时间有提交作品和学习记录的用户
            $hasWorkUserIds = Work::leftJoin('users', 'users.id', '=', 'works.user_id')
                ->whereBetween('works.created_at', [$startDate, $endDate])
                ->where('works.status', 1)
                ->where('users.grade', (int) $request->grade)
                ->select('works.user_id')
                ->distinct()
                ->pluck('user_id');

            $hasLearnRecordUserIds = LearnRecord::leftJoin('users', 'users.id', '=', 'learn_records.user_id')
                ->whereBetween('learn_records.entry_at', [$startDate, $endDate])
                ->select('learn_records.user_id')
                ->where('users.grade', (int) $request->grade)
                ->distinct()
                ->pluck('user_id');

            $userIds = $hasWorkUserIds->merge($hasLearnRecordUserIds)->unique()->values();

            Cache::set($key, $userIds, Carbon::now()->addSeconds($this->expireSecond));
        }

        $query = $this->userAllDetailRepository
            ->sex($request->sex !== null ? (int) $request->sex : null)
            ->keyword($request->keyword)
            ->whereIn('users.id', $userIds)
            ->with([
                'channel',
                'works' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 1);
                },
                'learnRecords' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('entry_at', [$startDate, $endDate]);
                },
                'userCategory',
            ])
            ->select([
                'users.*',
            ])
            ->distinct();

        if ($request->has('export')) {
            $users = $query->get();

            return Excel::download(new UserAllDetailExport($users), '用户数据统计总表详情.xlsx');
        } else {
            $users = $query->paginate($request->per_page, DB::raw('`users`.`id`'));
        }

        return $this->response()->paginator($users, UserResource::class);
    }
}
