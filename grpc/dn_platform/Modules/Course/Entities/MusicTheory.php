<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Personal\Entities\MusicLearnProgress;
use Modules\Personal\Entities\LearnRecord;

class MusicTheory extends Model
{
    use SoftDeletes;

    const STATUS_NO = 1;

    protected $fillable = [];

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_music_theory_pivot')
            ->withTimestamps();
    }

    public function musicLearnProgresses()
    {
        return $this->hasMany(MusicLearnProgress::class, 'music_id');
    }

    public function musicLearnRecords()
    {
        return $this->hasMany(LearnRecord::class, 'music_id');
    }
}
