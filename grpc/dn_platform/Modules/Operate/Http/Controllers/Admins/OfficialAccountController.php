<?php

namespace Modules\Operate\Http\Controllers\Admins;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Operate\Http\Requests\StoreOfficialAccountPost;
use Modules\Operate\Entities\WechatPushJob;
use function App\responseSuccess;
use Modules\Operate\Entities\WechatTemplate;
use Modules\Operate\Entities\WechatUserTag;
use Illuminate\Support\Facades\Artisan;

class OfficialAccountController extends Controller
{
    /**
     * 获取推送列表.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function pushs(Request $request, WechatPushJob $wechatPushJob, string $type)
    {
        $perPage = (int) $request->input('per_page', 15);

        $data = $wechatPushJob->where('category', WechatPushJob::CATEGORYS[$type])
            ->select('id', 'wechat_template_id', 'category', 'push_at', 'tpl_params', 'url', 'is_push')
            ->with([
                'tags' => function ($query) {
                    $query->select('wechat_user_tags.id', 'name', 'wechat_tag_id')
                        ->where('useful', WechatUserTag::USEFUL_ON);
                },
                'template' => function ($query) {
                    $query->select('id', 'tpl_id', 'title');
                }
            ])
            ->paginate($perPage);

        // collect($data->items())->map(function ($item) { // todo 不利于判断
        //     $item->str_push = $item->str_push;
        // });

        return responseSuccess($data, 'Success.', ['pushMap' => WechatPushJob::$pushStatusMap]);
    }

    /**
     * 获取模板列表.
     *
     * @param \Modules\Operate\Entities\WechatTemplate $wechatTemplate
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function templates(WechatTemplate $wechatTemplate, string $type)
    {
        $data = $wechatTemplate->where('category', WechatTemplate::CATEGORYS[$type])
            ->select('id', 'tpl_id', 'title', 'content')
            ->where('useful', WechatTemplate::USEFUL_ON)
            ->get();

        return responseSuccess($data);
    }

    /**
     * 获取用户标签列表.
     *
     * @param \Modules\Operate\Entities\WechatUserTag $wechatUserTag
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function userTags(WechatUserTag $wechatUserTag, string $type)
    {
        $data = $wechatUserTag->where('category', WechatUserTag::CATEGORYS[$type])
            ->select('id', 'wechat_tag_id', 'name', 'category', 'useful')
            ->where('useful', WechatUserTag::USEFUL_ON)
            ->get();

        return responseSuccess($data);
    }

    /**
     * 同步微信信息.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function sync(Request $request, string $type)
    {
        $this->validate($request, ['category' => 'string|in:userTags,templates']);

        $category = $request->input('category', 'templates');
        $exitCode = Artisan::call("syncOfficialAccount:{$category} {type}", ['type' => $type]);

        return responseSuccess($exitCode);
    }

    /**
     * 添加一条微信的推送
     *
     * @param  \Modules\Operate\Http\Requests\StoreOfficialAccountPost $request
     * @param  \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function pushStore(StoreOfficialAccountPost $request, WechatPushJob $wechatPushJob)
    {
        $wechatPushJob->creator_id = $request->user()->id;
        $wechatPushJob->wechat_template_id = $request->wechat_template_id;
        $wechatPushJob->category = $request->category;
        $wechatPushJob->push_at = $request->push_at;
        $wechatPushJob->tpl_params = $request->tpl_params;
        $wechatPushJob->url = $request->url;

        $wechatPushJob->getConnection()->transaction(function ($query) use ($wechatPushJob, $request) {
            if ($wechatPushJob->save()) {
                $wechatPushJob->tags()->attach($request->tags);
            }
        });

        return responseSuccess([
            'job_id' => $wechatPushJob->id
        ], '添加微信推送成功');
    }

    /**
     * 获取一条微信的推送
     *
     * @param \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function pushEdit(WechatPushJob $push)
    {
        $push->load([
            'tags' => function ($query) {
                $query->select('wechat_user_tags.id', 'name', 'wechat_tag_id')
                    ->where('useful', WechatUserTag::USEFUL_ON);
            },
            'template' => function ($query) {
                $query->select('id', 'tpl_id', 'title', 'content')
                    ->where('useful', WechatTemplate::USEFUL_ON);
            }
        ]);

        return responseSuccess($push);
    }

    /**
     * 修改一条微信的推送.
     *
     * @param  \Modules\Operate\Http\Requests\StoreOfficialAccountPost $request
     * @param  \Modules\Operate\Entities\WechatPushJob $push
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function pushUpdate(StoreOfficialAccountPost $request, WechatPushJob $push)
    {
        $push->wechat_template_id = $request->wechat_template_id;
        $push->category = $request->category;
        $push->push_at = $request->push_at;
        $push->tpl_params = $request->tpl_params;
        $push->url = $request->url;

        $push->getConnection()->transaction(function ($query) use ($push, $request) {
            if ($push->save()) {
                $push->tags()->sync($request->tags);
            }
        });

        return responseSuccess([
            'job_id' => $push->id
        ], '修改微信推送成功');
    }

    /**
     * 删除微信的推送
     *
     * @param \Modules\Operate\Entities\WechatPushJob $push
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function pushDestroy(WechatPushJob $push)
    {
        $push->delete();

        return responseSuccess();
    }
}
