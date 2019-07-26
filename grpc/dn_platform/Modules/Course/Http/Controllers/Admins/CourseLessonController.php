<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Http\Requests\StoreCourseLessonPost;
use Modules\Course\Entities\CourseLesson;
use function App\responseSuccess;
use function App\responseFailed;

class CourseLessonController extends Controller
{
    /**
     * 获取课程主题列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\CourseLesson $lesson
     * @param int $course_id
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, CourseLesson $lesson, int $course_id = 0)
    {
        $perPage = (int) $request->input('per_page', 50);
        $isAll = $request->input('is_all', null);

        $query = $lesson->OfCourseId($course_id)
            ->select('id', 'title', 'sort', 'cover_id', 'lesson_intro', 'is_code', 'status', 'knowledge', 'count_user_learns', 'is_drainage')
            ->with([
                'cover',
            ])
            ->withCount(['learnRecords'])
            ->orderBy('sort');

        $data = $isAll ? $query->get() : $query->paginate($perPage);

        return responseSuccess($data);
    }

    /**
     * 添加课程主题
     *
     * @param \Modules\Course\Http\Requests\StoreCourseLessonPost $request
     * @param \Modules\Course\Entities\CourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreCourseLessonPost $request, CourseLesson $lesson)
    {
        $lesson->title = $request->title;
        $lesson->status = $request->status;
        $lesson->is_drainage = $request->is_drainage;
        $lesson->course_id = $request->course_id;
        $lesson->work = $request->input('work', '');
        $lesson->tutorial_link = $request->input('tutorial_link', '');
        $lesson->materials = $request->input('materials', '');
        $lesson->lesson_intro = $request->input('lesson_intro', '');
        $lesson->knowledge = $request->input('knowledge', '');
        $lesson->cover_id = $request->input('cover_id', 0);
        $lesson->is_code = $request->input('is_code', 0);
        $lesson->count_user_learns = $request->input('count_user_learns', 0);
        $lesson->sort = empty($request->status) ? CourseLesson::LESSON_OFF_SORT : $lesson->lastSort($request->course_id);

        if ($lesson->save()) {
            return responseSuccess([
                'course_lesson_id' => $lesson->id,
            ], '添加课程主题成功');
        } else {
            return responseFailed('添加课程主题失败', 500);
        }
    }

    /**
     * 获取一条课程主题
     *
     * @param \Modules\Course\Entities\CourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(CourseLesson $lesson)
    {
        $lesson->load([
            'cover',
        ]);

        return responseSuccess($lesson);
    }

    /**
     * 修改课程主题
     *
     * @param \Modules\Course\Http\Requests\StoreCourseLessonPost $request
     * @param \Modules\Course\Entities\CourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreCourseLessonPost $request, CourseLesson $lesson)
    {
        $lesson->sort = empty($request->status) ? CourseLesson::LESSON_OFF_SORT :
            (empty($lesson->status) ? $lesson->lastSort($request->course_id) : $lesson->sort);
        $lesson->title = $request->title;
        $lesson->status = $request->status;
        $lesson->is_drainage = $request->is_drainage;
        $lesson->course_id = $request->course_id;
        $lesson->work = $request->input('work', '');
        $lesson->tutorial_link = $request->input('tutorial_link', '');
        $lesson->materials = $request->input('materials', '');
        $lesson->lesson_intro = $request->input('lesson_intro', '');
        $lesson->knowledge = $request->input('knowledge', '');
        $lesson->cover_id = $request->input('cover_id', 0);
        $lesson->is_code = $request->input('is_code', 0);
        $lesson->count_user_learns = $request->input('count_user_learns', 0);

        if ($lesson->save()) {
            return responseSuccess([
                'course_lesson_id' => $lesson->id,
            ], '修改课程主题成功');
        } else {
            return responseFailed('修改课程主题失败', 500);
        }
    }

    /**
     * 上下架一条课程主题
     *
     * @param \Modules\Course\Entities\CourseLesson $lesson
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(CourseLesson $lesson)
    {
        $lesson->getConnection()->transaction(function () use ($lesson) {
            if (empty($lesson->status)) {
                $lesson->status = CourseLesson::LESSON_STATUS_ON;
                $lesson->sort = $lesson->lastSort($lesson->course_id);
            } else {
                CourseLesson::where('sort', '<', CourseLesson::LESSON_OFF_SORT)
                    ->where('course_id', $lesson->course_id)
                    ->where('sort', '>', $lesson->sort)
                    ->decrement('sort');
                $lesson->status = CourseLesson::LESSON_STATUS_OFF;
                $lesson->sort = CourseLesson::LESSON_OFF_SORT;
            }

            $lesson->save();
        });

        return responseSuccess();
    }

    /**
     * 设置一条课程章节的sort
     *
     * @param \Modules\Course\Entities\CourseLesson $lesson
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, CourseLesson $lesson)
    {
        $this->validate($request, ['sort' => ['required', 'in:0,1', function ($attribute, $value, $fail) use ($lesson) {
            if (empty($value) && empty($lesson->sort)) {
                return $fail('你已经是第一个了，不能在向上移动');
            } elseif ($lesson->sort >= CourseLesson::LESSON_OFF_SORT) {
                return $fail('下架主题，不可以移动');
            }
        }]]);

        $lesson->getConnection()->transaction(function () use ($lesson, $request) {
            if (empty($request->sort)) {
                CourseLesson::where('sort', ((int) $lesson->sort - 1))
                    ->where('course_id', $lesson->course_id)
                    ->increment('sort');
                $lesson->decrement('sort');
            } else {
                CourseLesson::where('sort', ((int) $lesson->sort + 1))
                    ->where('course_id', $lesson->course_id)
                    ->decrement('sort');
                $lesson->increment('sort');
            }
        });

        return responseSuccess([
            'course_lesson_id' => $lesson->id,
        ], '修改课程主题排序成功');
    }
}
