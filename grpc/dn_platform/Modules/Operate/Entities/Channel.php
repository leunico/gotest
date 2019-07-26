<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [];

    public function level3()
    {
        return $this->belongsTo(Channel::class, 'level3_id');
    }

    public function level2()
    {
        return $this->belongsTo(Channel::class, 'level2_id');
    }

    public function level1()
    {
        return $this->belongsTo(Channel::class, 'level1_id');
    }
}
