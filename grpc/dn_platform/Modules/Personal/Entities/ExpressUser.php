<?php

namespace Modules\Personal\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Course\Entities\Course;
use Modules\Operate\Entities\Order;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use EasyWeChat\Factory;

class ExpressUser extends Model
{
    protected $guarded = ['id'];

    const SEND_STATUS_WAIT = 1;

    public static $waitStatusMap = [
        1 => '待寄件',
        2 => '部分寄件',
        3 => '寄件完成',
    ];

    public static function assign(Order $order)
    {
        $user = $order->user;

        if (!$order->isPaid()) {
            Log::warning('尝试给未支付的订单分配待寄件记录：' . $order->trade_no);

            return false;
        }

        $order->goods->load('course');

        $expressUsers = $user->expressUsers;

        foreach ($order->goods as $good) {
            $course = $good->course;
            //如果没有找到课程 或者 课程不需要寄件 跳过
            if (empty($course) or empty($course->is_mail)) {
                continue;
            }

            $expressUser = $expressUsers->first(function ($item) use ($course) {
                return $item->course_id == $course->id;
            });

            if (empty($expressUser)) {
                $expressUser = new ExpressUser();
            }

            $expressUser->user_id = $user->id;
            $expressUser->course_id = $course->id;
            $expressUser->order_id = $order->id;
            $expressUser->send_status = self::SEND_STATUS_WAIT;

            $expressUser->save();
        }

        return true;
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function userCourseLessons()
    {
        return $this->hasMany(UserCourseLesson::class, 'user_id', 'user_id');
    }

    /**
     * 根据用户手机，账号，姓名搜索
     *
     * @param        $query
     * @param string $keyword
     * @return mixed
     */
    public function scopeKeyword($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('users.name', 'like', "%$keyword%")
                    ->orWhere('users.phone', 'like', "%$keyword%")
                    ->orWhere('users.real_name', 'like', "%$keyword%");
            });
        }
    }

    public function scopeSendStatus($query, $send_status)
    {
        if ($send_status) {
            return $query->where('express_users.send_status', $send_status);
        }
    }

    public function scopeDate($query, $start_at, $ent_at)
    {
        if ($start_at && $ent_at) {
            return $query->whereBetween('express_users.created_at', [Carbon::parse($start_at), Carbon::parse($ent_at)->addHours(24)]);
        }
    }

    public function scopeIsAddress($query, $is_address)
    {
        if ($is_address == 1) {
            return $query->where('users.is_address', 1);
        } elseif ($is_address == 2) {
            return $query->where('users.is_address', 0);
        }
    }

    public function scopeCourse($query, $course)
    {
        if ($course) {
            return $query->where('courses.id', $course);
        }
    }

    //完善地址
    public static function perfectAddress($data)
    {
        if ($data['category'] == 1) {
            $app = Factory::officialAccount(config('wechat.official_account')['art']);
            $template = config('wechat.template')['art']['perfect_address'];
        } else {
            $app = Factory::officialAccount(config('wechat.official_account')['music']);
            $template = config('wechat.template')['music']['perfect_address'];
        }
        $res = $app->template_message->send([
            'touser' => $data['openid'],
            'template_id' => $template['id'],
            'url' => $data['url'],
            'data' => [
                'first' => '您成功购买课程，请完善您的收件信息，以便我们寄送上课材料包',
                'keyword1' => $data['name'],
                'keyword2' => Carbon::now()->addHours(24),
                'remark' => '【点击此处】填写收件信息',
            ],
        ]);
        return $res;
    }

}
