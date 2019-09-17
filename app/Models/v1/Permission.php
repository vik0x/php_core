<?php

namespace App\Models\v1;

use Spatie\Permission\Models\Permission as Model;
use EloquentFilter\Filterable;

class Permission extends Model
{
    use Filterable;

    public function parentPermission()
    {
        return $this->hasOne('App\Models\v1\Permission', 'id', 'parent_id');
    }
}
