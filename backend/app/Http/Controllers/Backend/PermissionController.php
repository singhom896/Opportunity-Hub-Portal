<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class PermissionController extends Controller
{


	public function permissionsList($role_id = null)
	{
		$roles = Role::where('active', 1)->get();

		$selectedRole = $role_id ? Crypt::decrypt($role_id) : null;

		$permissions = DB::table('modules as m')
			->leftJoin('permissions as p', function ($join) use ($selectedRole) {
				$join->on('p.module_id', '=', 'm.module_id');
			
				if ($selectedRole) {
					$join->where('p.role_id', '=', $selectedRole);
				}
			})
			->where('m.active', 1)
			->orderBy('m.position')
			->select(
				'm.module_id as mid',
				'm.module_name',
				'p.id as permission_id',
				'p.role_id',
				'p.can_view',
				'p.can_create',
				'p.can_edit',
				'p.can_delete'
			)
			->get();

		return view('backend.permission.list', compact(
			'permissions',
			'roles',
			'selectedRole'
		));
	}
	
	public function permissionstatus(Request $request)
{
    try {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:permissions,id',
            'module_id' => 'nullable|integer|exists:modules,module_id',
            'role_id' => 'required|integer|exists:roles,role_id',
            'permission' => 'required|string|in:can_view,can_create,can_edit,can_delete,all',
            'status' => 'required|boolean',
            'all_modules' => 'nullable|boolean'
        ]);

        $status = (int) $validated['status'];

        /*
         * Apply permission to ALL modules
         */
        if (!empty($validated['all_modules'])) {

            $modules = Module::where('active', 1)->pluck('module_id');

            foreach ($modules as $moduleId) {

                $permission = Permission::firstOrCreate(
                    [
                        'role_id' => $validated['role_id'],
                        'module_id' => $moduleId
                    ],
                    [
                        'can_view' => 0,
                        'can_create' => 0,
                        'can_edit' => 0,
                        'can_delete' => 0
                    ]
                );

                if ($validated['permission'] === 'all') {

                    $permission->can_view = $status;
                    $permission->can_create = $status;
                    $permission->can_edit = $status;
                    $permission->can_delete = $status;

                } else {

                    $permission->{$validated['permission']} = $status;
                }

                $permission->save();
            }

            return response()->json([
                'success' => true,
                'status' => $status,
                'permission' => $validated['permission'],
                'message' => 'Permission updated for all modules successfully.'
            ]);
        }

        /*
         * Single module permission
         */
        $permission = !empty($validated['id'])
            ? Permission::findOrFail($validated['id'])
            : Permission::firstOrCreate(
                [
                    'role_id' => $validated['role_id'],
                    'module_id' => $validated['module_id']
                ],
                [
                    'can_view' => 0,
                    'can_create' => 0,
                    'can_edit' => 0,
                    'can_delete' => 0
                ]
            );

        /*
         * Apply all permissions for single module
         */
        if ($validated['permission'] === 'all') {

            $permission->can_view = $status;
            $permission->can_create = $status;
            $permission->can_edit = $status;
            $permission->can_delete = $status;

        } else {

            $permission->{$validated['permission']} = $status;
        }

        $permission->save();

        return response()->json([
            'success' => true,
            'status' => $status,
            'permission_id' => $permission->id,
            'permission' => $validated['permission'],
            'message' => 'Permission updated successfully.'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        return response()->json([
            'success' => false,
            'message' => $e->errors()
        ], 422);

    } catch (\Exception $e) {

        Log::error('Permission update failed', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.'
        ], 500);
    }
}
	public function permissionstatus11(Request $request)
	{
		try {
			$validated = $request->validate([
				'id' => 'nullable|integer|exists:permissions,id',
				'module_id' => 'required|integer|exists:modules,module_id',
				'role_id' => 'required|integer|exists:roles,role_id',
				'permission' => 'required|string|in:can_view,can_create,can_edit,can_delete,all',
				'status' => 'required|boolean'
			]);

			$status = $validated['status'] == 1 ? 0 : 1;

			$permission = !empty($validated['id'])
				? Permission::findOrFail($validated['id'])
				: Permission::firstOrCreate(
					[
						'role_id' => $validated['role_id'],
						'module_id' => $validated['module_id']
					],
					[
						'can_view' => 0,
						'can_create' => 0,
						'can_edit' => 0,
						'can_delete' => 0
					]
				);

			if ($validated['permission'] === 'all') {
				$permission->can_view = $status;
				$permission->can_create = $status;
				$permission->can_edit = $status;
				$permission->can_delete = $status;
			} else {
				$permission->{$validated['permission']} = $status;
			}

			$permission->save();

			return response()->json([
				'success' => true,
				'status' => (int) $status,
				'permission_id' => $permission->id,
				'permission' => $validated['permission'],
				'message' => 'Permission updated successfully.'
			]);

		} catch (\Illuminate\Validation\ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => $e->errors()
			], 422);

		} catch (\Exception $e) {
			Log::error('Permission update failed', [
				'error' => $e->getMessage()
			]);

			return response()->json([
				'success' => false,
				'message' => 'Something went wrong. Please try again.'
			], 500);
		}
	}


    public function permissionstatussdsd(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'         => 'required|integer|exists:permissions,id',
                'permission' => 'required|string|in:can_view,can_create,can_edit,can_delete',
                'status'     => 'required|boolean'
            ]);

            $permission = Permission::findOrFail($validated['id']);
            $status= $validated['status'] == 1 ? 0 : 1;
            $permission->{$validated['permission']} = $status;
            $permission->save();
            return response()->json([
                'success' => true,
                'status'  => (int)$status,
                'message' => 'Permission updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Permission update failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
	
    public function createpermission()
    {
        $modules = Module::pluck('module_name', 'module_id');
        $roles=Role::pluck('name', 'role_id');
        return view('backend.permission.create',compact('modules','roles'));
    }
    public function savepermissions(Request $request)
    {
        $request->validate([
            'role'         => 'required|integer|exists:roles,role_id',
            'modules'         => 'required|integer|exists:modules,module_id',
        ]);
        $exists = Permission::where('role_id', $request->role)->where('module_id', $request->modules)->first();
        if ($exists) {
            return redirect()->route('permissions.list')->with('error', 'Permission already exists for this role and module.');
        }
        Permission::create([
            'role_id'       => $request->input('role'),
            'module_id'     => $request->input('modules'),
            'can_view'   => $request->has('can_view') ? 1 : 0,
            'can_create' => $request->has('can_create') ? 1 : 0,
            'can_edit'   => $request->has('can_edit') ? 1 : 0,
            'can_delete' => $request->has('can_delete') ? 1 : 0,
        ]);
        return redirect()->route('permissions.list')->with('success', 'Permission added successfully!');
    }
	
	
	//======

}