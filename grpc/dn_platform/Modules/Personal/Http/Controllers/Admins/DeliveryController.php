<?php

namespace Modules\Personal\Http\Controllers\Admins;

use function App\responseFailed;
use function App\responseSuccess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Personal\Entities\ExpressUser;
use Illuminate\Support\Facades\DB;
use Modules\Personal\Http\Requests\AddDeliveryRequest;
use Modules\Personal\Entities\Delivery;
use Modules\Personal\Entities\DeliveryLesson;
use function App\removeNullElement;
use EasyWeChat\Factory;
use Carbon\Carbon;
use Modules\Course\Entities\CourseLesson;
use Modules\Personal\Jobs\DeliveryMessageReminder;

class DeliveryController extends Controller
{

    //待寄件用户列表
    public function expressUsers(Request $request)
    {
        $expressUsers = ExpressUser::leftJoin('users', 'users.id', '=', 'express_users.user_id')
            ->leftJoin('courses', 'courses.id', '=', 'express_users.course_id')
            ->keyword($request->keyword)
            ->sendStatus($request->send_status)
            ->date($request->start_at, $request->ent_at)
            ->isAddress($request->is_address)
            ->course($request->course)
            ->select('express_users.*', 'users.name', 'users.phone', 'users.real_name', 'users.is_address as address_id', 'courses.title as course_title')
            ->orderBy('express_users.id', 'desc')
            ->paginate($request->per_page);

        foreach ($expressUsers as $vo) {
            $vo->is_remind = 0;
            if ($vo->remind_time && Carbon::now()->diffInHours($vo->remind_time) < 1) {
                $vo->is_remind = 1;
            }
        }

        return responseSuccess($expressUsers);
    }

    //添加寄件
    public function addDelivery(AddDeliveryRequest $request, ExpressUser $expressUser)
    {
        $expressUser->load([
            'deliveries.lessons',
            'course.lessons' => function ($query) {
                $query->where('status', CourseLesson::LESSON_STATUS_ON)->select('id', 'course_id', 'title');
            }
        ]);
        $deliveries_lessons = array_column($this->getLessons($expressUser->deliveries), 'id');
        $form_data = $request->only(['receiver', 'province_id', 'city_id', 'district_id', 'detail_address',
            'category', 'lessons_id', 'express_company', 'track_number', 'send_at', 'memo']);
        $form_data = removeNullElement($form_data);
        $form_data['express_user_id'] = $expressUser->id;
        $form_data['operator_id'] = Auth::id();

        if ($form_data['category'] == 2) {
            unset($form_data['lessons_id']);
            $data = Delivery::create($form_data);
        } else {
            $lessons = $form_data['lessons_id'];
            if (empty($lessons)) {
                return responseFailed('请选择主题');
            }
            $result = array_intersect($deliveries_lessons, $lessons);
            if (empty($result)) {
                unset($form_data['lessons_id']);
                $data = Delivery::create($form_data);
                $lesson_arr = [];
                foreach ($lessons as $vo) {
                    $lesson_arr[] = [
                        'delivery_id' => $data->id,
                        'lesson_id' => $vo,
                    ];
                }
                DB::table('delivery_lessons')->insert($lesson_arr);
            } else {
                return responseFailed('主题不能重复寄件');
            }
            $deliveries_lessons = array_merge($deliveries_lessons, $lessons);
            $send_status = Delivery::SEND_STATUS_FINISH;
            foreach ($expressUser->course->lessons as $vo) {
                if (!in_array($vo->id, $deliveries_lessons)) {
                    $send_status = Delivery::SEND_STATUS_PART;
                    break;
                }
            }
            $expressUser->send_status = $send_status;
            $expressUser->save();
        }
        return responseSuccess($data);
    }

    //寄件记录列表
    public function getDeliveriesList(ExpressUser $expressUser)
    {
        $expressUser->load([
            'user' => function ($u) {
                $u->select('id', 'name', 'phone', 'real_name')
                    ->with(['address' => function ($sql) {
                        $sql->select('id', 'user_id', 'province_id', 'city_id', 'district_id', 'detail', 'receiver')
                            ->with('province', 'city', 'district');
                    }]);
            },
            'course' => function ($c) {
                $c->select('id', 'title');
            },
            'deliveries.operator_user' => function ($query) {
                $query->select('id', 'name', 'real_name');
            },
            'deliveries.lessons.lesson' => function ($query) {
                $query->select('id', 'title');
            },
        ]);

        foreach ($expressUser->deliveries as &$vo) {
            foreach ($vo->lessons as $vo) {
                $vo->title = $vo->lesson->title;
                unset($vo->lesson);
            }
        }
        return responseSuccess($expressUser);
    }

