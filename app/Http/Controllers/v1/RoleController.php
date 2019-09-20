<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;
use Spatie\Permission\Models\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\AlterRoleRequest;
use App\Http\Requests\v1\AssignPermissionsToRoleRequest;
use App\Http\Requests\v1\AssignPermissionsToUserRequest;
use App\Models\v1\Permission;
use App\Transformers\v1\PermissionTransformer;
use App\Transformers\v1\RoleTransformer;

class RoleController extends Controller
{

    public function index()
    {
        $data = fractal()
           ->collection(Role::get(), new RoleTransformer(), 'roles')
           ->serializeWith(new JsonApiSerializer())
           ->toArray();
        return response()->json($data, 200);
    }

    public function create(AlterRoleRequest $request, Role $role)
    {
        $role->name = $request->input('role_name');
        $role->save();
        app('cache')->forget('spatie.permission.cache');
        $data = fractal()
            ->item($role, new RoleTransformer(), 'roles')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        return response()->json($data, 200);
    }

    public function destroy(Request $request, Role $role)
    {
        foreach ($role->permissions as $permission) {
            $role->revokePermissionTo($permission->name);
        }
        $role->delete();
        $data = fractal()
            ->item($role, new RoleTransformer(), 'roles')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        app('cache')->forget('spatie.permission.cache');
        return response()->json($data, 200);
    }

    public function permissions(Request $request)
    {
        $permissions = Permission::filter($request->all())->paginateFilter();
        $data = fractal()
            ->collection($permissions, new PermissionTransformer(), 'users')
            ->paginateWith(new IlluminatePaginatorAdapter($permissions))
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        return response()->json($data, 200);
    }

    public function assignPermissionsToRole(AssignPermissionsToRoleRequest $request, Role $role)
    {
        foreach ($role->permissions as $permission) {
            $role->revokePermissionTo($permission->name);
        }
        foreach ($request->input('permissions') as $permissionId) {
            $role->givePermissionTo(Permission::find($permissionId));
        }
        app('cache')->forget('spatie.permission.cache');
        $data = fractal()
            ->item($role, new RoleTransformer(), 'roles')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();

        return response()->json($data, 200);
    }
}
