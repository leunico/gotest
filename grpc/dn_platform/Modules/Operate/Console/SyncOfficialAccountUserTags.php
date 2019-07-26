<?php

namespace Modules\Operate\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;
use Modules\Operate\Entities\WechatUserTag;

class SyncOfficialAccountUserTags extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'syncOfficialAccount:userTags {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步公众号用户标签.';

    /**
     * @var array
     */
    protected $officialConfigs;

    /**
     * @var \Modules\Operate\Entities\WechatUserTag
     */
    protected $wechatUserTag;

    /**
     * Create a new command instance.
     *
     * @param \Modules\Operate\Entities\WechatUserTag $wechatUserTag
     * @return void
     */
    public function __construct(WechatUserTag $wechatUserTag)
    {
        parent::__construct();

        $this->officialConfigs = config('wechat.official_account');

        $this->wechatUserTag = $wechatUserTag;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');
        if (! in_array($type, explode('|', config('wechat.official_account_no')))) {
            $this->error('Argument type is not valid.');
        }

        try {
            $official = Factory::officialAccount($this->officialConfigs[$type]);
            $templates = $official->user_tag->list();
            if (isset($templates['tags'])) {
                $type = WechatUserTag::CATEGORYS[$type];
                $wechatUserTags = $this->wechatUserTag
                    ->where('category', $type)
                    ->where('useful', WechatUserTag::USEFUL_ON)
                    ->select('id', 'wechat_tag_id')
                    ->get();

                $tags = collect($templates['tags']);
                $addIds = $tags->pluck('id')->diff($wechatUserTags->pluck('wechat_tag_id'));
                $delIds = $wechatUserTags->pluck('wechat_tag_id')->diff($tags->pluck('id'));
                $this->wechatUserTag->getConnection()->transaction(function () use ($addIds, $delIds, $tags, $type, $wechatUserTags) {
                    $keyTags = $tags->keyBy('id');
                    $wechatUserTags->map(function ($item) use ($keyTags) {
                        if (($val = $keyTags->get($item->wechat_tag_id)) && $val['name'] != $item->name) {
                            $item->name = $val['name'];
                            $item->save();
                        }
                    });

                    array_map(function ($item) use ($type) {
                        WechatUserTag::create([
                            'wechat_tag_id' => $item['id'],
                            'name' => $item['name'],
                            'category' => $type
                        ]);
                    }, $tags->whereIn('id', $addIds)->all());

                    if ($delIds->isNotEmpty()) {
                        WechatUserTag::whereIn('wechat_tag_id', $delIds)->update(['useful' => 0]);
                    }
                });

                $this->info('Sync Success.');
            } else {
                Log::error('获取微信公众号用户标签失败.');
            }
        } catch (\Exception $exception) {
            Log::error('同步公众号用户标签：' . $exception->getMessage());
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
