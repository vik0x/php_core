<?php

namespace App\Transformers\v1;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RoleTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id'            => $role->id,
            'name'          => $role->name,
            'guard_name'    => $role->guard_name,
            'permissions'   => $role->permissions
        ];
    }
}
