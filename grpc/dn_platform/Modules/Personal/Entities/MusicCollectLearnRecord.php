<?php
/**
 * Created by PhpStorm.
 * User: MRW
 * Date: 2018/11/10
 * Time: 10:51
 */

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;

class MusicCollectLearnRecord extends Model
{
    protected $guarded = [];

    const STATUS_ON = 1;
}
