<?php

namespace Modules\Operate\Entities;

use App\Traits\OperationLogEnable;
use function App\arrGet;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Operate\Entities\Traits\OrderByCourse;

/**
 * @property mixed pay_method
 * @property mixed is_paid
 */
class Order extends Model
{
    use OperationLogEnable,
        OrderByCourse,
        SoftDeletes;

    // 支付状态常量
    const PAID = 1;
    const UNPAID = 0;

    // 订单状态常量
    const ORDER_UNKNOWN = 0;
    const ORDER_PAID = 1;
    const ORDER_UNPAID = 2;
    const ORDER_CLOSED = 3;
    const PAYMENT_METHOD_WECHAT = 1;
    const PAYMENT_METHOD_YOUZAN = 9;
    const GOODS_TYPE_COURSE = 'course';
    const GOODS_TYPE_STAR_PACKAGE = 'star_package';
    const CATEGORY_ART = 1;
    const CATEGORY_MUSIC = 2;
    const CATEGORY_STAR_PACKAGE = 3;
    const FINANCE_CONFIRM_OFF = 0;
    const FINANCE_CONFIRM_ON = 1;

    protected $fillable = [];

    protected $dates = ['expired_at', 'paid_at'];

    public static $paymentMethodMap = [
        1 => '微信支付',
        2 => '支付宝',
        3 => '转账至公账',
        4 => 'POS刷卡',
        5 => '现金',
        6 => '有赞'
    ];

    public static $payStatusMap = [
        self::ORDER_UNKNOWN => '未知',
        self::ORDER_PAID => '已支付',
        self::ORDER_UNPAID => '待付款',
        self::ORDER_CLOSED => '已关闭',
    ];

    // 未支付订单过期时间，半个钟
    protected $expiredSecond = 1800;

    public static $categoryMap = [
        'art' => 1,
        'music' => 2
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'good_remarks' => 'json'
    ];


    public function goods()
    {
        return $this->hasMany(OrderGood::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'trade_no';
    }

    public static function generateTradeNo()
    {
        return date('YmdHis') . substr(uniqid(), -5);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * @param null $default
     * @return mixed|null
     * @throws \Exception
     */
    public function getPaymentMethod($default = null)
    {
        return arrGet(self::$paymentMethodMap, $this->pay_method, $default);
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->is_paid == self::PAID;
    }

    /**
     * 根据商品返回真实订单
     *
     * @param array $goods
     * @param string $type
     * @param integer $payMentMethod
     * @return Order
     * @author lizx
     */
    public function existsGoods(array $goods, string $type = 'course', int $payMentMethod = 1): Order
    {
        $order = $this;
        if (! $user = request()->user()) {
            return $order;
        }

        self::where('user_id', $user->id)
            ->select('id', 'prepay_id', 'trade_no')
            ->where('payment_method', $payMentMethod)
            ->where('is_paid', self::UNPAID)
            ->where('expired_at', '>=', Carbon::now())
            ->with([
                'goods' => function ($query) use ($type) {
                    $query->select('order_id', 'goods_id', 'goods_type')
                        ->where('goods_type', $type);
                }
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->each(function ($item) use (&$order, $goods) {
                $goods_ids = $item->goods->pluck('goods_id');
                if ($goods_ids->diff($goods)->isEmpty() && empty(array_diff($goods, $goods_ids->toArray()))) {
                    $order = $item;
                    return false;
                }
            });

        return $order;
    }

    /**
     * 订单有效处理
     *
     * @return boolean
     * @author lizx
     */
    public function handelNotify(): bool
    {
        $goodsType = $this->goods->first()->goods_type;
        if (! $this->paid_at || empty($this->is_paid) || ! $this->tx_num || ! method_exists($this, $goodsType . 'Handle')) {
            return false;
        }

        return call_user_func([$this, $goodsType . 'Handle']);
    }

    /**
     * 获取订单状态
     *
     * @return integer
     */
    public function status(): int
    {
        if ($this->is_paid == self::PAID) {
            return self::ORDER_PAID;
        }

        // 未支付的订单需要判断expired_at
        if ($this->is_paid == self::UNPAID) {
            if ($this->expired_at->gt(Carbon::now())) {
                return self::ORDER_CLOSED;
            }

            return self::ORDER_UNPAID;
        }

        return self::ORDER_UNKNOWN;
    }

    /**
     * 判断一个订单是否课程订单
     * @return boolean
     */
    public function isCourseOrder()
    {
        return $this->goods->contains('goods_type', self::GOODS_TYPE_COURSE);
    }

    public function isStarPackageOrder()
    {
        return $this->goods->contains('goods_type', self::GOODS_TYPE_STAR_PACKAGE);
    }
    /**
     * 判断一个订单是否包含需要寄件的课程
     * @return boolean
     */
    public function containNeedMailCourse()
    {
        if ($this->isCourseOrder()) {
            $this->goods->load('course');

            return $this->goods->contains(function ($item) {
                return ! empty($item->course) and $item->course->needMail();
            });
        }

        return false;
    }

    public function isArt()
    {
        return $this->category == self::CATEGORY_ART;
    }

    public function isMusic()
    {
        return $this->category == self::CATEGORY_MUSIC;
    }

    public function goodsTitle()
    {
        return implode('、', $this->goods->pluck('goods_title')->toArray());
    }


    /**
     * 订单状态
     */
    public function scopeStatus($query, ?int $status)
    {
        if ($status !== null) {
            if ($status === Order::ORDER_PAID) {
                $query->where('is_paid', '=', Order::PAID);
            } elseif ($status === Order::ORDER_UNPAID) {
                $query->where('is_paid', '=', Order::UNPAID)
                    ->where('expired_at', '<', Carbon::now());
            } elseif ($status === Order::ORDER_CLOSED) {
                $query->where('is_paid', '=', Order::UNPAID)
                    ->where('expired_at', '>=', Carbon::now());
            }
        }

        return $query;
    }
}
