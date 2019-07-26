<?php

namespace Modules\Crm\Listeners;

use function App\arrGet;
use function App\errorLog;
use function App\requestCodeProject;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Modules\Personal\Events\ChangeUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;
use Log;

class SyncUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * 任务应该发送到的队列的连接的名称
     *
     * @var string|null
     */
    public $connection = 'sync';

    /**
     * 任务应该发送到的队列的优先级
     *
     * @var string|null
     */
    public $queue = 'high';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = config('queue.default');
    }

    /**
     * Handle the event.
     *
     * @param  ChangeUser $event
     * @return void
     */
    public function handle(ChangeUser $event)
    {
        $action = $event->action;
        $user   = $event->user;

        \Log::notice('user action：' . $action . '--' . json_encode($user));

        switch ($action) {
            case 'create':
            case 'update':
                $this->updateOrCreateUser($user);
                break;
            case 'delete':
                break;
        }
    }

    /**
     * @param User $user
     */
    protected function updateOrCreateUser(User $user)
    {
        try {
            $nowTime = Carbon::now();

            //  用户数据
            $insert = $user->only([
                'id',
                'name',
                'grade',
                'age',
                'sex',
                'real_name',
                'phone',
                'channel_id',
            ]);;
            $insert['user_id'] = $insert['id'];
            Log::notice('用户同步到CRM中 | ' . __CLASS__ . ' | ' . $user->id . ' 用户正在同步到CRM...');

            //  接口鉴权
            list($json, $msg) = requestCodeProject('crm', '/leads/sync_with_platform', 'POST', $insert, true);
            if ($msg) {
                throw new Exception($msg);
            }

            Log::debug('用户同步 ' . $user->id . ' 成功！');
        } catch (ClientException $e) {
            errorLog($e, '用户同步到CRM失败 | ' . __CLASS__ . ' |  ' . $user->id . ' | ');
            Log::error($e->getResponse()->getBody());
        } catch (\Exception $e) {
            errorLog($e, '用户同步到CRM失败 | ' . __CLASS__ . ' | ' . $user->id . ' | ');
        }
    }
}
