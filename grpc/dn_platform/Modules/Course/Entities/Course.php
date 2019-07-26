<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Personal\Entities\CourseUser;
use Modules\Personal\Entities\MusicCollectLearnRecord;
use App\File;
use Modules\Personal\Entities\CollectLearnRecord;
use Modules\Educational\Entities\StudyClass;

class Course extends Model
{
    use SoftDeletes;

    const STATUS_NO = 1;
    const IS_MAIL_ON = 1;
    const CATEGORY_ART_PROGRAME = 1;
    const CATEGORY_MUSIC_THEORY = 2;
    const TYPE_NW = 1;
    const TYPE_JR = 2;

    protected $fillable = [];

    public static $courseMap = [
        self::CATEGORY_ART_PROGRAME => '艺术编程',
        self::CATEGORY_MUSIC_THEORY => '数字音乐',
    ];

    public static $courseTypeMap = [
        self::TYPE_NW => '必修课',
        self::TYPE_JR => '节日课',
    ];

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }

    public function isNotDrainage()
    {
        return ! empty($this->is_drainage);
    }

    public function isTypeNw()
    {
        return self::TYPE_NW == $this->type;
    }

    public function scopeOfCategory($query, $category = self::CATEGORY_ART_PROGRAME)
    {
        return $query->where('category', $category);
    }

    public function bigCourses()
    {
        return $this->belongsToMany(BigCourse::class, 'big_course_course_pivot')
            ->withTimestamps();
    }

    public function cover()
    {
        return $this->belongsTo(File::class, 'cover_id')
            ->select('driver_baseurl', 'origin_filename', 'id', 'filename');
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class);
    }

    public function courseUsers()
    {
        return $this->hasMany(CourseUser::class);
    }

    public function courseUser()
    {
        return $this->hasOne(CourseUser::class);
    }

    public function studyClass()
    {
        return $this->hasMany(StudyClass::class);
    }

    public function musicTheories()
    {
        return $this->belongsToMany(MusicTheory::class, 'course_music_theory_pivot')
            ->withTimestamps();
    }

    public function arduinos()
    {
        return $this->belongsToMany(ArduinoMaterial::class, 'course_arduino_material_pivot')
            ->withTimestamps();
    }

    public function musicCollectLearnRecord()
    {
        return $this->hasMany(MusicCollectLearnRecord::class);
    }

    public function collectLearnRecords()
    {
        return $this->hasMany(CollectLearnRecord::class, 'course_id');
    }

    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('courses.category', $category);
        }

        return $query;
    }

    /**
     * 需要寄件
     *
     * @return boolean
     */
    public function needMail()
    {
        return (bool) $this->is_mail;
    }
}
