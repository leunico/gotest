<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;

class CollectLearnRecord extends Model
{
    const STATUS_ON = 1;

    protected $table = 'collect_learn_records';

    protected $guarded = [];

    public function learnProgresses()
    {
        return $this->hasMany(LearnProgress::class);
    }
}
