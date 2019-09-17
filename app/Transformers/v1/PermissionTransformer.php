<?php

namespace App\Transformers\v1;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Permission;

class PermissionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Permission $permission)
    {
        $data = [
            'id'            => $permission->id,
            'name'          => $permission->name,
            'guard_name'    => $permission->guard_name,
            'roles'         => $permission->getRoleNames(),
        ];
        return $data;
    }
}
