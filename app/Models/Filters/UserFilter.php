<?php
namespace App\Models\Filters;

use EloquentFilter\ModelFilter;

class UserFilter extends ModelFilter
{
    public function name($name)
    {
        return $this->where(function ($query) use ($name) {
            return $query->where('first_name', 'LIKE', '%' . $name . '%')
                ->orWhere('middle_name', 'LIKE', '%' . $name . '%')
                ->orWhere('last_name', 'LIKE', '%' . $name . '%');
        });
    }
}
