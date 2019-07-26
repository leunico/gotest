<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\File;

class BigCourse extends Model
{
    use SoftDeletes;

    const STATUS_ON = 1;

    const CATEGORY_ART_PROGRAME = 1;

    const CATEGORY_MUSIC_THEORY = 2;

    protected $fillable = [
        'id',
        'title',
        'sort',
    ];

    public static $courseMap = [
        1 => '艺术编程',
        2 => '数字音乐',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'big_course_course_pivot')
            ->where('courses.status', Course::STATUS_NO) // todo 下架不显示
            ->withTimestamps();
    }

    public function cover()
    {
        return $this->belongsTo(File::class, 'cover_id')
            ->select('driver_baseurl', 'origin_filename', 'id', 'filename');
    }

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }
}
