<?php

namespace Modules\Educational\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TeacherCourse extends Model
{
    const TYPE_ZS = 1;
    const TYPE_ST = 2;

    public static $typeMap = [
        self::TYPE_ZS => '正式课',
        self::TYPE_ST => '试听课'
    ];

    protected $fillable = [
        'biunique_course_id',
        'type'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
