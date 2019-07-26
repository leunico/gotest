<?php

namespace App;

use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id')
            ->select('id', 'name');
    }
}
