<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\Entities\BigCourse;
use Modules\Course\Http\Requests\StoreBigCoursePost;
use function App\responseSuccess;
use App\Http\Controllers\Controller;

class BigCourseController extends Controller
{
    /**
     * 获取大课程列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\BigCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, BigCourse $course)
    {
        $perPage = (int) $request->input('per_page', 15);

        $status = $request->input('status', null);
        $category = $request->input('category', null);
        $keyword = $request->input('keyword', null);

        $data = $course->select('title', 'id', 'category', 'status', 'course_intro', 'cover_id')
            ->with([
                'courses' => function ($query) {
                    $query->select('courses.id', 'title', 'price', 'original_price')
                        ->orderBy('big_course_course_pivot.sort');
                },
                'cover',
            ])
            ->when($category, function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        collect($data->items())->map(function ($item) {
            $item->count_course_price = (float) $item->courses->sum('price') / 100;
            $item->count_course = $item->courses->count();
        });

        return responseSuccess($data);
    }

    /**
     * 添加大课程
     *
     * @param \Modules\Course\Http\Requests\StoreBigCoursePost $request
     * @param \Modules\Course\Entities\BigCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreBigCoursePost $request, BigCourse $course)
    {
        $course->title = $request->title;
        $course->cover_id = $request->input('cover_id', 0);
        $course->category = $request->category;
        $course->course_intro = $request->input('course_intro', '');
        $course->status = $request->status;
        $course_ids = $request->input('course_ids', []);

        $course->getConnection()->transaction(function () use ($course, $course_ids) {
            if ($course->save()) {
                $course->courses()->attach(array_map(function ($item) {
                    return ['sort' => $item];
                }, array_flip($course_ids)));
            }
        });

        return responseSuccess([
            'big_course_id' => $course->id
        ], '添加大课程成功');
    }

    /**
     * 获取一条大课程
     *
     * @param \Modules\Course\Entities\BigCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(BigCourse $course)
    {
        $course->load([
            'courses' => function ($query) {
                $query->select('courses.id', 'title', 'price', 'original_price', 'big_course_course_pivot.sort', 'type')
                    ->orderBy('big_course_course_pivot.sort');
            },
            'cover'
        ]);

        return responseSuccess($course);
    }

    /**
     * 修改大课程
     *
     * @param \Modules\Course\Http\Requests\StoreBigCoursePost $request
     * @param \Modules\Course\Entities\BigCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreBigCoursePost $request, BigCourse $course)
    {
        $course->title = $request->title;
        $course->cover_id = $request->input('cover_id', 0);
        $course->category = $request->category;
        $course->course_intro = $request->input('course_intro', '');
        $course->status = $request->status;
        $course_ids = $request->input('course_ids', []);

        $course->getConnection()->transaction(function () use ($course, $course_ids) {
            if ($course->save()) {
                $course->courses()->sync(array_map(function ($item) {
                    return ['sort' => $item];
                }, array_flip($course_ids)));
            }
        });

        return responseSuccess([
            'big_course_id' => $course->id
        ], '修改大课程成功');
    }

    /**
     * 上下架一条大课程
     *
     * @param \Modules\Course\Entities\BigCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(BigCourse $course)
    {
        $course->actionStatus();

        return responseSuccess();
    }
}
