<?php

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use Illuminate\Support\Carbon;

class AuditionClass extends Model
{
    use SoftDeletes;

    const STATUS_OVER = 2;
    const STATUS_NO = 1;
    const STATUS_OFF = 0;

    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id')->select('id', 'name');
    }

    public function actionStatus()
    {
        return empty($this->getAttributes()['status']) ? $this->increment('status') : $this->decrement('status');
    }

    /**
     * 获取真实状态
     *
     * @param  int $value
     * @return integer
     */
    public function getStatusAttribute($value)
    {
        return (Carbon::now()->gte(Carbon::parse($this->entry_at)) && ! empty($value)) ? self::STATUS_OVER : $value;
    }
}
