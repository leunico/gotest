<?php

namespace Modules\Operate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\OperationLogEnable;
use App\File;

class ArticlePromote extends Model
{
    use SoftDeletes, OperationLogEnable;

    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    protected $fillable = [
        'status'
    ];

    /**
     * 获取真实状态
     *
     * @param integer|null $article_id
     * @return void
     */
    public function getRealStatus(?int $article_id = null)
    {
        $articleOn = self::select('id', 'article_id', 'status')
            ->where('article_id', $article_id ?? $this->article_id)
            ->where('status', self::STATUS_ON)
            ->first();

        return $articleOn ? self::STATUS_OFF : self::STATUS_ON;
    }

    public function image()
    {
        return $this->belongsTo(File::class, 'image_id')
            ->select('driver_baseurl', 'origin_filename', 'id', 'filename');
    }
}
