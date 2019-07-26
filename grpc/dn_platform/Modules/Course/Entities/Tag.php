<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use App\File;

class Tag extends Model
{
    protected $fillable = ['sort'];

    const CATEGORY_MUSIC_PRACTICE = 1;

    const TYPE_MUSIC_PRACTICE = 'music_practices';

    const CATEGORYS = [1];

    public function models()
    {
        return $this->hasMany(ModelHasTag::class);
    }

    public function cover()
    {
        return $this->belongsTo(File::class, 'cover_id')
            ->select('driver_baseurl', 'origin_filename', 'id', 'filename');
    }
}
