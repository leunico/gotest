<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use function App\responseSuccess;
use Illuminate\Http\JsonResponse;
use Modules\Personal\Events\ChangeUser;
use Modules\Personal\Http\Requests\UserManageRequest;
use Modules\Personal\Http\Repositories\UserManageRepository;
use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Modules\Personal\Entities\Work;
use Modules\Personal\Http\Requests\WorksListRequest;
use Modules\Personal\Http\Resources\WorksResource;
use Modules\Personal\Http\Repositories\CourseRepository;
use Modules\Personal\Http\Requests\CourseListRequest;
use Modules\Personal\Http\Resources\CourseResource;
use function App\formatSecond;
use Modules\Personal\Http\Requests\OrderListRequest;
use Modules\Personal\Http\Repositories\OrderRepository;
use Modules\Personal\Http\Resources\OrderResource;
use Modules\Personal\Http\Resources\UserResource;
use Illuminate\Support\Carbon;
use Modules\Personal\Http\Repositories\UserLearnRecordRepository;
use Modules\Personal\Http\Requests\UserLearnRecordRequest;
use Illuminate\Support\Facades\DB;
use Modules\Personal\Http\Requests\UserLearnRecordDetailRequest;
use Modules\Personal\Http\Repositories\UserLearnRecordDetailRepository;
use Modules\Personal\Http\Resources\LearnRecordsResource;
use Modules\Operate\Entities\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Personal\Exports\UserLearnRecordExport;
use Modules\Personal\Exports\UserLearnRecordDetailExport;
use Modules\Personal\Entities\Conversation;
use Modules\Personal\Http\Resources\ConversationResource;
use Modules\Personal\Http\Requests\ConversationRequest;
use Mews\Purifier\Facades\Purifier;
use App\Rules\ArrayExists;
use Spatie\Permission\Models\Permission;
use Modules\Personal\Entities\Delivery;
use Modules\Personal\Http\Resources\DeliveryResource;
use Modules\Course\Entities\Course;
use Modules\Personal\Entities\LearnRecord;

class UserManageController extends Controller
{
    /**
     * @var \Modules\Personal\Http\Repositories\UserManageRepository
     */
    private $userManageRepository;

    /**
     * @var \Modules\Personal\Http\Repositories\CourseRepository
     */
    private $courseRepository;

    /**
     * @var \Modules\Personal\Http\Repositories\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Modules\Personal\Http\Repositories\UserLearnRecordRepository
     */
    private $userLearnRecordRepository;

    /**
     * @var \Modules\Personal\Http\Repositories\UserLearnRecordDetailRepository
     */
    private $userLearnRecordDetailRepository;

    public function __construct(UserManageRepository $userManageRepository, CourseRepository $courseRepository, OrderRepository $orderRepository, UserLearnRecordRepository $userLearnRecordRepository, UserLearnRecordDetailRepository $userLearnRecordDetailRepository)
    {
        $this->userManageRepository = $userManageRepository;
        $this->courseRepository = $courseRepository;
        $this->orderRepository = $orderRepository;
        $this->userLearnRecordRepository = $userLearnRecordRepository;
        $this->userLearnRecordDetailRepository = $userLearnRecordDetailRepository;
    }

