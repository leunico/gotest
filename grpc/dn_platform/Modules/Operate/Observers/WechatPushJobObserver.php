<?php

namespace Modules\Operate\Observers;

use Modules\Operate\Entities\WechatPushJob;
use Modules\Operate\Entities\WechatTemplate;
use Modules\Operate\Entities\WechatUserTag;

class WechatPushJobObserver
{
    /**
     * 监听创建推送事件.
     *
     * @param \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @return void
     * @author lizx
     */
    public function created(WechatPushJob $wechatPushJob)
    {
        // ...
    }

    /**
     * 监听创建推送事件.
     *
     * @param  \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @return void
     * @author lizx
     */
    public function saved(WechatPushJob $wechatPushJob)
    {
        // ...
    }

    /**
     * 监听修改推送事件.
     *
     * @param  \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @return void
     * @author lizx
     */
    public function updated(WechatPushJob $wechatPushJob)
    {
        // ...
    }

    /**
     * 监听删除推送事件.
     *
     * @param  \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @return void
     * @author lizx
     */
    public function deleting(WechatPushJob $wechatPushJob)
    {
        $wechatPushJob->tagsPivot()->delete();
    }
}
