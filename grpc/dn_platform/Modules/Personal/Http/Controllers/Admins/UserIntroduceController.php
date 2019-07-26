<?php

namespace Modules\Personal\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Personal\Entities\UserIntroduce;
use App\Rules\ArrayExists;
use function App\responseSuccess;
use App\User;
use Modules\Personal\Entities\CourseUser;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\CourseLesson;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Personal\Exports\UserIntroduceListExport;
use Illuminate\Support\Carbon;
use Modules\Personal\Entities\ExpressUser;

class UserIntroduceController extends Controller
{
    /**
     * 转介绍用户列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Personal\Entities\UserIntroduce $introduce
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, UserIntroduce $introduce)
    {
        $perPage = (int) $request->input('per_page', 15);

        $keyword = $request->input('keyword', null);

        $data = $introduce->select('user_id', 'remark', 'name', 'real_name', 'phone', 'user_introduces.id')
            ->leftjoin('users', 'user_introduces.user_id', 'users.id')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('real_name', 'LIKE', "%$keyword%")
                    ->orWhere('name', 'LIKE', "%$keyword%")
                    ->orWhere('phone', 'LIKE', "%$keyword%");
            })
            ->with([
                'courseLessons',
                'courseUsers',
                'courseUsers.lessons' => function ($query) {
                    $query->select('id', 'course_id');
                }
            ])
            ->withCount(['orders']);

        if ($request->export) {
            return Excel::download(new UserIntroduceListExport($data->get()), Carbon::now()->format('Y-m-d H:i:s') . '转介绍用户列表.xlsx');
        }

        $data = $data->paginate($perPage);
        collect($data->items())->map(function ($item) {
            $item->count_lessons = $item->courseUsers->pluck('lessons')->flatten()->pluck('id')->merge($item->courseLessons->pluck('course_lesson_id'))->unique()->count();
            unset($item->courseUsers);
            return $item;
        });

        return responseSuccess($data);
    }

    /**
     * 转介绍详情
     *
     * @param \Modules\Personal\Entities\UserIntroduce $introduce
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(UserIntroduce $introduce)
    {
        $introduce->load([
            'courseUsers',
            'courseLessons'
        ]);

        return responseSuccess($introduce);
    }

    /**
     * 添加转介绍用户
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Personal\Entities\UserIntroduce $introduce
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(Request $request, UserIntroduce $introduce)
    {
        $this->validate($request, ['user_ids' => ['required', 'array', new ArrayExists(new User)]]);

        $introduce->getConnection()->transaction(function () use ($request) {
            foreach ($request->user_ids as $value) {
                UserIntroduce::firstOrCreate(['user_id' => $value]);
            }
        });

        return responseSuccess();
    }

    /**
     * 修改转介绍用户
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Personal\Entities\UserIntroduce $introduce
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(Request $request, UserIntroduce $introduce)
    {
        $introduce->remark = $request->input('remark', '');

        if ($introduce->save()) {
            return responseSuccess([
                'user_introduce_id' => $introduce->id
            ], '修改转介绍用户成功');
        } else {
            return responseFailed('修改转介绍用户失败', 500);
        }
    }

    /**
     * 分配转介绍用户课程权限
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function course(Request $request, User $user)
    {
        $this->validate($request, ['lessons_ids' => [
            // 'required',
            'array',
            new ArrayExists(new CourseLesson, true, true)
        ]]);

        $courseIds = [];
        $lessons = array_map(function ($item) use (&$courseIds) {
            $courseIds[] = $item;
            return ['course_id' => $item];
        }, $request->lessons_ids);

        $user->getConnection()->transaction(function () use ($courseIds, $user, $lessons) {
            if ($user->introduceCourseLessons()->sync($lessons)) {
                foreach (array_unique($courseIds) as $value) {
                    ExpressUser::firstOrCreate([
                        'user_id' => $user->id,
                        'course_id' => $value
                    ]);
                }
            }
        });

        return responseSuccess();
    }
}
