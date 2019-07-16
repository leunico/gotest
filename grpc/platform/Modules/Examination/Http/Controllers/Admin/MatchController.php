<?php

namespace Modules\Examination\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Examination\Http\Controllers\ExaminationController as Controller;
use Modules\Examination\Http\Requests\StoreMatchRequest;
use Modules\Examination\Entities\Match;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class MatchController extends Controller
{
    /**
     * 赛事列表.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $keyword = $request->input('keyword', null);
        $startTime = $request->input('start_time', 0);
        $endTime = $request->input('end_time', 0);

        $data = Match::select('id', 'title', 'start_at', 'end_at', 'description', 'cover_id', 'created_at', 'creator_id')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('title', 'like', "%$keyword%");
            })
            ->when((! empty($startTime) || ! empty($endTime)), function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_at', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfday()]);
            })
            ->with([
                'cover:id,origin_filename,driver_baseurl,filename',
                'creator:id,name,real_name'
            ])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return $this->response()->success($data);
    }

    /**
     * 添加赛事.
     *
     * @param \Modules\Examination\Http\Requests\StoreMatchRequest $request
     * @param \Modules\Examination\Entities\Match $match
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function store(StoreMatchRequest $request, Match $match): JsonResponse
    {
        $match->creator_id = $this->user()->id;
        $match->title = $request->title;
        $match->start_at = $request->start_at;
        $match->end_at = $request->end_at;
        $match->description = $request->description;
        $match->cover_id = $request->cover_id;

        return $match->save() ? $this->response()->success($match) : $this->response()->error();
    }

    /**
     * 获取赛事.
     *
     * @param \Modules\Examination\Entities\Match $match
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function edit(Match $match): JsonResponse
    {
        $match->load([
            'cover:id,origin_filename,driver_baseurl,filename',
            'creator:id,name,real_name'
        ]);

        return $this->response()->success($match);
    }

    /**
     * 修改赛事.
     *
     * @param \Modules\Examination\Http\Requests\StoreMatchRequest $request
     * @param \Modules\Examination\Entities\Match $match
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function update(StoreMatchRequest $request, Match $match): JsonResponse
    {
        $match->title = $request->title;
        $match->start_at = $request->start_at;
        $match->end_at = $request->end_at;
        $match->description = $request->description;
        $match->cover_id = $request->cover_id;

        return $match->save() ? $this->response()->success($match) : $this->response()->error();
    }

    /**
     * 删除赛事.
     *
     * @param \Modules\Examination\Entities\Match $match
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function destroy(Match $match): JsonResponse
    {
        if ($match->examinations->isNotEmpty()) {
            return $this->response()->error('删除错误，赛事已有考试数据！');
        }

        $match->delete();

        return $this->response()->success();
    }
}
