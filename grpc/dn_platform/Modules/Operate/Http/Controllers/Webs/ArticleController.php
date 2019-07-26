<?php

namespace Modules\Operate\Http\Controllers\Webs;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Operate\Entities\Article;
use function App\responseSuccess;
use Carbon\Carbon;

class ArticleController extends Controller
{
    /**
     * 文章列表
     */
    public function index()
    {
        $perPage = $perPage ?? 8;
        $data = Article::with('file')
            ->where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        return view('operate::news.index', ['articles' => $data]);
    }

    /**
     * 文章详情
     */
    public function show($id)
    {
        $data = Article::with('file')
            ->where('status', 1)
            ->findOrFail($id);
        $data->increment('views');
        list($data->next, $data->previous) = $data->getNextOrPrevious($data->id);

        return view('operate::news.info', ['article' => $data]);
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
