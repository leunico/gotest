<?php

namespace Modules\Operate\Http\Controllers\Admins;

use function App\responseSuccess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Operate\Entities\Lead;
use Modules\Operate\Transformers\LeadResource;

class LeadsController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $leads  = Lead::when($keyword, function ($q) use ($keyword) {
            $q->where(function ($s) use ($keyword) {
                $s->where('name', 'like', "%$keyword%")
                    ->orWhere('mobile', 'like', "%$keyword%");
            });
        })->orderBy('created_at', 'desc')->paginate();


        return responseSuccess($leads);
    }

    public function dnOne2OneList(Request $request)
    {
        $keyword = $request->keyword;
        $leads  = Lead::when($keyword, function ($q) use ($keyword) {
            $q->where(function ($s) use ($keyword) {
                $s->where('name', 'like', "%$keyword%")
                    ->orWhere('mobile', 'like', "%$keyword%");
            });
        })->with('channel.level3.level2.level1')
            ->with(['platform_user' => function($q) {
                $q->with('crm_owners', 'courses')->select('id', 'mobile');
            }])
            ->with('wechat_user')
            ->where('tag', Lead::TAG_MUSIC_CONTEST)
            ->orderBy('created_at', 'desc')
            ->paginate();

        $affairMap = Lead::AFFAIR_MAP;

        foreach ($leads as $lead) {
            $lead->is_buy = empty($lead->platform_user) ? 0 : $lead->platform_user->boughtSystemCourses();
            if (!empty($lead->platform_user)) {
                unset($lead->platform_user->courses);
            }

            $lead->affair_name = $affairMap[$lead->operational_affair] ?? '未知';
        }

        return responseSuccess($leads);
    }
}
