<?php

namespace Modules\Operate\Events;

use Modules\Operate\Entities\Order;
use Illuminate\Queue\SerializesModels;

class OrderChange
{
    use SerializesModels;

    /**
     * @var Order
     */
    public $order;
    /**
     * 表示此事件的动作 create 表示创建  update表示更新  delete表示删除
     * @var string
     */
    public $action;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order, $action = 'create')
    {
        $this->order = $order;
        $this->action = $action;
    }

    /**
     * Retrieve the paid order.
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
