<?php

declare(strict_types=1);

namespace Modules\Personal\Http\Repositories;

use App\User;
use App\Http\Repositories\BaseRepository;
use Carbon\Carbon;
use Modules\Course\Entities\Course;
use Modules\Operate\Entities\Order;
use Modules\Personal\Entities\CourseUser;
use Modules\Course\Entities\BigCourse;

class UserManageRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = $this->model()
            ->leftJoin('channels', 'channels.id', '=', 'users.channel_id')
            ->leftJoin('orders', 'orders.user_id', '=', 'users.id')
            ->leftJoin('course_users', 'course_users.user_id', '=', 'users.id')
            ->leftJoin('courses', 'courses.id', '=', 'course_users.course_id')
            ->whereNull('orders.deleted_at')
            ->whereNull('channels.deleted_at')
            ->whereNull('course_users.deleted_at')
            ->whereNull('courses.deleted_at');
    }

    /**
     * @return \App\User
     */
    public function model()
    {
        return new User();
    }

    /**
     * 年级
     *
     * @param integer|null $startGrade
     * @param integer|null $endGrade
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function grade(?int $startGrade, ?int $endGrade): UserManageRepository
    {
        if ($startGrade !== null) {
            $this->model->where('users.grade', '>=', $startGrade);
        }

        if ($endGrade !== null) {
            $this->model->where('users.grade', '<=', $endGrade);
        }

        return $this;
    }

    /**
     * 性别
     *
     * @param integer|null $sex
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function sex(?int $sex): UserManageRepository
    {
        if ($sex !== null) {
            $this->model->where('users.sex', '=', $sex);
        }

        return $this;
    }

    /**
     * 课程分类
     *
     * @param string|null $category
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function courseCategory(?string $category): UserManageRepository
    {
        if ($category !== null) {
            $categories = explode(';', $category);

            if (count($categories) === 1) {
                $this->model->where('courses.category', '=', current($categories))->where('courses.status', '=', 1);
            } else {
                // 找出购买了两门课程的所有用户

                $courseKeys = array_keys(Course::$courseMap);

                $users = Course::leftJoin('course_users', 'course_users.course_id', '=', 'courses.id')
                    ->where('course_users.status', 1)
                    ->where('courses.status', 1)
                    ->whereNull('courses.deleted_at')
                    ->whereNull('course_users.deleted_at')
                    ->get(['course_users.user_id', 'courses.category', 'course_users.id'])
                    ->groupBy('user_id')
                    ->filter(function ($value, $key) use ($courseKeys) {
                        return $value->pluck('category')->unique()->values()->sort()->values()->all() === $courseKeys;
                    })
                    ->all();

                $this->model->whereIn('users.id', collect(array_keys($users)));
            }
        }

        return $this;
    }

    /**
     *  用户分类，是否是付费用户
     *
     * @param integer|null $catgory
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function category(?int $catgory): UserManageRepository
    {
        if ($catgory !== null) {
            // 订单支付则是付费用户，否则是非付费用户
            if ($catgory === Order::PAID) {
                $this->model->where('orders.is_paid', '=', Order::PAID);
            } else {
                $this->model->whereDoesntHave('orders', function ($query) {
                    $query->where('orders.is_paid', '=', Order::PAID);
                });
            }
        }

        return $this;
    }

    /**
     * 渠道来源
     *
     * @param integer|null $channel
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function channel(?int $channel): UserManageRepository
    {
        if ($channel !== null) {
            $this->model->where('users.channel_id', '=', $channel);
        }

        return $this;
    }

    /**
     * 是否购买指定课程
     *
     * @param integer|null $course_id
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function course($course_id): UserManageRepository
    {
        if (! is_null($course_id)) {
            $this->model->whereIn('users.id', CourseUser::select('id', 'course_id', 'user_id')->where('course_id', $course_id)->get()->pluck('user_id'));
        }

        return $this;
    }

    /**
     * 是否购买指定大课程
     *
     * @param integer|null $big_course_id
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function bigCourse($big_course_id): UserManageRepository
    {
        if (! is_null($big_course_id)) {
            $bigCourse = BigCourse::where('id', $big_course_id)
                ->with([
                    'courses' => function ($query) {
                        return $query->select('courses.id', 'courses.title');
                    }
                ])
                ->first();

            if ($bigCourse->courses->isNotEmpty()) {
                $this->model->whereIn('users.id', CourseUser::select('id', 'course_id', 'user_id')->whereIn('course_id', $bigCourse->courses->pluck('id'))->get()->pluck('user_id'));
            }
        }

        return $this;
    }

    /**
     * 入驻时间
     *
     * @param string|null $startDate 2018-01-01
     * @param string|null $endDate
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function date(?string $startDate, ?string $endDate): UserManageRepository
    {
        if ($startDate !== null) {
            $this->model->where('users.created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate !== null) {
            $this->model->where('users.created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $this;
    }

    /**
     * 关键词搜索
     *
     * @param string|null $keyword
     * @return \Modules\Personal\Http\Controllers\Apis\UserManageRepository
     */
    public function keyword(?string $keyword): UserManageRepository
    {
        if ($keyword !== null) {
            $this->model->keyword($keyword);
        }

        return $this;
    }
}
