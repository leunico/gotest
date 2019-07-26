<?php

declare(strict_types=1);

namespace Modules\Operate\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;
use Modules\Operate\Entities\WechatPushJob;
use Modules\Operate\Entities\WechatUserTag;
use Modules\Operate\Entities\WechatTemplate;
use Illuminate\Support\Carbon;
use Modules\Operate\Jobs\WechatPushJobs;
use EasyWeChat\OfficialAccount\Application;

class WechatPushJobHandle extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wechatPushJob:handle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '处理微信推送队列';

    /**
     * @var \Modules\Operate\Entities\WechatPushJob
     */
    protected $wechatPushJob;

    /**
     * @var array
     */
    protected $officials = [];

    /**
     * @var array
     */
    protected $officialConfig;

    /**
     * @var array
     */
    protected $mockTemplates;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WechatPushJob $wechatPushJob)
    {
        parent::__construct();

        $this->wechatPushJob = $wechatPushJob;

        $this->mockTemplates = config('wechat.template_seed_mock');

        $this->officialConfig = config('wechat.official_account');
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        try {
            $pushs = $this->wechatPushJob
                ->where('is_push', WechatPushJob::IS_PUSH_NO)
                ->with([
                    'tags' => function ($query) {
                        $query->select('wechat_user_tags.id', 'name', 'wechat_tag_id')
                            ->where('useful', WechatUserTag::USEFUL_ON);
                    },
                    'template' => function ($query) {
                        $query->select('id', 'tpl_id', 'title')
                            ->where('useful', WechatTemplate::USEFUL_ON);
                    }
                ])
                ->orderBy('category')
                ->get();

            if ($pushs->isNotEmpty()) {
                $now = Carbon::now();
                $pushs->map(function ($item) use ($now) {
                    if (! empty($item->template)) {
                        if ($now->gt(Carbon::parse($item->push_at)) && $item->tags) {
                            $openids = $this->getOpenidsOfTag($item);
                            if (! empty($openids)) {
                                array_map(function ($id) use ($item) {
                                    WechatPushJobs::dispatch($item, $id);
                                }, $openids);
                                $item->is_push = WechatPushJob::IS_PUSH_OK;
                            } else {
                                $item->is_push = WechatPushJob::IS_PUSH_UNOPENID;
                            }
                        }
                    } else {
                        $item->is_push = WechatPushJob::IS_PUSH_UNTEMPLATE;
                        Log::error('微信模板错误：' . $item->id);
                    }

                    $item->save();
                    $this->info('Success Push Job：' . $item->id);
                });
            }
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            Log::error('处理微信推送列表异常：' . $exception->getMessage());
        }
    }

    /**
     * 获取微信公众号实例
     *
     * @param string $category
     * @return \EasyWeChat\OfficialAccount\Application|null
     */
    private function getOfficial(string $category): ?Application
    {
        if (! isset($this->officials[$category]) && isset($this->officialConfig[$category])) {
            $this->officials[$category] = Factory::officialAccount($this->officialConfig[$category]);
        }

        return $this->officials[$category] ?? null;
    }

    /**
     * 获取测试标签用户的Openid
     *
     * @param \Modules\Operate\Entities\\Modules\Operate\Entities\WechatPushJob $wechatPushJob
     * @return array
     */
    private function getOpenidsOfTag(WechatPushJob $wechatPushJob): array
    {
        $tagIds = $openids = [];
        $strCategory = $wechatPushJob->getStrCategory();
        if (($this->mockTemplates['is_mock'] || $wechatPushJob->tags->isEmpty()) &&
            isset($this->mockTemplates[$strCategory]) &&
            ! empty($this->mockTemplates[$strCategory])) {
            $tagIds = str_contains('|', $this->mockTemplates[$strCategory]) ?
                explode('|', $this->mockTemplates[$strCategory]) :
                [$this->mockTemplates[$strCategory]];
        } else {
            $tagIds = $wechatPushJob->tags->pluck('wechat_tag_id')->toArray();
        }

        foreach ($tagIds as $value) {
            $users = $this->getOfficial($strCategory)->user_tag->usersOfTag((int) $value);
            // Log::info($users);
            if (isset($users['data']['openid'])) {
                $openids = array_merge($openids, $users['data']['openid']);
            }
        }

        return array_unique($openids);
    }
}
