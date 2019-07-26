<?php

namespace Modules\Personal\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use EasyWeChat\Factory;
use Carbon\Carbon;
use Modules\Personal\Entities\Delivery;

class AddDeliveryMessageReminder extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'AddDeliveryMessageReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '添加寄件推送消息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::now()->subHours(1);
        $delivery = Delivery::with([
            'expressUser.course',
            'expressUser.user.wechatUser'
        ])
            ->whereBetween('created_at', [Carbon::now()->subHours(5), $date])
            ->where('push_wechat', 0)
            ->get();
        if ($delivery->isNotEmpty()) {
            //艺术编程
            $app = Factory::officialAccount(config('wechat.official_account.art'));
            $template = config('wechat.template.art.add_delivery');
            //数字音乐
            $musicApp = Factory::officialAccount(config('wechat.official_account.music'));
            $musicTpl = config('wechat.template.music.add_delivery');

            try {
                foreach ($delivery as $vo) {
                    $course_category = $vo->expressUser->course->category == 1 ? 'art' : 'music';
                    $url = config('services.study_domain') . '/mobile_logistics/' . $course_category . '?';
                    $param_arr['company'] = $vo->express_company;
                    $param_arr['number'] = $vo->track_number;
                    $param_arr['time'] = $vo->send_at;
                    $param = http_build_query($param_arr);
                    //艺术编程
                    if ($course_category == 'art' and !empty($vo->expressUser->user->wechatUser->art_openid)) {
                        $res = $app->template_message->send([
                            'touser' => $vo->expressUser->user->wechatUser->art_openid,
                            'template_id' => $template['id'],
                            'url' => $url . $param,
                            'data' => [
                                'first' => '您的上课物料已经发货!',
                                'keyword1' => $vo->expressUser->course->title,
                                'keyword2' => $vo->track_number,
                                'keyword3' => $vo->express_company,
                                'remark' => '【点击此处】查看物流信息',
                            ],
                        ]);
                        if ($res['errcode'] == 0) {
                            $vo->push_wechat = 1;
                            $vo->save();
                        }
                    }
                    //数字音乐
                    if ($course_category == 'music' and !empty($vo->expressUser->user->wechatUser->music_openid)) {
                        $res = $musicApp->template_message->send([
                            'touser' => $vo->expressUser->user->wechatUser->music_openid,
                            'template_id' => $musicTpl['id'],
                            'url' => $url . $param,
                            'data' => [
                                'first' => '您的上课物料已经发货!',
                                'keyword1' => $vo->expressUser->course->title,
                                'keyword2' => $vo->track_number,
                                'keyword3' => $vo->express_company,
                                'remark' => '【点击此处】查看物流信息',
                            ],
                        ]);
                        if ($res['errcode'] == 0) {
                            $vo->push_wechat = 1;
                            $vo->save();
                        }
                    }
                }
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
                Log::error('模板消息发送失败：' . $exception->getTraceAsString());
            }
        }

    }

}
