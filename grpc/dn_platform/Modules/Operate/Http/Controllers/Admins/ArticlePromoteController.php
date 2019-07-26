<?php

namespace Modules\Operate\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Operate\Http\Requests\StoreArticlePromotePost;
use Modules\Operate\Entities\ArticlePromote;
use function App\responseSuccess;
use function App\responseFailed;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ArticlePromoteController extends Controller
{
    /**
     * 获取软文推广列表
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request, ArticlePromote $promote)
    {
        $perPage = (int) $request->input('per_page', 15);

        $keyword = $request->input('keyword', null);
        $orderPv = $request->input('pv', null);
        $orderUv = $request->input('uv', null);

        $data = $promote->select('id', 'title', 'article_id', 'name', 'wechat_number', 'status', 'pv', 'uv', 'category')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('title', 'Like', "%$keyword%");
            })
            ->when(! is_null($orderPv), function ($query) use ($orderPv) {
                return $query->orderBy('pv', $orderPv ? 'desc' : 'asc');
            })
            ->when(! is_null($orderUv), function ($query) use ($orderUv) {
                return $query->orderBy('uv', $orderUv ? 'desc' : 'asc');
            })
            ->orderBy('article_id')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return responseSuccess($data);
    }

    /**
     * 添加一条软文推广
     *
     * @param  \Modules\Operate\Http\Requests\StoreArticlePromotePost $request
     * @param  \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreArticlePromotePost $request, ArticlePromote $promote)
    {
        $promote->title = $request->title;
        $promote->article_id = $request->article_id;
        $promote->image_id = $request->image_id;
        $promote->category = $request->category;
        $promote->name = $request->input('name', '');
        $promote->wechat_number = $request->input('wechat_number', '');
        $promote->status = $promote->getRealStatus();

        if ($promote->save()) {
            return responseSuccess([
                'promote_id' => $promote->id
            ], '添加软文推广成功');
        } else {
            return responseFailed('添加软文推广失败', 500);
        }
    }

    /**
     * 获取一条软文推广
     *
     * @param  \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(ArticlePromote $promote)
    {
        $promote->load([
            'image'
        ]);

        return responseSuccess($promote);
    }

    /**
     * 添加一条软文推广
     *
     * @param  \Modules\Operate\Http\Requests\StoreArticlePromotePost $request
     * @param  \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreArticlePromotePost $request, ArticlePromote $promote)
    {
        $promote->title = $request->title;
        $promote->article_id = $request->article_id;
        $promote->image_id = $request->image_id;
        $promote->category = $request->category;
        $promote->name = $request->input('name', '');
        $promote->wechat_number = $request->input('wechat_number', '');

        if ($promote->save()) {
            return responseSuccess([
                'promote_id' => $promote->id
            ], '修改软文推广成功');
        } else {
            return responseFailed('修改软文推广失败', 500);
        }
    }

    /**
     * 软文推广开关
     *
     * @param \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function action(ArticlePromote $promote)
    {
        if (empty($promote->status)) {
            try {
                DB::beginTransaction();
                if ($promote->where('article_id', $promote->article_id)->update(['status' => ArticlePromote::STATUS_OFF])) {
                    $promote->status = ArticlePromote::STATUS_ON;
                    $promote->save();
                    DB::commit();
                } else {
                    DB::rollBack();
                    return responseFailed('修改失败：Model Field', 500);
                }
            } catch (\Exception $exception) {
                DB::rollBack();
                return responseFailed($exception->getMessage(), 500);
            }
        }

        return responseSuccess();
    }

    /**
     * 删除软文推广
     *
     * @param \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(ArticlePromote $promote)
    {
        $promote->delete();

        return responseSuccess();
    }
}