    /**
     * 首页列表
     *
     * @param  \Modules\Personal\Http\Requests\UserManageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserManageRequest $request): JsonResponse
    {
        $data = $this->userManageRepository
            ->grade($request->start_grade !== null ? (int) $request->start_grade : null, $request->end_grade !== null ? (int) $request->end_grade : null)
            ->sex($request->sex !== null ? (int) $request->sex : null)
            ->courseCategory($request->course_category)
            ->category($request->user_category !== null ? (int) $request->user_category : null)
            ->channel($request->channel !== null ? (int) $request->channel : null)
            ->date($request->start_date, $request->end_date)
            ->keyword($request->keyword)
            ->course($request->course_id)
            ->bigCourse($request->big_course_id)
            ->select(['users.*'])
            ->orderBy('users.created_at', 'DESC')
            ->distinct()
            ->with([
                'channel',
                'courseUsers.course',
                'userCategory',
            ])
            ->paginate($request->per_page, DB::raw('`users`.`id`'));

        return $this->response()->paginator($data, UserResource::class);
    }

    /**
     * 设置账号状态
     *
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(User $user): JsonResponse
    {
        $user->account_status = !$user->account_status;

        return $this->response()->success($user->save());
    }

    /**
     * 设置账号角色
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setRole(Request $request, User $user): JsonResponse
    {
        $this->validate($request, [
            'roles' => [
                'exists:roles,name',
            ],
        ]);

        $user->syncRoles($request->input('roles', []));

        return $this->response()->success();
    }

    /**
     * 获取账号权限
     *
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function premissions(User $user): JsonResponse
    {
        return $this->response()->success($user->getAllPermissions());
    }

    /**
     * 获取账号角色
     *
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function role(User $user): JsonResponse
    {
        $user->load([
            'roles' => function ($query) {
                $query->select('name', 'id', 'title');
            },
        ]);

        return $this->response()->success($user->roles->first());
    }

    /**
     * 设置账号权限
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPremissions(Request $request, User $user): JsonResponse
    {
        $this->validate($request, [
            'premissions' => [
                'array',
                new ArrayExists(new Permission()),
            ],
        ]);

        $user->syncPermissions($request->premissions);

        return $this->response()->success();
    }

    /**
     * 获取用户信息
     *
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function userinfo(User $user): JsonResponse
    {
        $user->load([
            'address.province',
            'address.city',
            'address.district',
            'channel',
        ]);

        return $this->response()->item($user, UserResource::class);
    }

    /**
     * 更新用户信息
     *
     * @param \App\Http\Requests\UserRequest $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function setUserInfo(UserRequest $request, User $user): JsonResponse
    {
        $userInfo = $request->all();

        if (isset($userInfo['password'])) {
            $userInfo['password'] = Hash::make($userInfo['password']);
        }

        $user->update($userInfo);

        event(new ChangeUser($user, 'update'));

        return $this->response()->success();
    }

    /**
     * 创建账号
     *
     * @param \App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(UserRequest $request): JsonResponse
    {
        $userInfo = $request->all();

        $userInfo['password'] = Hash::make($userInfo['password']);

        $user = User::create($userInfo);

        event(new ChangeUser($user, 'create'));

        return $this->response()->created('', $user);
    }

    /**
     * 作业管理列表
     *
     * @param \Modules\Personal\Http\Requests\WorksListRequest $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function work(WorksListRequest $request, User $user): JsonResponse
    {
        $query = Work::where('user_id', $user->getAuthIdentifier());

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->has('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->start_date)->endOfDay());
        }

        if ($request->has('sort')) {
            $sorts = explode(',', $request->sort);

            if (count($sorts) === 2 &&
                in_array(array_first($sorts), ['views', 'id', 'created_at']) &&
                in_array(strtolower(array_last($sorts)), ['asc', 'desc'])) {
                $query->orderBy(array_first($sorts), strtolower(array_last($sorts)));
            }
        }

        $data = $query->with(['lesson.course'])->paginate($request->per_page);

        return $this->response()->paginator($data, WorksResource::class);
    }

    /**
     * 修改作业状态
     *
     * @param \Modules\Personal\Entities\Work $work
     * @return \Illuminate\Http\JsonResponse
     */
    public function setWorkStatus(User $user, Work $work): JsonResponse
    {
        $work->status = !$work->status;

        return $this->response()->success($work->save());
    }

