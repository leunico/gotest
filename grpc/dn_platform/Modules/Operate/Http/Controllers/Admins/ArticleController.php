<?php

namespace Modules\Operate\Http\Controllers\Admins;

use function App\removeNullElement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Operate\Http\Requests\ArticleRequest;
use Modules\Operate\Entities\Article;
use function App\responseSuccess;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * 文章列表
     */
    public function index(Request $request)
    {
        $perPage = $perPage ?? 10;
        $data = Article::with('file')
            ->keyword($request->keyword)
            ->orderBy('created_at','DESC')
            ->paginate($perPage);
        foreach ($data as $vo){
            $vo->image = $vo->image_url;
        }
        return responseSuccess($data);
    }

    /**
     * 新增文章
     */
    public function store(ArticleRequest $request)
    {
        $form_data = $request->only([
            'title',
            'keywords',
            'description',
            'body',
            'abstract',
            'file_id',
        ]);
        $form_data['operate_id'] = Auth::id();
        $form_data['status'] = 1;
        Article::create(removeNullElement($form_data));
        return responseSuccess([]);
    }

    /**
     * 文章详情
     */
    public function show(Article $article)
    {
        $article->load(['file']);
        return responseSuccess($article);
    }

    /**
     * 更新文章
     */
    public function update(Article $article, ArticleRequest $request)
    {
        $form_data = $request->only([
            'title',
            'keywords',
            'description',
            'abstract',
            'body',
            'file_id',
        ]);
        $form_data['operate_id'] = Auth::id();
        $article->update(removeNullElement($form_data));
        return responseSuccess([]);
    }

    /**
     * 删除文章
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return responseSuccess('删除成功');
    }

    /**
     * 开启或关闭
     */
    public function status(Article $article, Request $request)
    {
        if ($request->input('status') != $article->status) {
            $article->status = $request->input('status');
            $article->save();
        }
        return responseSuccess();
    }

}
