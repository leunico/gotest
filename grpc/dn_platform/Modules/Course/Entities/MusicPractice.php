<?php

namespace Modules\Course\Entities;

use Illuminate\Database\Eloquent\Model;
use App\File;
use Illuminate\Database\Eloquent\SoftDeletes;

class MusicPractice extends Model
{
    use SoftDeletes;

    const STATUS_ON = 1;

    protected $fillable = ['sort'];

    public function book()
    {
        return $this->belongsTo(File::class, 'book_id')
            ->select('driver_baseurl', 'origin_filename', 'id', 'filename');
    }

    public function tagPivots()
    {
        return $this->hasMany(ModelHasTag::class, 'model_id');
    }

    public function tags()
    {
        // return $this->belongsToMany(Tag::class, 'model_has_tags', 'model_id')
        //     ->select('tags.name', 'tags.sort')
        //     ->wherePivot('model_type', Tag::TYPE_MUSIC_PRACTICE);

        return $this->morphToMany(Tag::class, 'model', 'model_has_tags', 'model_id')->select('tags.id', 'tags.name', 'tags.sort');  //多对多多态关联
    }

    public function actionStatus()
    {
        return empty($this->status) ? $this->increment('status') : $this->decrement('status');
    }
}
