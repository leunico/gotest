<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\Http\Requests\StoreArduinoMaterialPost;
use Modules\Course\Entities\ArduinoMaterial;
use function App\responseFailed;
use function App\responseSuccess;
use App\Http\Controllers\Concerns\ControllerExtend;
use App\Http\Controllers\Controller;

class ArduinoMaterialController extends Controller
{
    use ControllerExtend;

    /**
     * arduino素材列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\ArduinoMaterial $arduino
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, ArduinoMaterial $arduino)
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = $request->input('is_all', null);

        $type = $request->input('type', null);
        $keyword = $request->input('keyword', null);

        $data = $arduino->select('id', 'name', 'md5', 'is_arduino', 'source_link', 'sort')
            ->when($type, function ($query) use ($type) {
                return $query->where('is_arduino', $type);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', "%$keyword%");
            })
            ->orderBy('sort')
            ->orderBy('id', 'desc');

        return responseSuccess($isAll ? $data->get() : $data->paginate($perPage), '操作成功', ['disk_url' => $this->getDiskUrl('scratch/media/')]);
    }

    /**
     * 添加arduino素材
     *
     * @param \Modules\Course\Http\Requests\StoreArduinoMaterialPost $request
     * @param \Modules\Course\Entities\ArduinoMaterial $arduino
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreArduinoMaterialPost $request, ArduinoMaterial $arduino)
    {
        list($width, $height) = ($imageInfo = @getimagesize($request->file->getRealPath())) === false ? [null, null] : $imageInfo;
        if ($request->file->getClientMimeType() != 'image/svg+xml' && empty($width) && empty($height)) {
            return responseFailed('arduino素材必须上传正确的图片素材', 422);
        }

        $result = $this->uploadOther($request->file);
        $arduino->name = $request->name;
        $arduino->is_arduino = $request->is_arduino;
        $arduino->source_link = $request->source_link;
        $arduino->md5 = $result['name'];
        $arduino->info = [
            $width,
            $height,
            $imageInfo['mime'] == 'image/svg-xml' ? 1 : 2
        ];

        if ($arduino->save()) {
            return responseSuccess([
                'arduino_id' => $arduino->id
            ], '添加arduino素材成功');
        } else {
            return responseFailed('添加arduino素材失败', 500);
        }
    }

    /**
     * 获取一条arduino素材
     *
     * @param \Modules\Course\Entities\ArduinoMaterial $arduino
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(ArduinoMaterial $arduino)
    {
        return responseSuccess($arduino);
    }

    /**
     * 修改arduino素材
     *
     * @param \Modules\Course\Http\Requests\StoreArduinoMaterialPost $request
     * @param \Modules\Course\Entities\ArduinoMaterial $arduino
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreArduinoMaterialPost $request, ArduinoMaterial $arduino)
    {
        $arduino->name = $request->name;
        $arduino->is_arduino = $request->is_arduino;
        $arduino->source_link = $request->source_link;

        if (!empty($request->file)) {
            list($width, $height) = ($imageInfo = @getimagesize($request->file->getRealPath())) === false ? [null, null] : $imageInfo;
            if ($request->file->getClientMimeType() != 'image/svg+xml' && empty($width) && empty($height)) {
                return responseFailed('arduino素材必须上传正确的图片素材', 422);
            }

            $result = $this->uploadOther($request->file);
            if ($result['name'] != $arduino->md5) {
                $arduino->info = [
                    $width,
                    $height,
                    $imageInfo['mime'] == 'image/svg-xml' ? 1 : 2
                ];
                $arduino->md5 = $result['name'];
            }
        }

        if ($arduino->save()) {
            return responseSuccess([
                'arduino_id' => $arduino->id
            ], '修改arduino素材成功');
        } else {
            return responseFailed('修改arduino素材失败', 500);
        }
    }

    /**
     * 删除
     *
     * @param \Modules\Course\Entities\ArduinoMaterial $arduino
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(ArduinoMaterial $arduino)
    {
        if (!$arduino->sections->isEmpty()) {
            return responseFailed('已有课程使用，请先解除使用关系！', 423);
        }

        $arduino->delete();

        return responseSuccess();
    }

    /**
     * 设置arduino素材的排序
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\ArduinoMaterial $arduino
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, ArduinoMaterial $arduino)
    {
        $this->validate($request, ['sort' => 'required|integer']);

        if ($arduino->update(['sort' => $request->sort])) {
            return responseSuccess();
        } else {
            return responseFailed('操作失败，请检查', 500);
        }
    }
}
