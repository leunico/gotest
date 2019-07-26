<?php

namespace Modules\Educational\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Educational\Http\Requests\StoreAuditionClassPost;
use Modules\Educational\Entities\AuditionClass;
use function App\responseSuccess;
use Modules\Educational\Entities\Teacher;
use Illuminate\Support\Carbon;

class AuditionClassController extends Controller
{
    /**
     * 预约试听课列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\AuditionClass $class
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, AuditionClass $class)
    {
        $perPage = (int) $request->input('per_page', 15);

        $category = $request->input('category', null);
        $keyword = $request->input('keyword', null);
        $status = $request->input('status', null);

        $data = $class->select('id', 'user_id', 'teacher_id', 'creator_id', 'category', 'entry_at', 'leave_at', 'remark', 'status', 'created_at')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->whereIn('user_id', function ($query) use ($keyword) {
                    return $query->from('users')
                        ->select('id')
                        ->where('real_name', 'Like', "%$keyword%")
                        ->orWhere('phone', 'Like', "%$keyword%");
                });
            })
            ->when(! is_null($status), function ($query) use ($status) {
                if ($status == AuditionClass::STATUS_OVER) {
                    return $query->where('status', AuditionClass::STATUS_NO)
                        ->where('entry_at', '<=', Carbon::now());
                } elseif ($status == AuditionClass::STATUS_NO) {
                    return $query->where('status', $status)
                        ->where('entry_at', '>', Carbon::now());
                } else {
                    return $query->where('status', $status);
                }
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->with([
                'teacher' => function ($query) {
                    return $query->select('id', 'name', 'real_name');
                },
                'user' => function ($query) {
                    return $query->select('id', 'name', 'real_name', 'phone');
                },
                'creator'
            ])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return responseSuccess($data, 'Success.', ['categoryMap' => Teacher::$authoritys]);
    }

    /**
     * 预约试听课
     *
     * @param \Modules\Educational\Http\Requests\StoreAuditionClassPost $request
     * @param \Modules\Educational\Entities\AuditionClass $class
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreAuditionClassPost $request, AuditionClass $class)
    {
        $class->user_id = $request->user_id;
        $class->teacher_id = $request->teacher_id;
        $class->category = $request->category;
        $class->entry_at = $request->entry_at;
        $class->leave_at = $request->leave_at;
        $class->remark = $request->input('remark', '');
        $class->creator_id = $request->user()->id;

        if ($class->save()) {
            return responseSuccess([
                'class_id' => $class->id
            ], '预约试听课成功');
        } else {
            return responseFailed('预约试听课失败', 500);
        }
    }

    /**
     * 取消有效预约
     *
     * @param \Modules\Educational\Entities\AuditionClass $class
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(AuditionClass $class)
    {
        $class->actionStatus();

        return responseSuccess();
    }

    /**
     * 删除试听课
     *
     * @param \Modules\Educational\Entities\AuditionClass $class
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(AuditionClass $class)
    {
        $class->delete();

        return responseSuccess();
    }
}
