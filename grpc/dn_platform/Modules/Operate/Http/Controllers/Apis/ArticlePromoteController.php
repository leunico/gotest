<?php

namespace Modules\Operate\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Operate\Entities\ArticlePromote;
use Modules\Operate\Transformers\ArticlePromoteResource;
use Modules\Operate\Jobs\OperateIncrementJob;

class ArticlePromoteController extends Controller
{
    /**
     * 获取软文推广
     *
     * @param int $article
     * @param  \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function show(ArticlePromote $promote, int $article)
    {
        $data = $promote->select('id', 'title', 'article_id', 'image_id', 'name', 'wechat_number')
            ->where('article_id', $article)
            ->where('status', ArticlePromote::STATUS_ON)
            ->with(['image'])
            ->first();

        if (empty($data)) {
            return $this->response()->errorNotFound('推广不存在');
        }

        return $this->response()->item($data, ArticlePromoteResource::class);
    }

    /**
     * 软文推广Pv[+1]
     *
     * @param  \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setPv(ArticlePromote $promote)
    {
        // $promote->increment('pv');
        OperateIncrementJob::dispatch($promote, 'pv');

        return $this->response()->success();
    }

    /**
     * 软文推广Uv[+1]
     *
     * @param  \Modules\Operate\Entities\ArticlePromote $promote
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function setUv(ArticlePromote $promote)
    {
        // $promote->increment('uv');
        OperateIncrementJob::dispatch($promote, 'uv');

        return $this->response()->success();
    }
}
