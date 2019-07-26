<?php


namespace App\Platforms;


use App\PlatformCRMs\CustomerUser;

class User extends Model
{
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'name', 'email', 'password', 'avatar', 'point', 'emailVerified', 'real_name', 'birthday', 'QQ',
            'school', 'email_code', 'expired_time', 'mobile', 'role_id', 'sex', 'province', 'city', 'district', 'about', 'data',
            'small_class_count', 'activity_id', 'wechat', 'age', 'share_code', 'grade', 'from',
        ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function crm_owners()
    {
        return $this->hasOne(CustomerUser::class, 'wwwuser_id', 'id')
            ->leftJoin('customers', 'customers.id', '=', 'customer_users.customer_id')
            ->leftJoin('users', 'users.id', '=', 'customers.owner_id')
            ->select('customer_users.wwwuser_id', 'users.name as sale');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_users');
    }

    public function boughtSystemCourses()
    {
        return $this->courses->filter(function ($item) {
            //判断该用户是否购买编玩必修课、启蒙课或冬令营
            return in_array($item->is_required, [1, 4, 5]);
        })->count() > 0;
    }
}