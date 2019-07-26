<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Course\Http\Requests\StoreBiuniqueCourseLessonPost;
use Modules\Course\Entities\BiuniqueCourseLesson;
use function App\responseSuccess;
use function App\responseFailed;
use App\Rules\ArrayExists;
use Modules\Course\Entities\BiuniqueCourseResource;

class BiuniqueCourseLessonController extends Controller
{
    /**
     * 获取课程主题列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\BiuniqueCourseLesson $lesson
     * @param int $course_id
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, BiuniqueCourseLesson $lesson, int $course_id = 0)
    {
        $perPage = (int) $request->input('per_page', 50);
        $keyword = $request->input('keyword', null);
        $isAll = $request->input('is_all', null);

        $query = $lesson->ofBiuniqueCourseId($course_id)
            ->select('id', 'title', 'sort', 'introduce', 'status', 'biunique_course_id')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'LIKE', "%$keyword%");
            })
            ->withCount(['resources'])
            ->orderBy('sort');

        $data = $isAll ? $query->get() : $query->paginate($perPage);

        return responseSuccess($data);
    }

    /**
     * 添加一对一课程主题.
     *
     * @param \Modules\Course\Http\Requests\StoreBiuniqueCourseLessonPost $request
     * @param \Modules\Course\Entities\BiuniqueCourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreBiuniqueCourseLessonPost $request, BiuniqueCourseLesson $lesson)
    {
        $lesson->title = $request->title;
        $lesson->status = $request->status;
        $lesson->biunique_course_id = $request->biunique_course_id;
        $lesson->introduce = $request->input('introduce', '');
        $lesson->sort = empty($request->status) ? BiuniqueCourseLesson::LESSON_OFF_SORT : $lesson->lastSort($request->biunique_course_id);

        if ($lesson->save()) {
            return responseSuccess([
                'biunique_course_lesson_id' => $lesson->id,
            ], '添加一对一课程主题成功');
        } else {
            return responseFailed('添加课程主题失败', 500);
        }
    }

    /**
     * 获取一对一课程主题.
     *
     * @param \Modules\Course\Entities\BiuniqueCourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(BiuniqueCourseLesson $lesson)
    {
        $lesson->load([
            'resources' => function ($query) {
                $query->select('biunique_course_resources.title', 'biunique_course_resources.id', 'file_id')
                    ->where('status', BiuniqueCourseResource::STATUS_ON);
            }
        ]);

        return responseSuccess($lesson);
    }

    /**
     * 修改一对一课程主题.
     *
     * @param \Modules\Course\Http\Requests\StoreBiuniqueCourseLessonPost $request
     * @param \Modules\Course\Entities\BiuniqueCourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreBiuniqueCourseLessonPost $request, BiuniqueCourseLesson $lesson)
    {
        $lesson->sort = empty($request->status) ? BiuniqueCourseLesson::LESSON_OFF_SORT :
            (empty($lesson->status) ? $lesson->lastSort($lesson->biunique_course_id) : $lesson->sort);
        $lesson->title = $request->title;
        $lesson->status = $request->status;
        $lesson->introduce = $request->input('introduce', '');

        if ($lesson->save()) {
            return responseSuccess([
                'biunique_course_lesson_id' => $lesson->id,
            ], '修改课程主题成功');
        } else {
            return responseFailed('修改课程主题失败', 500);
        }
    }

    /**
     * 上下架一对一课程主题.
     *
     * @param \Modules\Course\Entities\BiuniqueCourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(BiuniqueCourseLesson $lesson)
    {
        $lesson->getConnection()->transaction(function () use ($lesson) {
            if (empty($lesson->status)) {
                $lesson->status = BiuniqueCourseLesson::LESSON_STATUS_ON;
                $lesson->sort = $lesson->lastSort($lesson->biunique_course_id);
            } else {
                BiuniqueCourseLesson::where('sort', '<', BiuniqueCourseLesson::LESSON_OFF_SORT)
                    ->where('biunique_course_id', $lesson->biunique_course_id)
                    ->where('sort', '>', $lesson->sort)
                    ->decrement('sort');
                $lesson->status = BiuniqueCourseLesson::LESSON_STATUS_OFF;
                $lesson->sort = BiuniqueCourseLesson::LESSON_OFF_SORT;
            }

            $lesson->save();
        });

        return responseSuccess();
    }

    /**
     * 设置课时的资源.
     *
     * @param \Modules\Course\Entities\BiuniqueCourseLesson $lesson
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function resource(Request $request, BiuniqueCourseLesson $lesson)
    {
        $this->validate($request, ['resources' => [
            // 'required',
            'array',
            new ArrayExists(new BiuniqueCourseResource)
        ]]);

        $lesson->resources()->sync($request->resources);

        return responseSuccess();
    }

    /**
     * 设置一对一课程课时的排序.
     *
     * @param \Modules\Course\EntitiesBiuniqueCourseLesson $lesson
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, BiuniqueCourseLesson $lesson)
    {
        $this->validate($request, [
            'sort' => [
                'required',
                'in:0,1',
                function ($attribute, $value, $fail) use ($lesson) {
                    if (empty($value) && $lesson->sort == BiuniqueCourseLesson::LESSON_START_SORT) {
                        return $fail('你已经是第一个了，不能在向上移动');
                    } elseif ($lesson->sort >= BiuniqueCourseLesson::LESSON_OFF_SORT) {
                        return $fail('下架主题，不可以移动');
                    }
                }
            ],
        ]);

        $lesson->getConnection()->transaction(function () use ($lesson, $request) {
            if (empty($request->sort)) {
                BiuniqueCourseLesson::where('sort', ((int) $lesson->sort - 1))
                    ->where('biunique_course_id', $lesson->biunique_course_id)
                    ->increment('sort');
                $lesson->decrement('sort');
            } else {
                BiuniqueCourseLesson::where('sort', ((int) $lesson->sort + 1))
                    ->where('biunique_course_id', $lesson->biunique_course_id)
                    ->decrement('sort');
                $lesson->increment('sort');
            }
        });

        return responseSuccess([
            'biunique_course_lesson_id' => $lesson->id,
        ], '修改课程主题排序成功');
    }
}
