<?php

namespace Modules\Examination\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;
use App\Models\User;

class Match extends Model
{
    // protected $fillable = [];

    public function cover()
    {
        return $this->belongsTo(File::class, 'cover_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }
}
