<?php

namespace Modules\Crm\Http\Controllers\Api;

use function App\errorLog;
use function App\iteratorGet;
use function App\responseFailed;
use function App\responseSuccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Operate\Entities\Order;

class OrdersController extends Controller
{
    public function unpaid(Request $request)
    {
        try {
            $input = $request->all();
            $sql = Order::with([
                'user',
                'good' => function ($query) {
                    $query->select('order_id', 'goods_id', 'goods_type', 'goods_title', 'goods_price');
                }
            ])->where('orders.is_paid', 0);

            //  是否存在开始时间
            if (isset($input['created_at_start']) && strtotime($input['created_at_start'])) {
                $start = Carbon::parse($input['created_at_start'])->startOfDay();
                $sql->where('orders.created_at', '>=', $start);
            }

            if (isset($input['created_at_end']) && strtotime($input['created_at_end'])) {
                $end = Carbon::parse($input['created_at_end'])->endOfDay();
                $sql->where('orders.created_at', '<=', $end);
            }
            //  协议类型
            if (isset($input['policy_type'])) {
                if ('v2' == $input['policy_type']) {
                    $sql->where('orders.policy_type', 'v2');
                } else {
                    $sql->where(function ($query) {
                        $query->where('orders.policy_type', '!=', 'v2')->orWhereNull('policy_type');
                    });
                }
            }
            //  订单号
            if (isset($input['trade_no'])) {
                $sql->where('orders.out_id', 'like', $input['trade_no'] . '%');
            }
            $applyTerm            = iteratorGet($input, 'apply_term', -1);
            $isOnlyGetInstallment = false;
            $isGetInstallment     = false;
            //  订单分期
            switch ($applyTerm) {
                case  0:
                    \Log::info(0);
                    $sql->where('orders.audit_status', '-1');
                    break;
                case 6:
                    \Log::info(6);
                    $isOnlyGetInstallment = $isGetInstallment = true;
                    $sql->join('order_installments as os', 'orders.id', '=', 'os.order_id')->where('os.apply_term',
                        6)->where('orders.payment_discount', 'aihaimi')->where('orders.audit_status', '>', '-1');
                    break;
                case 12:
                    \Log::info(12);
                    $isOnlyGetInstallment = $isGetInstallment = true;
                    $sql->join('order_installments as os', 'orders.id', '=', 'os.order_id')->where('os.apply_term',
                        12)->where('orders.payment_discount', 'aihaimi')->where('orders.audit_status', '>', '-1');
                    break;
                case -1:
                    \Log::info(1);
                    $isGetInstallment = true;
                    $sql->leftJoin('order_installments as os', 'orders.id', '=', 'os.order_id');
                    break;
            }

            //  订单状态
            if (isset($input['status']) && in_array(request('status'), [0, 1])) {
                $sql->notExpired($request->status);
            }
            //  客户负责人
            //  在CRM关联获取用户id（array）然后传递给接口
            if (isset($input['user_ids'])) {
                $sql->whereIn('orders.user_id', explode(',', $input['user_ids']));
            }

            //  分页器
            $pageSize = isset($input['pg_size']) ? $input['pg_size'] : 10;
            //  排序字段
            $sortBy = 'orders.' . (request()->has('pg_sort') ? request('pg_sort') : 'created_at');
            //  排序方式
            $sortType = strtoupper('asc' == request('pg_order') ? 'asc' : 'desc');
            $fields   = [
                'orders.policy_type',
                'orders.order_status',
                'orders.created_at',
                'orders.total_price',
                'orders.out_id as trade_no',
                'orders.user_id',
                'orders.id',
                'orders.expired_at',
                'orders.order_attr'
            ];
            if ($isGetInstallment) {
                array_push($fields, 'os.apply_term');
            }
            $orderFields = ['orders.created_at'];
            //  判断是否能排序
            if (in_array($sortBy, $orderFields)) {
                $sql->orderBy($sortBy, $sortType);
            }
            $orders = $sql->select($fields)->paginate($pageSize);

            //  重新组装数据
            foreach ($orders as &$order) {
                if ($isOnlyGetInstallment) {
                    if (empty($order->installment)) {
                        unset($order);
                    }
                }
                //  友好化部分数据
                $user = $order->user;
                //  用户名
                $order->username = empty($user) ? '' : $user->name;
                //  用户协议
                $order->policy_name = Order::NEW_POLICY_TYPE == $order->policy_type ? '新协议' : '默认协议';
                $order->status      = $order->isExpire() ? 0 : 1;
                //  订单状态
                $order->order_status = $order->isExpire() ? '已过期' : '等待支付';
                //  价格
                $order->price_origin = convertAmount($order->total_price);

                $order->policy_type = Order::NEW_POLICY_TYPE == $order->policy_type ? 'v2' : 'v1';

                $order->is_drainage = $order->isDrainage();
                foreach ($order->good as $good) {
                    $good->goods_price = convertAmount($good->goods_price);
                    //                $order->goods[] = $good;
                    unset($good->form_data);
                }
                unset($order->user);
            }
            unset($order);
            return responseSuccess($orders);
        } catch (\Exception $e) {
            errorLog($e);
            return responseFailed($e->getMessage());
        }
    }
}
