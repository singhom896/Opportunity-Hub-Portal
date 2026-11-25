<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{


    public function role()
    {

		$roles = Role::withCount([
        'users',
        'permissions'
		])->get();
        return view('backend.role.list', compact('roles'));
    }

	public function createrole()
	{
		return view('backend.role.createEdit');
	}

	public function editrole($id)
	{
		$role = Role::findOrFail(decrypt($id));
		return view('backend.role.createEdit', compact('role'));
	}

	public function saveUpdateRole(Request $request)
	{
		$validatedData = $request->validate([
			'roleid' => [
				'required',
				'integer',
				'unique:roles,role_id,' . ($request->id ?? 'NULL')
			],

			'role' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],

			'status' => [
				'required',
				'in:' . implode(',', array_keys(statusoptions()))
			],
		], [
			'roleid.required' => 'Enter Role ID',
			'roleid.integer' => 'Role ID must be a number',
			'role.required' => 'Enter Role Name',
			'role.regex' => 'Role must contain letters and spaces only',
			'status.required' => 'Please choose Status',
			'status.in' => 'Invalid Status selected',
		]);

		$data = [
			'role_id' => $validatedData['roleid'],
			'name'    => $validatedData['role'],
			'active'  => $validatedData['status'],
		];

		// UPDATE
		if ($request->filled('id')) {

			$role = Role::findOrFail($request->id);

			$role->update($data);

			$message = 'Role updated successfully!';

		} else {

			// CREATE
			Role::create($data);

			$message = 'Role created successfully!';
		}

		return redirect()
			->route('role.list')
			->with('success', $message);
	}

    /* public function createrole()
    {
        return view('backend.role.create');
    } */

   /*  public function saverole(Request $request)
    {
        $validatedData = $request->validate([
            'roleid' => ['required', 'integer', 'unique:roles,role_id'],
            'role' => ['required', 'string', 'regex:/^[a-zA-Z]+$/'],
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ], [
            'role.required' => 'Enter Role Name',
            'role.regex'    => 'Role must contain letters only',
            'status.required'    => 'Please choose Status',
            'status.in'          => 'Invalid Status selected',
        ]);
        Role::create([
            'role_id' => $validatedData['roleid'],
            'name' => $validatedData['role'],
            'status' => $validatedData['status']
        ]);
        return redirect()->route('role.list')->with('success', 'Role Created successfully!');
    } */

    public function rolestatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:roles,role_id',
            'status' => 'required|boolean'
        ]);
        $role = Role::findOrFail($request->id);
        $role->active = $request->status;
        $role->save();
        if ($role->active) {
            return response()->json([
                'success' => true,
                'status' => $role->active,
                'message' => 'Role activated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => $role->active,
                'message' => 'Role deactivated successfully.'
            ]);
        }
    }


}
