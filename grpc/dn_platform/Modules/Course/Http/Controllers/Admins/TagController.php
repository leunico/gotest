<?php

namespace Modules\Course\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\Http\Requests\StoreTagPost;
use Modules\Course\Entities\Tag;
use function App\responseSuccess;
use function App\responseFailed;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    /**
     * 获取标签列表
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Tag $tag
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, Tag $tag)
    {
        $perPage = (int) $request->input('per_page', 15);
        $isAll = $request->input('is_all', null);

        $keyword = $request->input('keyword', null);
        $category = $request->input('category', null);

        $data = $tag->select('id', 'name', 'sort', 'category', 'cover_id')
            ->with(['cover'])
            ->when($category, function ($query) use ($category) {
                return $query->where('category', $category);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', "%$keyword%");
            })
            ->orderBy('sort')
            ->orderBy('id');

        return responseSuccess($isAll ? $data->get() : $data->paginate($perPage));
    }

    /**
     * 添加标签
     *
     * @param \Modules\Course\Http\Requests\StoreTagPost $request
     * @param \Modules\Course\Entities\Tag $tag
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreTagPost $request, Tag $tag)
    {
        $tag->name = $request->name;
        $tag->cover_id = $request->cover_id;
        $tag->category = $request->input('category', 1); // todo 暂时设为1，如果有其他类型，请加上

        if ($tag->save()) {
            return responseSuccess([
                'tag_id' => $tag->id
            ], '添加标签成功');
        } else {
            return responseFailed('添加标签失败', 500);
        }
    }

    /**
     * 修改标签
     *
     * @param \Modules\Course\Http\Requests\StoreTagPost $request
     * @param \Modules\Course\Entities\Tag $tag
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreTagPost $request, Tag $tag)
    {
        $tag->name = $request->name;
        $tag->cover_id = $request->cover_id;
        $tag->category = $request->input('category', 1); // todo 暂时设为1

        if ($tag->save()) {
            return responseSuccess([
                'tag_id' => $tag->id
            ], '修改标签成功');
        } else {
            return responseFailed('修改标签失败', 500);
        }
    }

    /**
     * 获取一条标签
     *
     * @param \Modules\Course\Entities\Tag $tag
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Tag $tag)
    {
        $tag->load([
            'cover'
        ]);

        return responseSuccess($tag);
    }

    /**
     * 设置标签的排序
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Course\Entities\Tag $tag
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sort(Request $request, Tag $tag)
    {
        $this->validate($request, ['sort' => 'required|integer']);

        if ($tag->update(['sort' => $request->sort])) {
            return responseSuccess();
        } else {
            return responseFailed('操作失败，请检查', 500);
        }
    }

    /**
     * 删除
     *
     * @param \Modules\Course\Entities\Tag $tag
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return responseSuccess();
    }
}
