<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use App\File;

class BiuniqueCourseResource extends Model
{
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    protected $fillable = [];

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id')
            ->select('id', 'driver_baseurl', 'origin_filename', 'filename', 'mime');
    }
}
