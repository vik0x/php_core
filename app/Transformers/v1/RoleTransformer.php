<?php

namespace App\Transformers\v1;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RoleTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
    */
    protected $availableIncludes = [
        'permissions'
    ];
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
            'guard_name'    => $role->guard_name
        ];
    }

    /**
     * Include Permission
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includePermissions(Role $role)
    {
        $permissions = $role->getAllPermissions();
        return $permissions ? $this->collection($permissions, new PermissionTransformer, 'permission') : $this->null();
    }
}
