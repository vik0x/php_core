<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\AltereRoleRequest;

class RoleController extends Controller
{

	public function index()
	{
		$data = [];
		foreach(Role::all() as $role){
			$data[] = [
				"type"  => "roles",
				"id"	=> $role->id,
				"links" => [
					"self"  => url('/roles/'. $role->id)
				],
				"attributes" => [
					$role
				]
			];
		}

		return response()->json([
			'links' => [],
			"data"  => $data
		], 200);
	}

	public function create(AltereRoleRequest $request, Role $role)
	{
		$role->name = $request->input('role');
		$role->save();
		$data = [
			"type"  => "roles",
			"id"	=> $role->id,
			"links" => [
				"self"  => url('/roles/'. $role->id)
			],
			"attributes" => [
				$role
			]
		];
		return $data;
	}

	public function destroy(Request $request, Role $role)
	{
		foreach (Permission::all() as $permission) {
			$role->revokePermissionTo($permission->name);
		} 
		$role->delete();
		$data = [
			"type"  => "roles",
			"id"	=> $role->id,
			"links" => [
				"self"  => url('/roles/'. $role->id)
			],
			"attributes" => [
				$role
			]
		];
		return $data;
	}
}
