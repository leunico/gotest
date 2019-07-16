<?php

namespace Modules\Examinee\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examinee\Http\Controllers\ExamineeController as Controller;
use Illuminate\Http\JsonResponse;
use Modules\Examinee\Entities\ExamineeDeviceProbing;
use Modules\Examinee\Http\Requests\ExamineeDeviceProbingRequest;
use Modules\Examinee\Transformers\ExamineeDeviceProbingResource;

class ExamineeDeviceProbingController extends Controller
{
    /**
     * 添加设备检测.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeDeviceProbingRequest $request
     * @param \Modules\Examinee\Entities\ExamineeDeviceProbing $deviceProbing
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(ExamineeDeviceProbingRequest $request, ExamineeDeviceProbing $deviceProbing): JsonResponse
    {
        $deviceProbing->examination_examinee_id = $request->examination_examinee_id;
        $deviceProbing->is_camera = $request->is_camera;
        $deviceProbing->is_microphone = $request->is_microphone;
        $deviceProbing->is_chrome = $request->is_chrome;
        $deviceProbing->is_mc_ide = $request->is_mc_ide;
        $deviceProbing->is_scratch_ide = $request->is_scratch_ide;
        $deviceProbing->is_python_ide = $request->is_python_ide;
        $deviceProbing->is_c_ide = $request->is_c_ide;

        return $deviceProbing->save() ? $this->response()->item($deviceProbing, ExamineeDeviceProbingResource::class) : $this->response()->error();
    }

    /**
     * 修改设备检测.
     *
     * @param \Modules\Examinee\Http\Requests\ExamineeDeviceProbingRequest $request
     * @param \Modules\Examinee\Entities\ExamineeDeviceProbing $deviceProbing
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(ExamineeDeviceProbingRequest $request, ExamineeDeviceProbing $deviceProbing): JsonResponse
    {
        $deviceProbing->is_camera = $request->is_camera ?? $deviceProbing->is_camera;
        $deviceProbing->is_microphone = $request->is_microphone ?? $deviceProbing->is_microphone;
        $deviceProbing->is_chrome = $request->is_chrome ?? $deviceProbing->is_chrome;
        $deviceProbing->is_mc_ide = $request->is_mc_ide ?? $deviceProbing->is_mc_ide;
        $deviceProbing->is_scratch_ide = $request->is_scratch_ide ?? $deviceProbing->is_scratch_ide;
        $deviceProbing->is_python_ide = $request->is_python_ide ?? $deviceProbing->is_python_ide;
        $deviceProbing->is_c_ide = $request->is_c_ide ?? $deviceProbing->is_c_ide;

        return $this->response()->success($deviceProbing->save());
    }
}
