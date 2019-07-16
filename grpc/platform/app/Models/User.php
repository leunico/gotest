<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable,
        HasRoles;

    protected $guard_name = 'api';

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
        'sex',
        'account_status',
        'avatar',
        'creator_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * sex
     */
    public static $sexMap = [
        0 => '未知',
        1 => '男',
        2 => '女',
    ];

    /**
     * category
     */
    public static $categoryMap = [
        'Scratch',
        'Python',
        'C++',
        'Minecraft',
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
     * 获取用户的类型.
     *
     * @param  string $value
     * @return array
     */
    public function getCategoryAttribute(string $value)
    {
        return empty($value) ? [] : explode(',', $value);
    }

    /**
     * 设定用户的类型.
     *
     * @param  array $value
     * @return string
     */
    public function setCategoryAttribute(array $value)
    {
        $this->attributes['category'] = implode(',', $value);
    }

    /**
     * 获取头像
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar ?? config('services.default_headimgurl', '');
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
}
