<?php

namespace Modules\Operate\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Operate\Entities\Article;
use function App\responseSuccess;

class ArticleController extends Controller
{
    /**
     * 文章列表
     */
    public function index(Request $request)
    {
        $perPage = $perPage ?? 8;
        $data = Article::with('file')
            ->where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
        foreach ($data as $vo) {
            $vo->image = $vo->image_url;
            $vo->date = $vo->created_at->format('Y年m月d日');
            unset($vo->file);
            unset($vo->body);
        }
        return responseSuccess($data);
    }

    /**
     * 文章详情
     */
    public function show($id)
    {
        $data = Article::with('file')
            ->where('status', 1)
            ->findOrFail($id);
        $data->image = $data->image_url;
        list($data->next, $data->previous) = $data->getNextOrPrevious($data->id);
        unset($data->file);
        return responseSuccess($data);
    }

    /**
     * 文章浏览
     */
    public function browse(Article $article)
    {
        $article->increment('views');
        return responseSuccess([]);
    }


}
