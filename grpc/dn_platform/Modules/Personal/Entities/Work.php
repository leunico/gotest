<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Course\Entities\CourseLesson;
use App\User;

class Work extends Model
{
    use SoftDeletes;

    const SHARE_STATUS_ON = 1;

    const WORK_STATUS_ON = 1;

    const WORK_STATUS_OFF = 0;

    protected $table = 'works';

    protected $guarded = [];

    public function lesson()
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getJsonUrlAttribute()
    {
        return !empty($this->file_url) ? config('filesystems.disks.cosv5.cdn') . '/' . $this->file_url : '';
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image_cover)) {
            return config('filesystems.disks.cosv5.cdn') . '/' . $this->image_cover;
        } else {
            $mod = ($this->id) % 6;
            $name = ($mod + 1) . ".png";
            return asset('img/work/' . $name);
        }
    }

    public function scopeTitle($query, $name)
    {
        if (isset($name) && $name != '') {
            return $query->where('works.title', 'like', '%' . $name . '%');
        }

        return $query;
    }

    public function scopeLesson($query, $lesson_id)
    {
        if ($lesson_id) {
            return $query->where('works.lesson_id', $lesson_id);
        }

        return $query;
    }

    public function scopeType($query, $type)
    {
        if (!empty($type)) {
            return $query->where('works.type', $type);
        }

    }

}
