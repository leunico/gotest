<?php

namespace Modules\Operate\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;
use Modules\Operate\Entities\WechatTemplate;

class SyncOfficialAccountTemplates extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'syncOfficialAccount:templates {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步公众号模板.';

    /**
     * @var array
     */
    protected $officialConfigs;

    /**
     * @var \Modules\Operate\Entities\WechatTemplate
     */
    protected $wechatTemplate;

    /**
     * Create a new command instance.
     *
     * @param \Modules\Operate\Entities\WechatTemplate $wechatTemplate
     * @return void
     */
    public function __construct(WechatTemplate $wechatTemplate)
    {
        parent::__construct();

        $this->officialConfigs = config('wechat.official_account');

        $this->wechatTemplate = $wechatTemplate;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $type = $this->argument('type');
        if (! in_array($type, explode('|', config('wechat.official_account_no')))) {
            $this->error('Argument type is not valid.');
        }

        try {
            $official = Factory::officialAccount($this->officialConfigs[$type]);
            $templates = $official->template_message->getPrivateTemplates();
            if (isset($templates['template_list'])) {
                $type = WechatTemplate::CATEGORYS[$type];
                $wechatTemplates = $this->wechatTemplate
                    ->where('category', $type)
                    ->where('useful', WechatTemplate::USEFUL_ON)
                    ->select('id', 'tpl_id')
                    ->get();

                $templates = collect($templates['template_list']);
                $addIds = $templates->pluck('template_id')->diff($wechatTemplates->pluck('tpl_id'));
                $delIds = $wechatTemplates->pluck('tpl_id')->diff($templates->pluck('template_id'));
                $this->wechatTemplate->getConnection()->transaction(function () use ($addIds, $delIds, $templates, $type) {
                    array_map(function ($item) use ($type) {
                        WechatTemplate::create([
                            'tpl_id' => $item['template_id'],
                            'title' => $item['title'],
                            'content' => $item['content'],
                            'category' => $type
                        ]); // todo 论如何批量添加？
                    }, $templates->whereIn('template_id', $addIds)->all());

                    if ($delIds->isNotEmpty()) {
                        WechatTemplate::whereIn('tpl_id', $delIds)->update(['useful' => 0]);
                    }
                });

                $this->info('Sync Success.');
            } else {
                Log::error('获取微信公众号模板失败.');
            }
        } catch (\Exception $exception) {
            Log::error('同步公众号模板：' . $exception->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['type', InputArgument::REQUIRED, 'An type argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['type', null, InputOption::VALUE_OPTIONAL, 'An type option.', null],
        ];
    }
}
