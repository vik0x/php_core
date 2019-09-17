<?php

namespace App\Rules\v1;

use Illuminate\Contracts\Validation\Rule;
use App\Models\v1\Permission;
use App\Models\v1\User;

class CheckPermissions implements Rule
{
    private $user;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $response = true;
        foreach ($value as $permissionId) {
            $permission = Permission::find($permissionId);

            if (!$permission || !$this->canAddPermission($permission)) {
                $response = false;
            }
        }
        return $response;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.invalid_permission');
    }

    private function canAddPermission(Permission $permission)
    {
        $user = $this->user;
        if ($user->hasRole(config('settings.user.rootRole'))) {
            return true;
        }
        $parentPermission = $permission->parentPermission;
        if (!$parentPermission) {
            return $user->hasPermissionTo($permission);
        }
        return $user->hasPermissionTo($permission) && $user->hasPermissionTo($parentPermission);
    }
}
