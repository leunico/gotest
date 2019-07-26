<?php

namespace Modules\Personal\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use EasyWeChat\Factory;
use Carbon\Carbon;
use Modules\Personal\Entities\ExpressUser;
use Illuminate\Support\Facades\Log;

class DeliveryMessageReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $system;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $express_users = ExpressUser::with([
                'user' => function ($u) {
                    $u->with(['wechatUser']);
                },
                'course' => function ($query) {
                    $query->select('id', 'category');
                }
            ])->where('send_status', 1)
                ->whereNull('remind_time')
                ->get();
            $order_ids = [];
            foreach ($express_users as $vo) {
                if (!empty($vo->course) && !empty($vo->user->wechatUser)) {
                    if ($vo->course->category == 1) {
                        $openid = $vo->user->wechatUser->art_openid;
                    } else {
                        $openid = $vo->user->wechatUser->music_openid;
                    }
                    if (empty($vo->user->is_address) && !empty($openid)) {
                        if(!in_array($vo->order_id, $order_ids)){
                            $course_category = $vo->course->category == 1 ? 'art' : 'music';
                            $url = config('services.study_domain') . '/mobile_complete_info/' . $course_category;
                            $data_type = [
                                'category' => $vo->course->category,
                                'openid' => $openid,
                                'url' => $url,
                                'name' => $vo->user->name
                            ];
                            $res = ExpressUser::perfectAddress($data_type);
                            if ($res['errcode'] == 0) {
                                $vo->remind_time = Carbon::now();
                                $vo->save();
                                $order_ids[] = $vo->order_id;
                            } else {
                                Log::info($vo->user->name . '模板消息队列发送失败：' . $res['errmsg']);
                            }
                        }else{
                            $vo->remind_time = Carbon::now();
                            $vo->save();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::info('模板消息队列异常退出：' . $exception->getMessage());
        }

    }
}
