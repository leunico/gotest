<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    const ARTICLE_STATUS_ON = 1;

    const ARTICLE_STATUS_OFF = 0;

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id')->select('id', 'driver_baseurl', 'filename', 'origin_filename');
    }

    public function scopeKeyword($query, $keyword)
    {
        if ($keyword) {
            return $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('articles.title', 'like', "%$keyword%")
                    ->orWhere('articles.keywords', 'like', "%$keyword%");
            });
        }
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->file)) {
            return asset('img/news/default_cover.png');
        } else {
            return $this->file->driver_baseurl . $this->file->filename;
        }
    }

    public function getNextOrPrevious($id)
    {
        if($id){
            $next = $this->where('id','>',$id)
                ->where('status',1)
                ->orderBy('id','ASC')
                ->first();
            $previous = $this->where('id','<',$id)
                ->where('status',1)
                ->orderBy('id','DESC')
                ->first();
            return array($next,$previous);
        }
        return [];
    }

}