    //获取寄件信息
    public function getDelivery(ExpressUser $expressUser)
    {
        $user = Auth::user();
        $expressUser->load([
            'user' => function ($u) {
                $u->select('id', 'name', 'phone', 'real_name', 'is_address')
                    ->with(['address' => function ($sql) {
                        $sql->select('id', 'user_id', 'province_id', 'city_id', 'district_id', 'detail', 'receiver')
                            ->with('province', 'city', 'district');
                    }]);
            },
            'order' => function ($query) {
                $query->select('id', 'good_remarks');
            },
            'course' => function ($query) {
                $query->select('id', 'title');
            },
            'course.lessons' => function ($query) {
                $query->select('id', 'course_id', 'title');
            },
            'userCourseLessons' => function ($query) use ($expressUser) {
                $query->select('id', 'user_id', 'course_id', 'course_lesson_id')
                    ->where('course_id', $expressUser->course_id);
            },
            'deliveries.operator_user' => function ($query) {
                $query->select('id', 'name', 'real_name');
            },
            'deliveries.lessons.lesson' => function ($query) {
                $query->select('id', 'title');
            },
        ]);

        if (! $expressUser->user->is_address) {
            unset($expressUser->user->address);
            $expressUser->user->address = '';
        }

        if (! $expressUser->order) {
            $lessons = $expressUser->course->lessons->keyBy('id');
            $expressUser->userCourseLessons->map(function ($item) use ($lessons) {
                $item->title = $lessons->get($item->course_lesson_id) ? $lessons->get($item->course_lesson_id)->title : '-';
            });
        }

        $expressUser->lessons = $this->getLessons($expressUser->deliveries);
        $expressUser->operator_user = $user->name;
        unset($expressUser->deliveries);
        return responseSuccess($expressUser);
    }

    private function getLessons($deliveries)
    {
        $data = [];
        foreach ($deliveries as $vo) {
            foreach ($vo->lessons as $value) {
                $data[] = $value->lesson;
            }
        }
        return $data;
    }

    //完善地址信息提醒
    public function deliveryMessageReminder(Request $request)
    {
        $form_data = $request->only(['type', 'id']);
        if ($form_data['type'] == 1 && $form_data['id']) {
            $express_user = ExpressUser::findOrFail($form_data['id']);
            $express_user->load([
                'user' => function ($query) {
                    $query->select('id', 'name', 'real_name', 'unionid', 'is_address');
                },
                'user.wechatUser',
                'course' => function ($query) {
                    $query->select('id', 'category');
                }
            ]);
            $diff = Carbon::now()->diffInSeconds($express_user->remind_time);
            if (!empty($express_user->remind_time) && $diff <= 3600) {
                return responseFailed('已经提醒过了，请一个小时后再提醒！');
            }
            if (empty($express_user->user->is_address) && !empty($express_user->user->wechatUser->art_openid)) {
                if ($express_user->course->category == 1) {
                    $course_category = 'art';
                    $openid = $express_user->user->wechatUser->art_openid;
                } else {
                    $openid = $express_user->user->wechatUser->music_openid;
                    $course_category = 'music';
                }
                $url = config('services.study_domain') . '/mobile_complete_info/' . $course_category;
                $data = [
                    'category' => $express_user->course->category,
                    'openid' => $openid,
                    'url' => $url,
                    'name' => $express_user->user->name
                ];
                $res = ExpressUser::perfectAddress($data);
                if ($res['errcode'] == 0) {
                    $express_user->remind_time = Carbon::now();
                    $express_user->save();
                } else {
                    return responseFailed($res['errmsg']);
                }
            } else {
                return responseFailed('用户没有绑定微信或者已经填写地址');
            }
        } elseif ($form_data['type'] == 2) {
            $this->dispatch(new DeliveryMessageReminder());
        } else {
            return responseFailed('参数错误');
        }
        return responseSuccess('提醒成功');
    }
}
