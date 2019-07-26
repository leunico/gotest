<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Operate\Entities\File;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id')->select('id', 'driver_baseurl', 'filename', 'origin_filename');
    }

    public function scopeType($query, $type)
    {
        if (!empty($type)) {
            return $query->where('banners.type', $type);
        }
    }

    public function scopeCategory($query, $category)
    {
        if (!empty($category)) {
            return $query->where('banners.category', $category);
        }
    }

    public function scopeBelongPage($query, $belong_page)
    {
        if (!empty($belong_page)) {
            return $query->where('banners.belong_page', $belong_page);
        }
    }

    public function scopePlatform($query, $platform)
    {
        if (!empty($platform)) {
            return $query->where('banners.platform', $platform);
        }
    }

}
