<?php

namespace Modules\Operate\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Operate\Entities\Order;
use App\User;
use Illuminate\Support\Carbon;
use Modules\Course\Entities\Course;
use Illuminate\Http\Request;

class OrderExport implements FromCollection
{
    /**
     * \Illuminate\Http\Request
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $isPaid = $this->request->input('is_paid', null);
        $tradeNo = $this->request->input('trade_no', null);
        $name = $this->request->input('name', null);
        $category = $this->request->input('category', null);
        $paymentMethod = $this->request->input('payment_method', null);
        $startTime = $this->request->input('start_time', 0);
        $endTime = $this->request->input('end_time', 0);

        $values = [['订单用户名', '用户真实姓名', '用户手机号码', '系统订单号', '商户订单号', '支付时间', '应付金额', '实付金额', '商户类型', '订单商品类型', '支付方式', '创建人', '创建人真实姓名', '创建人手机号', '有赞订单号']];
        $orders = Order::when(! is_null($isPaid) && is_numeric($isPaid), function ($query) use ($isPaid) {
            $query->status($isPaid);
        })
            ->when($tradeNo, function ($query) use ($tradeNo) {
                $query->where('trade_no', $tradeNo);
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($name, function ($query) use ($name) {
                $userIds = User::name($name)->pluck('id');
                $query->whereIn('user_id', $userIds);
            })
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            ->when((! empty($startTime) || ! empty($endTime)), function ($query) use ($startTime, $endTime) {
                $query->whereBetween('created_at', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfDay()]);
            })
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'real_name', 'phone');
                },
                'creator' => function ($query) {
                    $query->select('id', 'name', 'real_name', 'phone');
                },
                'goods'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) use (&$values) {
                $val[0] = $item->user ? $item->user->name : '用户已删除';
                $val[1] = $item->user ? $item->user->real_name : '用户已删除';
                $val[2] = $item->user ? $item->user->phone : '';
                $val[3] = $item->trade_no . ' ';
                $val[4] = $item->tx_num;
                $val[5] = $item->paid_at;
                $val[6] = (float) ($item->total_price / 100);
                $val[7] = (float)($item->real_price / 100);
                $val[8] = isset(Course::$courseMap[$item->category]) ? Course::$courseMap[$item->category] : '星星包';
                $val[9] = $item->goods->first()->goods_type;
                $val[10] = isset(Order::$paymentMethodMap[$item->payment_method]) ? Order::$paymentMethodMap[$item->payment_method] : '-';
                $val[11] = $item->creator ? $item->creator->name : '-';
                $val[12] = $item->creator ? $item->creator->real_name : '-';
                $val[13] = $item->creator ? $item->creator->phone : '-';
                $val[14] = $item->youzan_trade_no;
                $values[] = $val;
            });

        return collect($values);
    }
}
