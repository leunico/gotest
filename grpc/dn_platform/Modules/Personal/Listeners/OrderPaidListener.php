<?php

namespace Modules\Personal\Listeners;

use function App\errorLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Operate\Events\OrderChange;
use Modules\Personal\Entities\CourseUser;
use Modules\Personal\Entities\ExpressUser;

class OrderPaidListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderChange $event)
    {
        $order = $event->getOrder();
        $action = $event->action;

        //只有新建的已支付的订单才会触发
        if (! $order->isPaid() || $action != 'create') {
            return false;
        }
        if ($order->isCourseOrder()) {
            \Log::info('新的已支付订单：'. $order->trade_no . '，正在分配课程权限...');

            try {
                if (CourseUser::assign($order)) {
                    \Log::info($order->trade_no . " 分配课程权限成功");
                }

                //待寄件记录
                if (ExpressUser::assign($order)) {
                    Log::info($order->trade_no . " 分配待寄件记录成功");
                }

                $this->sendTplRemind($order);
            } catch (\Exception $exception) {
                Log::error('Personal OrderPaidListener handle error ：' . $exception->getTraceAsString());
            }
        }

    }


    protected function sendTplRemind($order)
    {
        try {
            $user = $order->user;
            $wechatUser = $user->wechatUser;

            if ($order->isArt() and !empty($wechatUser) and $wechatUser->art_openid) {
                $app = app('wechat.official_account.art');

                $needFillReceipt = (string) (!$user->hasFilledReceipt() and $order->containNeedMailCourse());

                $url = config('services.study_domain') . "/mobile_notice/art?need_fill_receipt=$needFillReceipt";
                $res = $app->template_message->send([
                    'touser' => $wechatUser->art_openid,
                    'template_id' => config('wechat.template.art.purchase_success.id'),
                    'url' => $url,
                    'data' => [
                        'first' => '恭喜你报名成功，获得上课资格！',
                        'keyword1' => $order->goodsTitle(),
                        'keyword2' => '报名成功',
                        'keyword3' => $order->paid_at->toDateTimeString(),
                        'remark' => '【点击这里】查看上课须知'
                    ]
                ]);

                Log::info('art purchase success, wechat push res ：', (array) $res);


            } elseif ($order->isMusic() and !empty($wechatUser) and $wechatUser->music_openid) {
                $app = app('wechat.official_account.music');
                $needFillReceipt = (string) (!$user->hasFilledReceipt() and $order->containNeedMailCourse());
                $url = config('services.study_domain') . "/mobile_notice/music?need_fill_receipt=$needFillReceipt";

                $res = $app->template_message->send([
                    'touser' => $wechatUser->music_openid,
                    'template_id' => config('wechat.template.music.purchase_success.id'),
                    'url' => $url,
                    'data' => [
                        'first' => '恭喜你报名成功，获得上课资格！',
                        'keyword1' => $order->goodsTitle(),
                        'keyword2' => '报名成功',
                        'keyword3' => $order->paid_at->toDateTimeString(),
                        'remark' => '【点击这里】查看上课须知'
                    ]
                ]);

                Log::info('music purchase success, wechat push res ：', (array) $res);
            }
        } catch (\Exception $exception) {
            Log::warning("订单号 {$order->trade_no} | 发送模板消息提醒失败");
            Log::error($exception->getTraceAsString());
        }
    }
}
