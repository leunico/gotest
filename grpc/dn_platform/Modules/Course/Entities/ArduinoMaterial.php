<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArduinoMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = ['sort'];

    public function setInfoAttribute($value)
    {
        $this->attributes['info'] = json_encode($value);
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }
}