    /**
     * 课程管理
     *
     * @param \Modules\Personal\Http\Requests\CourseListRequest $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function course(CourseListRequest $request, User $user): JsonResponse
    {
        $data = $this->courseRepository
            ->keyword($request->keyword)
            ->course('id', $request->course_id !== null ? (int) $request->course_id : null)
            ->course('category', $request->category !== null ? (int) $request->category : null)
            ->orderBy('courses.category')
            ->orderBy('courses.level')
            ->select(['courses.*'])
            ->distinct()
            ->with([
                'lessons' => function ($query) {
                    $query->where('status', '=', 1);
                },
                'lessons.sections' => function ($query) {
                    $query->where('status', '=', 1);
                },
                'lessons.sections.learnRecords' => function ($query) use ($user) {
                    $query->where('user_id', '=', $user->getAuthIdentifier());
                },
                'lessons.sections.learnProgresses' => function ($query) use ($user) {
                    $query->where('user_id', '=', $user->getAuthIdentifier());
                },
            ])
            ->where('course_users.user_id', '=', $user->getAuthIdentifier())
            ->paginate($request->per_page);

        $data->getCollection()->map(function ($item) {
            // 计算上课总时长和上课进度
            $learnRecordsTotal = 0;
            $sectionCount = 0;
            $finishSectionCount = 0;

            $item->lessons->map(function ($item) use (&$learnRecordsTotal, &$sectionCount, &$finishSectionCount) {
                $sectionCount += $item->sections->count();
                $item->sections->map(function ($item) use (&$learnRecordsTotal, &$finishSectionCount) {
                    $finishSectionCount += $item->learnRecords->unique('section_id')->values()->count();
                    $item->learnRecords->map(function ($item) use (&$learnRecordsTotal) {
                        $learnRecordsTotal += $item->duration;
                    });
                });
            });

            $item->learn_records_total = $learnRecordsTotal;
            $item->learn_records_total_format = formatSecond((int) (floor($learnRecordsTotal / 1000)));
            $item->finish_section_percent = $sectionCount === 0 ? 0 : round($finishSectionCount / $sectionCount, 2);
        });

        return $this->response()->paginator($data, CourseResource::class);
    }

    /**
     * 课程学习记录
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @param \App\Course $course
     * @return \Illuminate\Http\JsonResponse
     */
    public function courseLearnRecord(Request $request, User $user, Course $course): JsonResponse
    {
        $lessonId = $request->lesson_id;

        $course->load([
            'lessons' => function ($query) use ($lessonId) {
                $query->where('status', '=', 1);

                if ($lessonId) {
                    $query->where('id', '=', $lessonId);
                }
            },
            'lessons.sections' => function ($query) {
                $query->where('status', '=', 1);
            },
        ]);

        $sectionIds = collect([]);

        $course->lessons->pluck('sections')->map(function ($sections) use (&$sectionIds) {
            $sectionIds = $sectionIds->merge($sections->pluck('id'));
        });

        $learnRecords = LearnRecord::where('user_id', $user->getAuthIdentifier())
            ->whereIn('section_id', $sectionIds)
            ->with([
                'courseSection.courseLesson.course',
            ])
            ->get();

        return $this->response()->collection($learnRecords, LearnRecordsResource::class);
    }

