<?php

namespace Modules\Crm\Http\Controllers\Api;

use function App\errorLog;
use function App\responseFailed;
use function App\responseSuccess;
use function App\toCarbon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Class CoursesController
 * @package Modules\Crm\Http\Controllers\Api
 */
class CoursesController extends Controller
{
    /**
     * Crm 同步课程
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncCourses(Request $request)
    {
        try {
            $startAt = Carbon::parse('2018-11-26')->startOfDay();
            $endAt   = Carbon::now()->endOfDay();
            if ($request->has('start_at')) {
                $startAt = Carbon::parse($request->start_at)->startOfDay();
            }
            if ($request->has('end_at')) {
                $endAt = Carbon::parse($request->end_at)->endOfDay();
            }

            $update     = $request->get('update', 0);
            $beforeDate = toCarbon($request->get('before_date'));

            $query = \DB::table('courses')->whereBetween('created_at', [$startAt, $endAt]);
            if ($update) {
                $query->where('updated_at', '>', $beforeDate->subMinutes(6)->toDateTimeString());
            }
            $data = $query->select([
                'id',
                'title',
                'course_intro',
                'price',
                'original_price',
                'cover_id',
                'level',
                'category',
                'status',
                'is_drainage',
                'is_mail',
                'deleted_at',
                'created_at',
                'updated_at',
            ])->get();

            return responseSuccess($data, '同步成功');
        } catch (\Exception $e) {
            errorLog($e, __FUNCTION__, 'error');
            return responseFailed('同步失败');
        }
    }
}
