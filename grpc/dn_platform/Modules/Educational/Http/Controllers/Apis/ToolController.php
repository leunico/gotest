<?php

declare(strict_types=1);

namespace Modules\Educational\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Educational\Entities\Teacher;
use App\User;
use Illuminate\Http\JsonResponse;

class ToolController extends Controller
{
    /**
     * 老师列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function teachers(Request $request): JsonResponse
    {
        $type = $request->input('type', Teacher::COURSE_TEACHER);

        $teachers = User::role($type)
            ->select('users.id', 'users.name', 'real_name', 'phone', 'sex')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->response()->success($teachers);
    }
}
