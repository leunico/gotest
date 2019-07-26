<?php

namespace Modules\Personal\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Conversation extends Model
{
    use SoftDeletes;

    protected $table = 'conversation';

    protected $fillable = [
        'creator_id', 'user_id', 'conversation_at', 'content', 'type',
    ];

    protected $dates = ['conversation_at'];

    public static $conversationMap = [
        0 => '电话沟通，正常沟通',
        1 => '微信沟通',
        2 => '电话未接通',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function scopeUser($query, $userId)
    {
        return $query->where("{$this->table}.user_id", '=', $userId);
    }
}
