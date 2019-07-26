<?php

namespace Modules\Crm\Http\Controllers\Api;

use function App\errorLog;
use function App\iteratorGet;
use function App\responseFailed;
use function App\responseSuccess;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;
use Modules\Crm\Entities\Channel;

/**
 * Class ChannelController
 * @package Modules\Crm\Http\Controllers\Api
 */
class ChannelsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $channels = Channel::query()->get();
            return responseSuccess($channels);
        } catch (\Exception $e) {
            errorLog($e);
            return responseFailed($e->getMessage());
        }
    }

    /**
     * 新增渠道
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try {

            if (empty($request->slug)) {
                return responseFailed('slug不能为空！');
            }
            //  上级渠道id，通过传递的sup_slug获取上级渠道id
            $parentChannel = Channel::query()->where('slug', $request->sup_slug)->first();
            $parentId      = iteratorGet($parentChannel, 'id', 0);


            //  组装数据
            $insert = [
                'category'    => $request->type,
                'owner_id'    => iteratorGet(User::query()->find($request->owner_id), 'id', 0),
                'level'       => $request->level,
                'level1_id'   => $request->l1_id ?? 0,
                'level2_id'   => $request->l2_id ?? 0,
                'level3_id'   => $request->l3_id ?? 0,
                'parent_id'   => $parentId,
                'title'       => $request->title,
                'description' => $request->description ?? '',
                'link'        => $request->link,
                'created_at'  => $request->created_at,
                'updated_at'  => $request->updated_at,
                'deleted_at'  => $request->deleted_at,
            ];
            Channel::query()->updateOrCreate(['slug' => $request->slug, 'id' => $request->id], $insert);
            return responseSuccess([], '同步成功！');
        } catch (\Exception $e) {
            errorLog($e, '同步渠道时出错：');
            return responseFailed($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $channel = Channel::query()->where('slug', $request->get('slug', ''))->first();
            if (empty($channel)) {
                throw new \Exception('同步删除渠道出错：找不到要删除的 ' . $request->get('slug') . ' 渠道');
            }
            $channel->delete();
            return responseSuccess();
        } catch (\Exception $e) {
            errorLog($e);
            return responseFailed($e->getMessage());
        }
    }
}
