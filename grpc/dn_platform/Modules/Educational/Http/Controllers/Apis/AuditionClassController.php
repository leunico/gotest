<?php

declare(strict_types=1);

namespace Modules\Educational\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Educational\Entities\AuditionClass;
use Modules\Educational\Transformers\AuditionClassResource;

class AuditionClassController extends Controller
{
    /**
     * 我的预约试听课
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Educational\Entities\AuditionClass $class
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, AuditionClass $class): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $data = $class->select('id', 'category', 'entry_at', 'leave_at', 'teacher_id', 'status')
            ->where($this->user()->hasRole('audition_teacher') ? 'teacher_id' : 'user_id', $this->user()->id)
            ->with([
                'teacher' => function ($query) {
                    return $query->select('id', 'name', 'avatar', 'real_name');
                }
            ])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return $this->response()->paginator($data, AuditionClassResource::class);
    }

    /**
     * 我的预约课详情.
     *
     * @param \Modules\Educational\Entities\AuditionClass $class
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(AuditionClass $class): JsonResponse
    {
        $class->load([
            'teacher' => function ($query) {
                return $query->select('id', 'name', 'avatar');
            },
            'user' => function ($query) {
                return $query->select('id', 'name', 'real_name', 'phone');
            },
        ]);

        return $this->response()->item($class, AuditionClassResource::class);
    }
}
