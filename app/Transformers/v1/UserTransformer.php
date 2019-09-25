<?php

namespace App\Transformers\v1;

use League\Fractal\TransformerAbstract;
use App\Models\v1\User;
use App\Transformers\v1\PermissionTransformer;
use App\Transformers\v1\RoleTransformer;

use Spatie\Permission\Models\Role;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
    */
    protected $availableIncludes = [
        'profile',
        'roles',
        'permissions'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'            => $user->id,
            'username'      => $user->username,
            'email'         => $user->email,
            'mobile_number' => $user->mobile_number,
            'first_name'    => $user->first_name,
            'middle_name'   => $user->middle_name,
            'last_name'     => $user->last_name,
            'status'        => $user->status,
            'can_login'     => $user->can_login
        ];
    }

    /**
     * Include Role
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeRoles(User $user)
    {
        $roles = $user->roles;
        return $roles ? $this->collection($roles, new RoleTransformer, 'role') : $this->null();
    }

    /**
     * Include Permission
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includePermissions(User $user)
    {
        $permissions = $user->permissions;
        return $permissions ? $this->collection($permissions, new PermissionTransformer, 'permission') : $this->null();
    }

    /**
     * Include Profile
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeProfile(User $user)
    {
        $profile = $user->profile;
        return $profile ? $this->item($profile, new ProfileTransformer, 'profile') : $this->null();
    }
}
