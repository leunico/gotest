<?php

namespace Modules\Operate\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Operate\Entities\Order;
use Modules\Operate\Http\Requests\CourseOrderRequest;
use Modules\Operate\Transformers\OrderResource;
use Modules\Operate\Http\Requests\StoreOrderPost;
use Modules\Course\Entities\Course;
// use EasyWeChat\Payment\Application;
use App\UserAddress;
use EasyWeChat\Factory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\User;
use Modules\Personal\Events\ChangeUser;

class OrderController extends Controller
{

    /**
     * 订单详情
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        $order->load('goods');

        return $this->response()->item($order, OrderResource::class);
    }

    /**
     * 生成课程订单
     *
     * @param StoreOrderPost                  $request
     * @param \Modules\Operate\Entities\Order $order
     * @param string                          $type
     * @return \Illuminate\Http\JsonResponse
     * @author lizx
     */
    public function storeCourseWechat(StoreOrderPost $request, Order $order, string $type): JsonResponse
    {
        $payment = Factory::payment(config('wechat.payment')[$type]);
        $user = $request->user();
        $orderCategory = Order::$categoryMap[$type];
        try {
            DB::beginTransaction();

            $order = $order->existsGoods($request->course_ids);
            if (empty($order->prepay_id)) {
                $payCourses = Course::whereIn('id', $request->course_ids)
                    ->select('id', 'title', 'price', 'category', 'is_mail')
                    ->get();
                $totalPrice = $payCourses->sum('price');

                if (! empty($request->pay_type) || empty($user)) {
                    $user = $user ?? User::firstOrNew(['unionid' => $request->unionid]);
                    $user->phone = $request->phone;
                    $user->real_name = $request->real_name;
                    $user->grade = $request->grade;
                    $user->createPassword(substr($request->phone, -6));
                    if ($user->save()) {
                        event(new ChangeUser($user, 'create'));
                    }
                }

                if ($payCourses->contains('is_mail', Course::IS_MAIL_ON)) {
                    $address = $user->address ?? UserAddress::firstOrNew(['user_id' => $user->id]);
                    $address->receiver = $request->receiver ?? $address->receiver;
                    $address->province_id = $request->province_id ?? $address->province_id;
                    $address->city_id = $request->city_id ?? $address->city_id;
                    $address->district_id = $request->district_id ?? $address->district_id;
                    $address->detail = $request->address_detail ?? $address->address_detail;
                    $address->save();
                }

                $order->user_id = $user->id;
                $order->trade_no = Order::generateTradeNo();
                $order->payment_method = Order::PAYMENT_METHOD_WECHAT;
                $order->real_price = (int) ($payCourses->sum('price')); //分！
                $order->expired_at = Carbon::now()->addMinutes(30); // todo 订单有效期，记得放到config[env]
                $order->total_price = $payCourses->sum('price');
                $order->discount = $totalPrice - $order->real_price;
                $order->trade_type = 'JSAPI';
                $order->category = $orderCategory;
                $result = $payment->order->unify([
                    'body'         => str_limit('购买课程[' . $payCourses->pluck('title')->implode('，') . ']', 60, '...]'),
                    'out_trade_no' => $order->trade_no,
                    'total_fee'    => $order->real_price,
                    'trade_type'   => 'JSAPI',
                    'openid'       => $user->wechatUser->{"{$type}_openid"},
                    'notify_url'   => route('payment.notify', $type)
                ]);

                if (isset($result['return_code']) &&
                    $result['return_code'] === 'SUCCESS' &&
                    isset($result['result_code']) &&
                    $result['result_code'] === 'SUCCESS' &&
                    isset($result['prepay_id'])) {
                    $order->prepay_id = $result['prepay_id'];
                    if ($order->save()) {
                        $goodsData = [];
                        $payCourses->map(function ($item) use (&$goodsData, $request, $totalPrice, $order) {
                            $goodsData[] = [
                                'goods_type'    => 'course',
                                'goods_id'      => $item->id,
                                'goods_title'   => $item->title,
                                'goods_price'   => $item->price,
                                'payment_price' => $item->price * $order->real_price / $totalPrice,
                            ];
                        });
                        $order->goods()->createMany($goodsData);
                    } else {
                        DB::rollBack();
                        return $this->response()->errorServer(['Order Model Save Error.']);
                    }
                } else {
                    DB::rollBack();
                    return $this->response()->errorServer($result);
                }
            }

            $payJssdk = $payment->jssdk->bridgeConfig($order->prepay_id);
            DB::commit();
        } catch (\Exception $exception) {
            //dd($exception->getTraceAsString()); // todo 还有其它异常后面补上
            DB::rollBack();
            return $this->response()->error([$exception->getMessage()]);
        }

        return $this->response()->success([
            'pay_jssdk' => json_decode($payJssdk),
            'login_status' => $this->respondWithToken(auth('api')->login($user))
        ]);
    }
}
