<?php

namespace Modules\Educational\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Operate\Entities\StarPackageUser;
use Modules\Operate\Events\OrderChange;

class AddStarForStarPackageOrder
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

        if ($order->isStarPackageOrder()) {
            $user = $order->user;

            //增加流水记录
            $starTotal = $order->goods->sum('star');
            $user->addUserOrder($order, 1, $starTotal,'生成订单');

            //add record to star_package_users table
            StarPackageUser::assign($order);

        }
    }
}
