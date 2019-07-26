<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/7
 * Time: 11:54
 */

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;

class ThumbsUp extends Model
{
    protected $table = 'thumbs_up';

    protected $guarded = [];
}