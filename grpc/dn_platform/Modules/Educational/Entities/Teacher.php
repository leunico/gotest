<?php

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;
use App\File;
use function App\toDecbin;

class Teacher extends Model
{
    const AUTHORUTY_SL = 1;
    const AUTHORUTY_SCYE = 2;
    const AUTHORUTY_YYYJ = 4;
    const AUTHORUTY_YHYL = 8;
    const AUDITION_TEACHER = 'audition_teacher';
    const COURSE_TEACHER = 'course_teacher';
    const TEACHER_TYPE_JZ = 1;
    const TEACHER_TYPE_QZ = 2;

    protected $fillable = [];

    public static $authoritys = [
        0 => '-',
        self::AUTHORUTY_SL => '乐理',
        self::AUTHORUTY_SCYE => '视唱乐耳',
        self::AUTHORUTY_YYYJ => '央音音基考试',
        self::AUTHORUTY_YHYL => '英皇乐理考级',
    ];

    public static $teachers = [
        self::AUDITION_TEACHER => '约课老师',
        self::COURSE_TEACHER => '教务运营',
    ];

    public static $typeMap = [
        self::TEACHER_TYPE_JZ => '兼职',
        self::TEACHER_TYPE_QZ => '全职',
    ];

    public function qrcodeFile()
    {
        return $this->belongsTo(File::class, 'qrcode')
            ->select('driver_baseurl', 'origin_filename', 'id', 'filename');
    }

    // /**
    //  * 设定老师authority
    //  *
    //  * @param  array $value
    //  * @return void
    //  */
    // public function setAuthorityAttribute(array $value)
    // {
    //     $this->attributes['authority'] = array_sum($value);
    // }

    // /**
    //  * 获取老师authority
    //  *
    //  * @param  int $value
    //  * @return array
    //  */
    // public function getAuthorityAttribute($value)
    // {
    //     return toDecbin($value);
    // }
}
