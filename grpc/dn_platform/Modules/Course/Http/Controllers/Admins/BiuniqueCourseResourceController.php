<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Http\Requests\StoreBiuniqueCourseResourcePost;
use Modules\Course\Entities\BiuniqueCourseResource;
use function App\responseSuccess;
use function App\responseFailed;

class BiuniqueCourseResourceController extends Controller
{
    /**
     * 获取资源列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\BiuniqueCourseResource $resource
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, BiuniqueCourseResource $resource)
    {
        $perPage = (int) $request->input('per_page', 15);

        $course = $request->input('course', null);
        $status = $request->input('status', null);
        $category = $request->input('category', null);
        $keyword = $request->input('keyword', null);

        $data = $resource->select('title', 'id', 'biunique_course_id', 'file_id', 'category', 'status', 'created_at')
            // ->where('biunique_course_id', '>', 0)
            ->with([
                'file'
            ])
            ->when(! is_null($course), function ($query) use ($course) {
                return $query->where('biunique_course_id', $course);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when(! is_null($category), function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return responseSuccess($data);
    }

    /**
     * 添加资源
     *
     * @param \Modules\Course\Http\Requests\StoreBiuniqueCourseResourcePost $request
     * @param \Modules\Course\Entities\BiuniqueCourseResource $resource
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreBiuniqueCourseResourcePost $request, BiuniqueCourseResource $resource)
    {
        $resource->title = $request->title;
        $resource->biunique_course_id = $request->biunique_course_id;
        $resource->file_id = $request->file_id;
        $resource->category = $request->category;
        $resource->status = $request->input('status', 1);

        return $resource->save() ? responseSuccess() : responseFailed('添加资源失败', 500);
    }

    /**
     * 获取一条资源
     *
     * @param \Modules\Course\Entities\BiuniqueCourseResource $resource
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(BiuniqueCourseResource $resource)
    {
        $resource->load([
            'file'
        ]);

        return responseSuccess($resource);
    }

    /**
     * 修改资源
     *
     * @param \Modules\Course\Http\Requests\StoreBiuniqueCourseResourcePost $request
     * @param \Modules\Course\Entities\BiuniqueCourseResource $resource
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreBiuniqueCourseResourcePost $request, BiuniqueCourseResource $resource)
    {
        $resource->title = $request->title;
        $resource->biunique_course_id = $request->biunique_course_id;
        $resource->file_id = $request->file_id;
        $resource->category = $request->category;
        $resource->status = $request->input('status', 1);

        return $resource->save() ? responseSuccess() : responseFailed('修改资源失败', 500);
    }

    /**
     * 上下架资源
     *
     * @param \Modules\Course\Entities\BiuniqueCourseResource $resource
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(BiuniqueCourseResource $resource)
    {
        $resource->actionStatus();

        return responseSuccess();
    }
}
