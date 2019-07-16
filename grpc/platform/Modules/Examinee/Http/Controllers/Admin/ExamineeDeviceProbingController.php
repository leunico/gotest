<?php

namespace Modules\Examinee\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examinee\Entities\ExamineeDeviceProbing;
use Modules\Examination\Entities\ExaminationExaminee;

class ExamineeDeviceProbingController extends Controller
{
    /**
     * 检测列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Examination\Entities\ExaminationExaminee $eexaminee
     * @param \Modules\Examinee\Entities\ExamineeDeviceProbing $deviceProbing
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, ExamineeDeviceProbing $deviceProbing, ExaminationExaminee $eexaminee): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = (int) $request->input('is_all', 0);

        $query = $deviceProbing->select(
            'examination_examinee_id',
            'is_camera',
            'is_microphone',
            'is_chrome',
            'is_mc_ide',
            'is_scratch_ide',
            'is_python_ide',
            'is_c_ide',
            'created_at'
        )
            ->where('examination_examinee_id', $eexaminee->id);

        return $this->response()->success(empty($isAll) ? $query->paginate($perPage) : $query->get());
    }
}
