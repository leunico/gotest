<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Course\Entities\CourseSection;
use App\User;

class LearnRecord extends Model
{
    protected $table = 'learn_records';

    protected $guarded = [];

    protected $dates = ['entry_at', 'leave_at'];

    public $timestamps = false;

    public function courseSection()
    {
        return $this->hasOne(CourseSection::class, 'id', 'section_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
