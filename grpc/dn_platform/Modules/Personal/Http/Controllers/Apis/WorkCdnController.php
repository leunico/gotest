<?php

namespace Modules\Personal\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Personal\Entities\Work;
use function App\uploadFile;
use function App\removeNullElement;
use Modules\Personal\Entities\WorkSbfile;
use Modules\Personal\Http\Requests\WorkCdnRequest;
use Modules\Personal\Jobs\HandleWorkSbFile;

class WorkCdnController extends Controller
{
    /**
     * 通过cdn上传作业
     */
    public function cdnSaveWork(WorkCdnRequest $request)
    {
        $user = Auth::user();
        $formData = $request->all();

        //作品信息
        $insert_data = [
            'user_id' => $user->id,
            'title' => $formData['title'],
            'lesson_id' => $formData['lesson_id'] ?? 0,
            'description' => $formData['description'] ?? '',
            'board_type' => $formData['board_type'] ?? '',
            'type' => 'scratch_arduino',
        ];
        if ($request->hasFile('file')) {
            $insert_data['file_url'] = uploadFile($request->file('file'), 'dn/work/' . date('Y-m-d'));
        }
        if ($request->hasFile('image_cover')) {
            $insert_data['image_cover'] = uploadFile($request->file('image_cover'), 'dn/image/' . date('Y-m-d'));
        }

        if (!empty($formData['work_id'])) {
            $work_data = Work::findOrFail($formData['work_id']);
            $work_data->update($insert_data);
        } else {
            \Log::info('添加作业作品');
            $work_data = Work::create($insert_data);
        }

        //异步处理sb3文件上传素材
        $sbfile = [
            'user_id' => $user->id,
            'work_id' => $work_data->id,
            'sb_url' => trim($formData['sb_url'], '/')
        ];
        $work_sbfile = WorkSbfile::create($sbfile);
        $this->dispatch(new HandleWorkSbFile($work_sbfile));

        return $this->response()->success($work_data);
    }
}