    /**
     * 订单管理
     *
     * @param \Modules\Personal\Http\Requests\OrderListRequest $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function order(OrderListRequest $request, User $user): JsonResponse
    {
        $data = $this->orderRepository
            ->date($request->start_date, $request->end_date)
            ->keyword($request->keyword)
            ->status($request->status !== null ? (int) $request->status : null)
            ->where('orders.user_id', $user->getAuthIdentifier())
            ->distinct()
            ->select(['orders.*'])
            ->orderBy('orders.created_at', 'DESC')
            ->with(['user', 'creator'])
            ->paginate($request->per_page, DB::raw('`orders`.`id`'));

        return $this->response()->paginator($data, OrderResource::class);
    }

    /**
     * 用户观看录播课数据
     *
     * @param \Modules\Personal\Http\Requests\UserLearnRecordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function learnRecord(UserLearnRecordRequest $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = $this->userLearnRecordRepository
            ->grade($request->start_grade !== null ? (int) $request->start_grade : null, $request->end_grade !== null ? (int) $request->end_grade : null)
            ->sex($request->sex !== null ? (int) $request->sex : null)
            ->category($request->user_category !== null ? (int) $request->user_category : null)
            ->date($startDate, $endDate)
            ->keyword($request->keyword)
            ->select(['users.*'])
            ->with([
                'learnRecords' => function ($query) use ($startDate, $endDate) {
                    if ($startDate) {
                        $query->where('entry_at', '>=', Carbon::parse($startDate)->startOfDay());
                    }

                    if ($endDate) {
                        $query->where('entry_at', '<=', Carbon::parse($endDate)->endOfDay());
                    }
                },
                'userCategory',
            ])
            ->distinct()
            ->orderBy('users.created_at', 'DESC');

        if ($request->has('export')) {
            $data = $query->get();

            return Excel::download(new UserLearnRecordExport($data), '用户观看录播课数据.xlsx');
        } else {
            $data = $query->paginate($request->per_page, DB::raw('`users`.`id`'));
        }

        return $this->response()->paginator($data, UserResource::class);
    }

    /**
     * 用户观看录播课详情
     *
     * @param \Modules\Personal\Http\Requests\UserLearnRecordDetailRequest $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function learnRecordDetail(UserLearnRecordDetailRequest $request, User $user)
    {
        $query = $this->userLearnRecordDetailRepository
            ->courseCategory($request->course_category !== null ? (int) $request->course_category : null)
            ->date($request->start_date, $request->end_date)
            ->course($request->course_id !== null ? (int) $request->course_id : null)
            ->lesson($request->lesson_id !== null ? (int) $request->lesson_id : null)
            ->select(['learn_records.*'])
            ->with(['courseSection.courseLesson.course', 'user'])
            ->where('learn_records.user_id', $user->getAuthIdentifier())
            ->orderBy('id', 'DESC');

        if ($request->has('export')) {
            $data = $query->get();

            return Excel::download(new UserLearnRecordDetailExport($data), '用户观看录播课详情.xlsx');
        } else {
            $data = $query->paginate($request->per_page);
        }

        return $this->response()->paginator($data, LearnRecordsResource::class);
    }

    /**
     * 沟通记录
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function conversation(Request $request, User $user): JsonResponse
    {
        $data = Conversation::orderBy('created_at', 'DESC')
            ->user($user->getAuthIdentifier())
            ->with(['creator'])
            ->paginate($request->per_page);

        return $this->response()->paginator($data, ConversationResource::class);
    }

    /**
     * 创建沟通记录
     *
     * @param \Modules\Personal\Http\Requests\ConversationRequest $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function createConversation(ConversationRequest $request, User $user): JsonResponse
    {
        $data = $request->all();

        $data['user_id'] = $user->getAuthIdentifier();
        $data['creator_id'] = $this->user()->getAuthIdentifier();
        $data['content'] = Purifier::clean($data['content']);

        return $this->response()->created('', Conversation::create($data));
    }

    /**
     * 寄件记录
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function delivery(Request $request, User $user): JsonResponse
    {
        $data = Delivery::leftJoin('express_users', 'express_users.id', '=', 'deliveries.express_user_id')
            ->where('express_users.user_id', '=', $user->getAuthIdentifier())
            ->select(['deliveries.*'])
            ->orderBy('deliveries.created_at', 'DESC')
            ->with([
                'expressUser.course',
                'operator',
                'province',
                'city',
                'district',
                'deliveryLessons.lesson',
            ])
            ->paginate($request->per_page);

        return $this->response()->paginator($data, DeliveryResource::class);
    }


    public function loginLogs(User $user)
    {
        $loginLogs = $user->loginLogs()->orderBy('created_at', 'desc')
            ->paginate(\request('per_page', 15));

        return responseSuccess($loginLogs);
    }
}
