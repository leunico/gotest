<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Entities\BiuniqueCourse;
use Modules\Course\Http\Requests\StoreBiuniqueCoursePost;
use function App\responseSuccess;
use function App\responseFailed;
use Illuminate\Support\Facades\DB;

class BiuniqueCourseController extends Controller
{
    /**
     * 获取课程列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\BiuniqueCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, BiuniqueCourse $course)
    {
        $perPage = (int) $request->input('per_page', 15);

        $isAll = $request->input('is_all', null);
        $status = $request->input('status', null);
        $isAudition = $request->input('is_audition', null);
        $keyword = $request->input('keyword', null);
        $category = $request->input('category', null);

        $data = $course->select('title', 'id', 'category', 'price_star', 'status', 'is_audition', 'introduce', 'sort')
            ->with([
                // ...
            ])
            ->when($category, function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when(! is_null($isAudition), function ($query) use ($isAudition) {
                return $query->where('is_audition', $isAudition);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->orderBy('sort')
            ->orderBy('created_at', 'desc');

        return responseSuccess($isAll ? $data->get() :  $data->paginate($perPage));
    }

    /**
     * 添加课程
     *
     * @param \Modules\Course\Http\Requests\storeBiuniqueCoursePost $request
     * @param \Modules\Course\Entities\BiuniqueCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreBiuniqueCoursePost $request, BiuniqueCourse $course)
    {
        $course->title = $request->title;
        $course->category = $request->category;
        $course->introduce = $request->introduce;
        $course->price_star = $request->price_star;
        $course->status = $request->status;
        $course->is_audition = $request->is_audition;
        $course->sort = $course->lastCourseSort() + 1;

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
     * @param \Modules\Course\Entities\BiuniqueCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(BiuniqueCourse $course)
    {
        $course->load([
            // ...
        ]);

        return responseSuccess($course);
    }

    /**
     * 修改课程
     *
     * @param \Modules\Course\Http\Requests\StoreBiuniqueCoursePost $request
     * @param \Modules\Course\Entities\BiuniqueCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreBiuniqueCoursePost $request, BiuniqueCourse $course)
    {
        $course->title = $request->title;
        $course->category = $request->category;
        $course->introduce = $request->introduce;
        $course->price_star = $request->price_star;
        $course->status = $request->status;
        $course->is_audition = $request->is_audition;

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
     * @param \Modules\Course\Entities\BiuniqueCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(BiuniqueCourse $course)
    {
        $course->actionStatus();

        return responseSuccess();
    }

    /**
     * 设置课程的排序【上下】
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\BiuniqueCourse $course
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function upDownSort(Request $request, BiuniqueCourse $course)
    {
        $this->validate($request, [
            'sort' => 'required|numeric|max:2147483647',
        ]);

        try {
            DB::beginTransaction();
            $courseCheck = BiuniqueCourse::where('sort', $request->sort)->first();
            if (empty($courseCheck) || $course->id == $courseCheck->id) {
                DB::rollBack();
                return responseFailed('排序失败，没有置换对象', 500);
            }
            $sort = $courseCheck->sort;
            $courseCheck->sort = $course->sort;
            $course->sort = $sort;
            if ($courseCheck->save() && $course->save()) {
                DB::commit();
                return responseSuccess();
            }

            DB::rollBack();
            return responseFailed('排序失败', 500);
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed($exception->getMessage());
        }
    }
}
