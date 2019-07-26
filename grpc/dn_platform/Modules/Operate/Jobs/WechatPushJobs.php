<?php

declare(strict_types=1);

namespace Modules\Operate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Operate\Entities\WechatPushJob;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;
use EasyWeChat\OfficialAccount\Application;

class WechatPushJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 推送Model
     *
     * @var \Modules\Operate\Entities\WechatPushJob
     */
    protected $wechatPushJob;

    /**
     * @var \EasyWeChat\OfficialAccount\Application
     */
    protected $officials;

    /**
     * @var string
     */
    private $openid;

    /**
     * @var array
     */
    private $officialConfig;

    /**
     * Create a new job instance.
     *
     * @param \Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @param string $openid
     */
    public function __construct(WechatPushJob $wechatPushJob, string $openid)
    {
        $this->wechatPushJob = $wechatPushJob;

        $this->openid = $openid;

        $this->officialConfig = config('wechat.official_account');
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     * @return void
     */
    public function handle()
    {
        Log::info($this->wechatPushJob->id . '开始推送，推送用户为：' . $this->openid);
        try {
            $this->getOfficial()->template_message->send([
                'touser' => $this->openid,
                'template_id' => $this->wechatPushJob->template->tpl_id,
                'url' => $this->wechatPushJob->url,
                'data' => $this->getRealTplParams(),
            ]);

            // Log::info([
            //     'touser' => $this->openid,
            //     'template_id' => $this->wechatPushJob->template->tpl_id,
            //     'url' => $this->wechatPushJob->url,
            //     'data' => $this->getRealTplParams(),
            // ]);

            // 推送记录
            $this->wechatPushJob->wechatPushUserLogs()->create([
                'category' => $this->wechatPushJob->category,
                'openid' => $this->openid
            ]);
            Log::info($this->openid . '成功推送');
        } catch (\Exception $exception) {
            // dump($exception->getMessage());
            Log::error($this->openid . '微信推送异常：' . $exception->getMessage());
        }
    }

    /**
     * 执行失败的任务。
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // ...
    }

    /**
     * 获取微信公众号实例
     *
     * @return \EasyWeChat\OfficialAccount\Application|null
     */
    public function getOfficial(): ?Application
    {
        $strCategory = $this->wechatPushJob->getStrCategory();
        if (! isset($this->officials[$strCategory]) && isset($this->officialConfig[$strCategory])) {
            $this->officials[$strCategory] = Factory::officialAccount($this->officialConfig[$strCategory]);
        }

        return $this->officials[$strCategory] ?? null;
    }

    /**
     * 拼接发送内容
     *
     * @return array|null
     */
    public function getRealTplParams(): ?array
    {
        $data = $this->wechatPushJob->template->content;
        $tpl_params = $this->wechatPushJob->tpl_params;
        foreach ($data as $key => $value) {
            $data[$key] = [
                isset($tpl_params[$key][0]) ? $tpl_params[$key][0] : '',
                isset($tpl_params[$key][1]) ? $tpl_params[$key][1] : '#000000'
            ];
        }

        return $data;
    }
}
