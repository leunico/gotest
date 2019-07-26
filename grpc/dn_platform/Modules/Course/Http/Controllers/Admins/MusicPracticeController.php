<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\Http\Requests\StoreMusicPracticePost;
use Modules\Course\Entities\MusicPractice;
use Modules\Course\Entities\Tag;
use function App\responseSuccess;
use App\Http\Controllers\Controller;

class MusicPracticeController extends Controller
{
    /**
     * 获取音乐练耳列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, MusicPractice $practice)
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = $request->input('is_all', null);

        $tag = $request->input('tag', null);
        $status = $request->input('status', null);
        $keyword = $request->input('keyword', null);

        $data = $practice->select('id', 'name', 'audio_link', 'status', 'sort')
            ->with(['tags'])
            ->when(!is_null($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', "%$keyword%");
            })
            ->when($tag, function ($query) use ($tag) {
                return $query->whereIn('id', function ($query) use ($tag) {
                    $query->from('tags')
                        ->leftjoin('model_has_tags', 'tags.id', 'model_has_tags.tag_id')
                        ->select('model_has_tags.model_id')
                        ->where('model_has_tags.model_type', Tag::TYPE_MUSIC_PRACTICE)
                        ->where('name', 'like', "%$tag%");
                });
            })
            ->orderBy('sort')
            ->orderBy('id', 'desc');

        return responseSuccess($isAll ? $data->get() : $data->paginate($perPage));
    }

    /**
     * 添加音乐练耳
     *
     * @param \Modules\Course\Http\Requests\StoreMusicPracticePost $request
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreMusicPracticePost $request, MusicPractice $practice)
    {
        $practice->name = $request->name;
        $practice->audio_link = $request->audio_link;
        $practice->book_id = $request->book_id;
        $practice->status = $request->input('status', 0);
        $practice->sort = $request->input('sort', 0);

        $practice->getConnection()->transaction(function () use ($practice, $request) {
            if ($practice->save()) {
                $practice->tags()->attach(array_map(function ($item) {
                    return ['model_type' => Tag::TYPE_MUSIC_PRACTICE];
                }, array_flip($request->tags)));
            }
        });

        return responseSuccess([
            'music_practice_id' => $practice->id,
        ], '添加音乐练耳成功');
    }

    /**
     * 获取一条音乐练耳
     *
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(MusicPractice $practice)
    {
        $practice->load([
            'tags',
            'book'
        ]);

        return responseSuccess($practice);
    }

    /**
     * 修改音乐练耳
     *
     * @param \Modules\Course\Http\Requests\StoreMusicPracticePost $request
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreMusicPracticePost $request, MusicPractice $practice)
    {
        $practice->name = $request->name;
        $practice->audio_link = $request->audio_link;
        $practice->book_id = $request->book_id;
        $practice->status = $request->input('status', 0);
        $practice->sort = $request->input('sort', 0);

        $practice->getConnection()->transaction(function () use ($practice, $request) {
            if ($practice->save()) {
                $practice->tags()->sync(array_map(function ($item) {
                    return ['model_type' => Tag::TYPE_MUSIC_PRACTICE];
                }, array_flip($request->tags)));
            }
        });

        return responseSuccess([
            'music_practice_id' => $practice->id,
        ], '修改音乐练耳成功');
    }

    /**
     * 设置音乐练耳的排序
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, MusicPractice $practice)
    {
        $this->validate($request, ['sort' => 'required|integer']);

        if ($practice->update(['sort' => $request->sort])) {
            return responseSuccess();
        } else {
            return responseFailed('操作失败，请检查', 500);
        }
    }

    /**
     * 上下架一条音乐练耳
     *
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(MusicPractice $practice)
    {
        $practice->actionStatus();

        return responseSuccess();
    }

    /**
     * 删除
     *
     * @param \Modules\Course\Entities\MusicPractice $practice
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(MusicPractice $practice)
    {
        $practice->delete();

        return responseSuccess();
    }
}
