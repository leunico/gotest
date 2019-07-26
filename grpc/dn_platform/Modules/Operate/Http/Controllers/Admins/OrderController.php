<?php

namespace Modules\Operate\Http\Controllers\Admins;

use function App\errorLog;
use function App\responseFailed;
use function App\responseSuccess;
use function App\toCarbon;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Course\Entities\Course;
use Modules\Course\Entities\StarPackage;
use Modules\Operate\Entities\Order;
use Modules\Operate\Events\OrderChange;
use Modules\Operate\Http\Requests\CourseOrderRequest;
use Modules\Operate\Http\Requests\StarPackageOrderRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Carbon;
use function App\toCents;

class OrderController extends Controller
{
    /**
     * 订单列表
     *
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     */
    public function index(Request $request, Order $order)
    {
        $perPage = (int) $request->input('per_page', 15);
        $isPaid = $request->input('is_paid', null);
        $tradeNo = $request->input('trade_no', null);
        $youzanTradeNo = $request->input('youzan_trade_no', null);
        $name = $request->input('name', null);
        $category = $request->input('category', null);
        $paymentMethod = $request->input('payment_method', null);
        $startTime = $request->input('start_time', 0);
        $endTime = $request->input('end_time', 0);

        $orders = $order->when(! is_null($isPaid), function ($query) use ($isPaid) {
            $query->status($isPaid);
        })
            ->when($tradeNo, function ($query) use ($tradeNo) {
                $query->where('trade_no', $tradeNo);
            })
            ->when($youzanTradeNo, function ($query) use ($youzanTradeNo) {
                $query->where('youzan_trade_no', $youzanTradeNo);
            })
            ->when(! is_null($category), function ($query) use ($category) {
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
                $query->whereBetween('paid_at', [$startTime, (empty($endTime) ? Carbon::now() : Carbon::parse($endTime))->endOfDay()]);
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
            ->paginate($perPage);

        return responseSuccess($orders);
    }

    /**
     * 后台生成订单
     *
     * @param CourseOrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function store(CourseOrderRequest $request, Order $order)
    {
        // if ($totalPrice < $order->real_price) {
        //     throw new BadRequestHttpException('实际收款必须小于或等于应付金额');
        // }
        try {
            DB::beginTransaction();

            $order->user_id = $request->input('user_id');
            $order->trade_no = Order::generateTradeNo();
            $order->payment_method = $request->input('payment_method');
            $order->real_price = intval($request->input('real_price') * 1000 / 10);
            $order->memo = $request->input('memo', null);
            $order->youzan_trade_no = $request->input('youzan_trade_no', null);
            $order->tx_num = $request->tx_num;
            $order->paid_at = toCarbon($request->paid_at);
            $order->category = $request->category;
            $order->good_remarks = $request->input('good_remarks', []);
            $courses = Course::whereIn('id', $request->course_ids)->select('id', 'title', 'price')->get();
            $goodsData = [];
            $totalPrice = $courses->sum('price');
            foreach ($courses as $course) {
                $paymentPrice = $course->price * $order->real_price / $totalPrice;
                $goodsData[] = [
                    'goods_type' => Order::GOODS_TYPE_COURSE,
                    'goods_id' => $course->id,
                    'goods_title' => $course->title,
                    'goods_price' => $course->price,
                    'payment_price' => $paymentPrice,
                ];
            }

            $order->total_price = $totalPrice;
            $order->discount = $totalPrice - $order->real_price;
            $order->creator_id = Auth::user()->id;
            $order->is_paid = true;
            $order->save();
            $order->goods()->createMany($goodsData);
            DB::commit();
            event(new OrderChange($order));
            return responseSuccess($order);
        } catch (BadRequestHttpException $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            return responseFailed($exception->getMessage(), 400);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            errorLog($exception, __FUNCTION__, 'error');
            return responseFailed($exception->getMessage());
        }
    }

    public function storeStarPackage(StarPackageOrderRequest $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $order->user_id = $request->input('user_id');
            $order->trade_no = Order::generateTradeNo();
            $order->payment_method = $request->input('payment_method');
            $order->real_price = toCents($request->input('real_price'));
            $order->memo = $request->input('memo', null);
            $order->youzan_trade_no = $request->input('youzan_trade_no', null);
            $order->tx_num = $request->tx_num;
            $order->paid_at = toCarbon($request->paid_at);
            $order->category = Order::CATEGORY_STAR_PACKAGE; //$request->category;
            $starPackages = StarPackage::whereIn('id', $request->star_packages)->select('id', 'title', 'star', 'price')->get();
            $goodsData = [];
            $totalPrice = toCents($starPackages->sum('price'));
            if ($totalPrice < $order->real_price) {
                throw new BadRequestHttpException('实际收款必须小于或等于应付金额');
            }
            foreach ($starPackages as $starPackage) {
                $paymentPrice = toCents($starPackage->price) * $order->real_price / $totalPrice;
                $goodsData[] = [
                    'goods_type' => Order::GOODS_TYPE_STAR_PACKAGE,
                    'goods_id' => $starPackage->id,
                    'goods_title' => $starPackage->title,
                    'goods_price' => toCents($starPackage->price),
                    'payment_price' => $paymentPrice,
                    'star' => $starPackage->star
                ];
            }

            $order->total_price = $totalPrice;
            $order->discount = $totalPrice - $order->real_price;
            $order->creator_id = Auth::user()->id;
            $order->is_paid = true;
            $order->save();
            $order->goods()->createMany($goodsData);
            DB::commit();
            event(new OrderChange($order));
            return responseSuccess($order);
        } catch (BadRequestHttpException $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            return responseFailed($exception->getMessage(), 400);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getTraceAsString());
            errorLog($exception, __FUNCTION__, 'error');
            return responseFailed($exception->getMessage());
        }
    }

    /**
     * 后台查看订单详情
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function edit(Order $order)
    {
        $order->load('goods', 'user', 'creator');

        return responseSuccess($order);
    }

    /**
     * 后台财务确认订单
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function financeConfirm(Order $order)
    {
        $order->finance_confirm = empty($order->finance_confirm) ? Order::FINANCE_CONFIRM_ON : Order::FINANCE_CONFIRM_OFF;

        if ($order->save()) {
            return responseSuccess([
                'order_id' => $order->id
            ], '财务处理订单成功');
        } else {
            return responseFailed('财务订单处理失败', 500);
        }
    }

    /**
     * 删除订单
     *
     * @param Order $order
     * @throws \Exception
     * @return JsonResponse
     */
    public function destroy(Order $order)
    {
        if ($order->is_paid) {
            //@todo 如果可以删，那么对应的课程权限如何处理
            return responseFailed('删除失败，不能删除已支付的订单');
        }

        try {
            DB::beginTransaction();
            event(new OrderChange($order, 'delete'));
            $res = $order->delete();
            DB::commit();
            return responseSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            return responseFailed('删除失败，' . $exception->getMessage());
        }
    }
}
