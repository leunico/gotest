<?php

namespace Modules\Examination\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\File;
use Modules\ExaminationPaper\Entities\MajorProblem;
use Modules\Examinee\Entities\ExamineeAnswer;

class Examination extends Model
{
    use SoftDeletes;

    const STATUS_ACHIEVEMENT = 3;
    const STATUS_EXAMINATION = 2;
    const STATUS_PAPER = 1;
    const STATUS_OFF = 0;

    const STAFF_PAPERS = 1; // 考卷
    const STAFF_EXAMINEE = 2; // 考生
    const STAFF_MARKING = 4; // 阅卷
    const STAFF_ACHIEVEMENT = 8; // 成绩
    const STAFF_ANTI_CHEATING = 16; // 反作弊

    public static $staffs = [
        self::STAFF_PAPERS => '考卷管理',
        self::STAFF_EXAMINEE => '考生管理',
        self::STAFF_MARKING => '阅卷管理',
        self::STAFF_ACHIEVEMENT => '成绩管理',
        self::STAFF_ANTI_CHEATING => '反作弊管理',
    ];

    public function examFile()
    {
        return $this->belongsTo(File::class, 'exam_file_id');
    }

    public function match()
    {
        return $this->belongsTo(Match::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function statusUser()
    {
        return $this->belongsTo(User::class, 'release_user_id');
    }

    public function qualificationUser()
    {
        return $this->belongsTo(User::class, 'qualification_user_id');
    }

    public function category()
    {
        return $this->belongsTo(ExaminationCategory::class, 'examination_category_id');
    }

    public function examinationStaff()
    {
        return $this->belongsToMany(User::class, 'examination_users')
            ->withTimestamps();
    }

    public function examinationStaffPivot()
    {
        return $this->hasMany(ExaminationUser::class);
    }

    public function majorProblems()
    {
        return $this->hasMany(MajorProblem::class);
    }

    public function examineeAnswer()
    {
        return $this->hasMany(ExamineeAnswer::class);
    }

    public function examinationExaminees()
    {
        return $this->hasMany(ExaminationExaminee::class);
    }
}
