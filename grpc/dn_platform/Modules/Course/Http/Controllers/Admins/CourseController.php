<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Http\Requests\StoreCoursePost;
use Modules\Course\Entities\Course;
use function App\responseSuccess;
use function App\responseFailed;
use Modules\Course\Entities\MusicTheory;
use Modules\Course\Entities\BigCourse;
use Modules\Course\Entities\CourseLesson;

class CourseController extends Controller
{
    /**
     * 获取课程列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, Course $course)
    {
        $perPage = (int) $request->input('per_page', 15);

        $status = $request->input('status', null);
        $drainage = $request->input('drainage', null);
        $category = $request->input('category', null);
        $type = $request->input('type', null);
        $level = $request->input('level', null);
        $keyword = $request->input('keyword', null);

        $data = $course->select('title', 'id', 'category', 'price', 'status', 'is_drainage', 'cover_id', 'level', 'is_mail', 'original_price', 'learn_duration', 'type')
            ->with([
                'musicTheories' => function ($query) {
                    $query->where('status', MusicTheory::STATUS_NO)
                        ->select('music_theories.id', 'name');
                },
                'arduinos' => function ($query) {
                    $query->select('arduino_materials.id', 'name', 'is_arduino');
                },
                'cover',
                'lessons' => function ($query) {
                    $query->select('id', 'course_id')
                        ->where('status', CourseLesson::LESSON_STATUS_ON);
                }
            ])
            ->withCount(['studyClass'])
            ->when($category, function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when(! is_null($drainage), function ($query) use ($drainage) {
                return $query->where('is_drainage', $drainage);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($level, function ($query) use ($level) {
                return $query->where('level', $level);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        collect($data->items())->map(function ($item) {
            $item->price = (float) $item->price / 100;
            $item->original_price = (float) $item->original_price / 100;
            $item->lessons_count = $item->lessons->count();
            $item->count_learn_day = $item->learn_duration; // todo 扩展成自己添加学习时长
        });

        return responseSuccess($data);
    }

    /**
     * 获取课程课屎工具接口列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function courseLessons(Request $request, Course $course)
    {
        $status = $request->input('status', null);
        $category = $request->input('category', null);
        $type = $request->input('type', null);
        $level = $request->input('level', null);

        $data = $course->select('title', 'id', 'category', 'price', 'status', 'is_drainage', 'level', 'is_mail', 'original_price', 'type')
            ->with([
                'arduinos' => function ($query) {
                    $query->select('arduino_materials.id', 'name', 'is_arduino');
                },
                'lessons' => function ($query) {
                    $query->select('id', 'course_id', 'title')
                        ->where('status', CourseLesson::LESSON_STATUS_ON);
                }
            ])
            ->when($category, function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($level, function ($query) use ($level) {
                return $query->where('level', $level);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return responseSuccess($data);
    }

    /**
     * 添加课程
     *
     * @param \Modules\Course\Http\Requests\StoreCoursePost $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreCoursePost $request, Course $course)
    {
        $course->title = $request->title;
        $course->price = $request->input('price', 0);
        $course->original_price = $request->input('original_price', 0);
        $course->cover_id = $request->input('cover_id', 0);
        $course->learn_duration = $request->input('learn_duration', 0);
        $course->category = $request->category;
        $course->level = $request->input('level', 1);
        $course->course_intro = $request->input('course_intro', '');
        $course->status = $request->status;
        $course->is_drainage = $request->is_drainage;
        $course->is_mail = $request->is_mail;
        $course->type = $request->type;

        if ($course->save()) {
            return responseSuccess([
                'course_id' => $course->id
            ], '添加课程成功');
        } else {
            return responseFailed('添加课程失败', 500);
        }
    }

    /**
     * 获取一条课程
     *
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Course $course)
    {
        $course->load([
            'musicTheories' => function ($query) {
                $query->select('music_theories.id', 'name');
            },
            'arduinos' => function ($query) {
                $query->select('arduino_materials.id', 'name');
            },
            'cover',
            'bigCourses' => function ($query) {
                $query->select('big_courses.id', 'big_courses.title');
            }
        ]);

        return responseSuccess($course);
    }

    /**
     * 修改课程
     *
     * @param \Modules\Course\Http\Requests\StoreCoursePost $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreCoursePost $request, Course $course)
    {
        $course->title = $request->title;
        $course->price = $request->input('price', 0);
        $course->original_price = $request->input('original_price', 0);
        $course->cover_id = $request->input('cover_id', 0);
        $course->learn_duration = $request->input('learn_duration', 0);
        $course->category = $request->category;
        $course->level = $request->input('level', 1);
        $course->course_intro = $request->input('course_intro', '');
        $course->status = $request->status;
        $course->is_drainage = $request->is_drainage;
        $course->is_mail = $request->is_mail;
        $course->type = $request->type;

        if ($course->save()) {
            return responseSuccess([
                'course_id' => $course->id
            ], '修改课程成功');
        } else {
            return responseFailed('修改课程失败', 500);
        }
    }

    /**
     * 上下架一条课程
     *
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(Course $course)
    {
        if ($course->status && $course->bigCourses->isNotEmpty()) {
            return responseFailed('已有大课程使用，不予下架！', 400);
        }

        $course->actionStatus();

        return responseSuccess();
    }

    /**
     * 课程设置乐理包
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function musicTheory(Request $request, Course $course)
    {
        $this->validate($request, ['musics' => 'array']);

        if ($course->musicTheories()->sync($request->musics)) {
            return responseSuccess([
                'course_id' => $course->id
            ], '设置乐理包成功');
        } else {
            return responseFailed('设置乐理包失败', 500);
        }
    }

    /**
     * 课程设置arduino素材
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Course $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function arduino(Request $request, Course $course)
    {
        $this->validate($request, ['arduinos' => 'array']);

        if ($course->arduinos()->sync($request->arduinos)) {
            return responseSuccess([
                'course_id' => $course->id
            ], '设置arduino素材成功');
        } else {
            return responseFailed('设置arduino素材失败', 500);
        }
    }
}
