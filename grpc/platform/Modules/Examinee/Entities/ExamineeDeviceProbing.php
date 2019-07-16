<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Examination\Entities\ExaminationExaminee;

class ExamineeDeviceProbing extends Model
{
    // protected $fillable = [];

    public function eexaminee()
    {
        return $this->belongsTo(ExaminationExaminee::class, 'examination_examinee_id');
    }
}
