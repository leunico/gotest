<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Course\Http\Requests\StoreMusicTheoryPost;
use Modules\Course\Entities\MusicTheory;
use function App\responseSuccess;
use function App\responseFailed;

class MusicTheoryController extends Controller
{
    /**
     * 获取乐理包列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\MusicTheory $music
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, MusicTheory $music)
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = $request->input('is_all', null);

        $status = $request->input('status', null);
        $keyword = $request->input('keyword', null);

        $data = $music->select('id', 'name', 'source_link', 'status', 'sort')
            ->with([
                'courses' => function ($query) {
                    $query->select('courses.id', 'title');
                },
            ])
            ->when(!is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'desc');

        return responseSuccess($isAll ? $data->get() : $data->paginate($perPage));
    }

    /**
     * 添加乐理包
     *
     * @param \Modules\Course\Http\Requests\StoreMusicTheoryPost $request
     * @param \Modules\Course\Entities\MusicTheory $music
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreMusicTheoryPost $request, MusicTheory $music)
    {
        $music->name = $request->name;
        $music->source_link = $request->source_link;
        $music->source_duration = $request->input('source_duration', 0);
        $music->status = $request->input('status', 0);

        if ($music->save()) {
            return responseSuccess([
                'music_id' => $music->id
            ], '添加乐理包成功');
        } else {
            return responseFailed('添加乐理包失败', 500);
        }
    }

    /**
     * 获取一条乐理包
     *
     * @param \Modules\Course\Entities\MusicTheory $music
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(MusicTheory $music)
    {
        $music->load([
            // ...
        ]);

        return responseSuccess($music);
    }

    /**
     * 修改乐理包
     *
     * @param \Modules\Course\Http\Requests\StoreMusicTheoryPost $request
     * @param \Modules\Course\Entities\MusicTheory $music
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreMusicTheoryPost $request, MusicTheory $music)
    {
        $music->name = $request->name;
        $music->source_link = $request->source_link;
        $music->source_duration = $request->input('source_duration', 0);
        $music->status = $request->input('status', 0);

        if ($music->save()) {
            return responseSuccess([
                'music_id' => $music->id
            ], '修改乐理包成功');
        } else {
            return responseFailed('修改乐理包失败', 500);
        }
    }

    /**
     * 上下架一条课程
     *
     * @param \Modules\Course\Entities\MusicTheory $music
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(MusicTheory $music)
    {
        $music->actionStatus();

        return responseSuccess();
    }

    /**
     * 删除
     *
     * @param \Modules\Course\Entities\MusicTheory $music
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(MusicTheory $music)
    {
        if (!$music->courses->isEmpty()) {
            return responseFailed('已有课程使用，不予删除！', 423);
        }

        $music->delete();

        return responseSuccess();
    }
}
