<?php

declare(strict_types=1);

namespace App;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Course\Entities\Course;
use Modules\Operate\Entities\Order;
use Modules\Personal\Entities\CourseUser;
use Modules\Personal\Entities\ExpressUser;
use Modules\Personal\Entities\LoginLog;
use Modules\Personal\Events\ChangeUser;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use Modules\Crm\Entities\Channel;
use Spatie\Permission\Traits\HasRoles;
use Modules\Personal\Entities\LearnRecord;
use Modules\Operate\Entities\WechatUser;
use Modules\Personal\Entities\Work;
use Modules\Course\Entities\MusicTheory;
use App\Traits\Relations\HasClasses;
use App\Traits\Relations\HasTeachers;
use App\Traits\Relations\HasOrders;
use App\Traits\Relations\HasModelUser;
use Modules\Operate\Entities\StarPackageUser;

use Modules\Course\Entities\Concerns\PivotTrait; //todo都放在这里太蠢了，怎么分离这些？
use Modules\Educational\Entities\TeacherOfficeTime;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Modules\Educational\Entities\BiuniqueAppointment;
use Modules\Personal\Entities\UserIntroduce;

/**
 * @property string name
 * @property string phone
 * @property string password
 * @property string real_name
 * @property int    age
 * @property int    grade
 * @property int    channel_id
 *
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable,
        PivotTrait,
        HasRoles,
        HasClasses,
        HasModelUser,
        HasOrders,
        HasTeachers;

    const IS_ADDRESS_ON = 1;
    const IS_ADDRESS_OFF = 0;

    protected $table = 'users';

    protected $guard_name = 'api'; // 使用权限守卫

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'real_name',
        'phone',
        'grade',
        'sex',
        'account_status',
        'unionid',
        'avatar',
        'creator_id',
        'is_address'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static $sexMap = [
        0 => '未知',
        1 => '男',
        2 => '女',
    ];

    /**
     * 监听事件
     */
    protected static function boot()
    {
        parent::boot();

        static::pivotDetaching(function ($model, $relationName, $pivotIds) {
            if ($relationName == 'teacherFormalCourses' || $relationName == 'teacherAuditionCourses') {
                if ($appointment = BiuniqueAppointment::leftjoin('teacher_office_times', 'teacher_office_time_id', 'teacher_office_times.id')
                    ->select('biunique_appointments.id', 'biunique_appointments.user_id', 'biunique_appointments.biunique_course_id')
                    ->whereIn('biunique_course_id', $pivotIds)
                    ->where('teacher_office_times.user_id', $model->id)
                    ->where('teacher_office_times.type', $relationName == 'teacherFormalCourses' ? TeacherOfficeTime::TYPE_ZS : TeacherOfficeTime::TYPE_ST)
                    ->where('teacher_office_times.status', BiuniqueAppointment::STATUS_NO)
                    ->first()) {
                    throw new UnprocessableEntityHttpException('老师权限课程ID为' . $appointment->biunique_course_id . '的已被约课，不可取消！');
                }
            }
        });
    }

    public static $gradeMap = [
        // '0' => '未知',
        '10' => '学前',
        '20' => '一年级',
        '30' => '二年级',
        '40' => '三年级',
        '50' => '四年级',
        '60' => '五年级',
        '70' => '六年级',
        '80' => '初一',
        '90' => '初二',
        '100' => '初三',
        '110' => '高一',
        '120' => '高二',
        '130' => '高三',
        '140' => '成人',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Create user password.
     *
     * @param string $password user password
     * @return self
     * @author lizx
     */
    public function createPassword(string $password): self
    {
        $this->password = Hash::make($password);

        return $this;
    }

    public function makePassword(string $password): self
    {
        $this->password = Hash::make($password);

        return $this;
    }

    /**
     * 验证用户密码
     *
     * @param string $password [description]
     * @return bool
     * @author lizx
     */
    public function verifyPassword(string $password): bool
    {
        return $this->password && app('hash')->check($password, $this->password);
    }

    /**
     * 是否超级管理员
     *
     * @return boolean
     * @author lizx
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class);
    }

    public function introduce()
    {
        return $this->hasOne(UserIntroduce::class)
            ->select('id', 'remark');
    }

    public function scopeName($query, $name)
    {
        if ($name) {
            return $query->where(function ($subQuery) use ($name) {
                $subQuery->where('name', 'like', "%$name%")
                    ->orWhere('phone', 'like', "%$name%");
            });
        }

        return $query;
    }

    // public function courseUsers()
    // {
    //     return $this->hasMany(CourseUser::class, 'user_id')
    //         ->select('user_id', 'course_id', 'order_id', 'memo', 'created_at', 'class_id')
    //         ->where('status', CourseUser::STATUS_NO);
    // }

    // public function starPackgeUsers()
    // {
    //     return $this->hasMany(StarPackageUser::class, 'user_id')
    //         ->select('user_id', 'star_package_id', 'order_id', 'memo', 'created_at', 'star')
    //         ->where('status', StarPackageUser::STATUS_NO);
    // }

    public function expressUsers()
    {
        return $this->hasMany(ExpressUser::class, 'user_id');
    }

    public function channel()
    {
        return $this->hasOne(Channel::class, 'id', 'channel_id');
    }

    public function learnRecords()
    {
        return $this->hasMany(LearnRecord::class, 'user_id', 'id');
    }

    public function works()
    {
        return $this->hasMany(Work::class, 'user_id', 'id');
    }

    public function musicTheories()
    {
        return $this->belongsToMany(MusicTheory::class, 'music_learn_progresses', 'user_id', 'music_id');
    }

    public function wechatUser()
    {
        return $this->hasOne(WechatUser::class, 'unionid', 'unionid');
    }

    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }

    /**
     * 根据用户手机，账号，姓名搜索
     *
     * @param        $query
     * @param string $keyword
     * @return mixed
     */
    public function scopeKeyword($query, string $keyword)
    {
        if (isMobile($keyword)) {
            return $query->where("{$this->table}.phone", $keyword);
        }

        return $query->where("{$this->table}.name", 'like', "{$keyword}%")
            ->orWhere("{$this->table}.real_name", 'like', "{$keyword}%");
    }

    // public function courses()
    // {
    //     return $this->belongsToMany(Course::class, 'course_users')->withTimestamps();
    // }

    /**
     * 获取头像
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar ?? ($this->wechatUser ? $this->wechatUser->headimgurl : config('services.default_headimgurl'));
    }

    public function hasFilledReceipt()
    {
        return $this->address and $this->address->province_id and $this->address->city_id and $this->address->district_id and $this->address->receiver;
    }

    /**
     * 生成不重复的用户名
     *
     * @param $name
     * @return string
     */
    public static function createName($name)
    {
        if (empty($name)) {
            $name = str_random(5);
        }
        $i = 0;
        $realName = $name;
        while (self::query()->where('name', $name)->first()) {
            ++$i;
            $name = $realName . $i;
        }

        return $name;
    }

    /**
     * 是否是付费用户
     *
     * @return boolean
     */
    public function userCategory()
    {
        return $this->orders()->where('is_paid', '=', 1);
    }

    public static function marketingCreate(Request $request)
    {
        if ($user = User::where('phone', $request->mobile)->first()) {
            return $user;
        }

        $user = new User;
        $user->name = self::getUniqueName($request->name);
        $user->phone = $request->mobile;
        $user->sex = $request->sex ?? 0;
        $user->grade = $request->grade ?? 0;
        $user->age = $request->age ?? 0;
        $user->real_name = $request->name ?? '';
        $user->password = bcrypt(substr($request->mobile, -6));
        $user->channel_id = getChannel();

        $unionid = WechatUser::getUnionidFromSession($request->oauth_category);

        if (!empty($unionid) and empty(User::where('unionid', $unionid)->first())) {
            $user->unionid = $unionid;
        }
        $user->save();

        event(new ChangeUser($user));

        return $user;
    }

    public static function getUniqueName($name)
    {
        if (empty($name)) {
            return null;
        }

        $i = 0;
        $tmpName = $name;
        while (User::whereName($name)->first()) {
            $i ++;
            $name = $tmpName . $i;
        }

        return $name;
    }
}
