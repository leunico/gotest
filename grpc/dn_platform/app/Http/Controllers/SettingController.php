<?php

namespace App\Http\Controllers;

use function App\responseSuccess;
use function App\responseFailed;
use Illuminate\Http\Request;
use App\Setting;

class SettingController extends Controller
{
    /**
     * 配置列表
     *
    //  * @param \Illuminate\Http\Request $request
     * @param \App\Setting $setting
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Setting $setting, string $namespace = '')
    {
        $data = $setting->select('namespace', 'id', 'name', 'note', 'contents')
            ->when(! empty($namespace), function ($query) use ($namespace) {
                $query->where('namespace', $namespace);
            })
            ->get();

        return responseSuccess($data);
    }

    /**
     * 更新配置.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Setting $setting
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(Request $request, Setting $setting)
    {
        $this->validate($request, ['contents' => 'required|array']);

        $setting->contents = $request->contents;

        if ($setting->save()) {
            return responseSuccess([
                'setting_id' => $setting->id
            ], '更新配置成功');
        } else {
            return responseFailed('更新配置失败', 500);
        }
    }
}
