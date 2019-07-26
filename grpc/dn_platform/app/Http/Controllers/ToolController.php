<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\District;
use App\User;
use App\Setting;

class ToolController extends Controller
{
    /**
     * 地区列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function districts(Request $request): JsonResponse
    {
        $districts = District::all();
        $isGroup = $request->input('group', null);

        return $this->response()->success($isGroup ? $districts->groupBy('parent_code') : $districts);
    }

    /**
     * 年级列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function grade(): JsonResponse
    {
        $data = [];

        foreach (User::$gradeMap as $id => $grade) {
            $data[] = [
                'id' => $id,
                'grade' => $grade,
            ];
        }

        return $this->response()->success($data);
    }

    /**
     * 默认头像列表
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function defaultHead(Request $request): JsonResponse
    {
        $headimages = Setting::where('name', 'default_head')->first();

        return $this->response()->success($headimages ? $headimages->contents : []);
    }

    /**
     * 获取某个配置项目
     *
    //  * @param \Illuminate\Http\Request $request
     * @param \App\Setting $setting
     * @return \Illuminate\Http\JsonResponse
     */
    public function setting(Setting $setting): JsonResponse
    {
        return $this->response()->success($setting->contents);
    }
}
