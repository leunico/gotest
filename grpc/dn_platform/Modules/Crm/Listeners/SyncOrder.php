<?php

namespace Modules\Crm\Listeners;

use function App\arrGet;
use function App\errorLog;
use function App\jsonGet;
use function App\requestCodeProject;
use function App\toCarbon;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Modules\Operate\Entities\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Exception;
use Modules\Operate\Events\OrderChange;

/**
 * Class SyncOrder
 * @package Modules\Crm\Listeners
 */
class SyncOrder implements ShouldQueue
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
     * @param  OrderChange $event
     * @return bool
     */
    public function handle(OrderChange $event)
    {
        $action = $event->action;
        $order  = $event->order;

        \Log::notice('order action：' . $action . '--' . json_encode($order));

        //  只同步已支付订单
        if (!$order->isPaid()) {
            return false;
        }

        switch ($action) {
            case 'create':
            case 'update':
                $this->updateOrCreateOrder($order);
                break;
            case 'delete':
                $this->deleteOrder($order);
                break;
        }
    }

    /**
     * @param Order $order
     */
    protected function updateOrCreateOrder(Order $order)
    {
        try {
            $nowTime  = Carbon::now();
            $paidTime = toCarbon(jsonGet($order, 'paid_at'));

            $dateFormat = 'Y-m-d H:i:s';
            $user       = User::query()->findOrFail($order->user_id);
            $userData   = $user->only([
                'name',
                'grade',
                'age',
                'sex',
                'real_name',
                'phone',
                'channel_id',
            ]);
            //  订单数据
            $insert                   = array_merge($order->getAttributes(), $userData);
            $insert['paid_at']        = $nowTime->lt($paidTime) ? $nowTime->format($dateFormat) : $paidTime->format($dateFormat);
            $insert['original_price'] = $insert['total_price'];
            $insert['payment_price']  = $insert['real_price'];

            $insert['goods'] = [];
            //  获取商品数据
            foreach ($order->goods as $good) {
                //  商品信息
                $insert['goods'][] = [
                    'goods_id'      => $good->goods_id,
                    'goods_type'    => $good->goods_type,
                    'goods_title'   => $good->goods_title,
                    'goods_price'   => $good->goods_price,
                    'payment_price' => $good->payment_price
                ];
            }
            if (empty($insert['goods'])) {
                $this->deleteOrder($order);
                throw new Exception($order->trade_no . ' 订单内不含有效商品，停止同步到CRM...');
            }
            Log::notice('订单同步到CRM中 | ' . __CLASS__ . ' | ' . $order->trade_no . ' 订单正在同步到CRM...');

            //  接口鉴权
            list($json, $msg) = requestCodeProject('crm', '/orders/store', 'POST', $insert, true);
            if ($msg) {
                throw new Exception($msg);
            }

            Log::debug('订单同步 ' . $order->trade_no . ' 成功！');
        } catch (ClientException $e) {
            errorLog($e, '订单同步到CRM失败 | ' . __CLASS__ . ' |  ' . $order->trade_no . ' | ');
            Log::error($e->getResponse()->getBody());
        } catch (\Exception $e) {
            errorLog($e, '订单同步到CRM失败 | ' . __CLASS__ . ' | ' . $order->trade_no . ' | ');
        }
    }

    /**
     * @param Order $order
     */
    protected function deleteOrder(Order $order)
    {
        try {
            $formParams = ['trade_no' => $order->out_id];
            Log::notice(__CLASS__ . ' | ' . $order->out_id . ' | ' . '正在同步删除CRM的订单');
            list($json, $msg) = requestCodeProject('crm', '/orders/destroy', 'DELETE', $formParams, true);
            if ($msg) {
                throw new Exception($msg);
            }
            Log::notice(__CLASS__ . ' | ' . $order->out_id . ' | ' . 'CRM的 ' . $order->out_id . ' 订单已同步删除');
        } catch (ClientException $e) {
            errorLog($e, __CLASS__ . ' | ' . $order->out_id . ' | ', 'debug');
        } catch (\Exception $e) {
            errorLog($e, __CLASS__ . ' | ' . $order->out_id . ' | ', 'debug');
        }
    }
}
