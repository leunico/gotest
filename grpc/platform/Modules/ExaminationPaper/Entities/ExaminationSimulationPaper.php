<?php

namespace Modules\ExaminationPaper\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Examination\Entities\ExaminationCategory;

class ExaminationSimulationPaper extends Model
{
    protected $fillable = ['examination_category_id'];
    
    /**
     * 设定content
     *
     * @param  array $value
     * @return void
     */
    public function setContentAttribute(array $value)
    {
        $this->attributes['content'] = json_encode($value);
    }

    /**
     * 获取content
     *
     * @param  int $value
     * @return array
     */
    public function getContentAttribute($value)
    {
        return json_decode($value, true);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function category()
    {
        return $this->belongsTo(ExaminationCategory::class, 'examination_category_id');
    }
}
