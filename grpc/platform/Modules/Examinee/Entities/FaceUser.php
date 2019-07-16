<?php

namespace Modules\Examinee\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;

class FaceUser extends Model
{
    const STATUS_NORMAL = 0;

    protected $guarded = [];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
