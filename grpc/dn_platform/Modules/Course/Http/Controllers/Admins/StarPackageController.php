<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Course\Http\Requests\StoreStarPackagePost;
use Modules\Course\Entities\StarPackage;
use function App\responseSuccess;
use function App\responseFailed;

class StarPackageController extends Controller
{
    /**
     * 获取星星包列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\StarPackage $star
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, StarPackage $star)
    {
        $perPage = (int) $request->input('per_page', 15);

        $status = $request->input('status', null);
        $keyword = $request->input('keyword', null);

        $data = $star->select('title', 'id', 'count_lesson', 'price', 'star', 'status', 'created_at')
            ->with([
                // ...
            ])
            ->when(! is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return responseSuccess($data);
    }

    /**
     * 添加星星包
     *
     * @param \Modules\Course\Http\Requests\StoreStarPackagePost $request
     * @param \Modules\Course\Entities\StarPackage $star
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreStarPackagePost $request, StarPackage $star)
    {
        $star->title = $request->title;
        $star->count_lesson = $request->count_lesson;
        $star->price = $request->price;
        $star->star = $request->star;
        $star->status = $request->status;

        if ($star->save()) {
            return responseSuccess([
                'star_package_id' => $star->id
            ], '添加星星包成功');
        } else {
            return responseFailed('添加星星包失败', 500);
        }
    }

    /**
     * 获取一条星星包
     *
     * @param \Modules\Course\Entities\StarPackage $star
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(StarPackage $star)
    {
        $star->load([
            // ...
        ]);

        return responseSuccess($star);
    }

    /**
     * 修改星星包
     *
     * @param \Modules\Course\Http\Requests\StoreStarPackagePost $request
     * @param \Modules\Course\Entities\StarPackage $star
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreStarPackagePost $request, StarPackage $star)
    {
        $star->title = $request->title;
        $star->count_lesson = $request->count_lesson;
        $star->price = $request->price;
        $star->star = $request->star;
        $star->status = $request->status;

        if ($star->save()) {
            return responseSuccess([
                'star_package_id' => $star->id
            ], '修改星星包成功');
        } else {
            return responseFailed('修改星星包失败', 500);
        }
    }

    /**
     * 上下架一条星星包
     *
     * @param \Modules\Course\Entities\StarPackage $star
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(StarPackage $star)
    {
        $star->actionStatus();

        return responseSuccess();
    }
}
