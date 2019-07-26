<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Operate\Entities\WechatUser;
// use EasyWeChat\OfficialAccount\Application; // todo 多个公众号不能用依赖注入【需要设置default公众号】
use App\User;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;
use Modules\Operate\Entities\Order;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Modules\Operate\Entities\PaymentData;

class WechatController extends Controller
{
    /**
     * @var array
     */
    private $officialConfigs;

    /**
     * @var array
     */
    private $paymentConfigs;

    public function __construct()
    {
        $this->officialConfigs = config('wechat.official_account');
        $this->paymentConfigs = config('wechat.payment');
    }

    /**
     * Jssdk
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @return void
     */
    public function jssdk(Request $request, string $type)
    {
        $this->validate($request, ['jssdk_url' => 'url']);

        $official = Factory::officialAccount($this->officialConfigs[$type]);
        if ($request->jssdk_url) {
            $official->jssdk->setUrl($request->jssdk_url);
        }

        return $this->response()->success(json_decode($official->jssdk->buildConfig([])));
    }

    /**
     * 网页授权
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @return void
     */
    public function web(Request $request, string $type)
    {
        $this->validate($request, ['redirect_url' => 'url']);

        $official = Factory::officialAccount($this->officialConfigs[$type]);
        if ($request->redirect_url) {
            $official->oauth->setRedirectUrl($request->redirect_url);
        }

        return $official->oauth->redirect();
    }

    /**
     * 网页授权的回调
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @author lizx
     */
    public function webNotify(Request $request, string $type): JsonResponse
    {
        $official = Factory::officialAccount($this->officialConfigs[$type]);

        try {
            // 获取 OAuth 授权结果用户信息
            $user = $official->oauth->user();
            $wechatUserInfo = $user->getOriginal();

            if (! isset($wechatUserInfo['unionid']) || empty($wechatUserInfo['unionid'])) {
                return $this->response()->error('No UnionId Error.');
            }

            $wechatUser = WechatUser::firstOrNew(['unionid' => $wechatUserInfo['unionid']]);
            if (($user = User::where('unionid', $wechatUserInfo['unionid'])->first()) && $wechatUser) {
                if (empty($wechatUser->{"{$type}_openid"})) {
                    $wechatUser->{"{$type}_openid"} = $wechatUserInfo['openid'];
                    $wechatUser->save();
                }

                if (empty($user->account_status)) {
                    return $this->response()->errorForbidden('你在小黑屋哦');
                }

                return $this->respondWithToken(auth('api')->login($user));
            } else {
                $wechatUser->nickname = $wechatUserInfo['nickname'];
                $wechatUser->sex = $wechatUserInfo['sex'];
                $wechatUser->language = $wechatUserInfo['language'];
                $wechatUser->city = $wechatUserInfo['city'];
                $wechatUser->province = $wechatUserInfo['province'];
                $wechatUser->country = $wechatUserInfo['country'];
                $wechatUser->headimgurl = $wechatUserInfo['headimgurl'];
                $wechatUser->{"{$type}_openid"} = $wechatUserInfo['openid'];
                $wechatUser->save();
            }

            return $this->response()->success(['unionId' => $wechatUserInfo['unionid']]);
        } catch (\Exception $exception) {
            return $this->response()->errorServer([$exception->getMessage()]);
        }
    }

    /**
     * 微信支付回调
     *
     * @param string $type
     * @return void
     */
    public function paymentNotify(string $type) // todo 对Operatem模块Order模型产生依赖，把Order放到外面？
    {
        $payment = Factory::payment($this->paymentConfigs[$type]);
        $response = $payment->handlePaidNotify(function ($message, $fail) use ($payment) {
            Log::notice("订单 {$message['out_trade_no']} 微信回调开始处理订单");
            Log::notice('订单回调参数：', $message);

            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::where('trade_no', $message['out_trade_no'])->first();

            if (! $order) {
                return 'Order not exist.';
            }

            if ($order->is_paid && $order->paid_at) {
                return true; // 告诉微信，我已经处理完了，已经支付成功了，别再通知我了
            }

            /////////////建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
            // $payment->order->queryByOutTradeNumber($message['out_trade_no']); // todo 查询订单。

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    Log::notice("订单ID {$order->trade_no} 微信支付成功");
                    $order->paid_at = Carbon::createFromFormat('YmdHis', $message['time_end']); // 更新支付时间
                    $order->is_paid = Order::PAID;
                    $order->tx_num = $message['transaction_id'];
                    $order->mch_id = $message['mch_id'];

                    if (! $order->handelNotify()) {
                        Log::notice("订单ID {$order->trade_no} 处理失败，请检查！");
                        return false; // todo 返回false有什么用呢？
                    }
                } elseif (array_get($message, 'result_code') === 'FAIL') {  // 用户支付失败
                    Log::notice("订单ID {$order->trade_no} 微信支付失败");
                    $order->is_paid = Order::UNPAID;
                    $order->save(); // 保存订单
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            PaymentData::firstOrCreate(['tx_num' => $message['transaction_id']], ['payment_method' => 1, 'tx_data' => $message]); //记录请求信息
            return true; // 返回处理完成
        });

        return $response;
    }
}
