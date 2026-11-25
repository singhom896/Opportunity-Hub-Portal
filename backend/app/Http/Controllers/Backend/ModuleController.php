<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



class ModuleController extends Controller
{


    public function modules()
    { 
        $modules = Module::with(['user.role', 'children'])->whereNull('parent_id')->orderBy('id')->get();
        return view('backend.modules.list', compact('modules'));
    }

    public function childmodules(Request $request, $id)
    {
        //dd(decrypt($id));
        $modules = Module::where('parent_id', decrypt($id))
            ->with(['user.role', 'children'])
            // ->when($request->filled('status'), function ($q) use ($request) {
            //     $q->where('active', $request->status);
            // })
            // ->when($request->filled('page_type'), function ($q) use ($request) {
            //     $q->where('type', $request->page_type);
            // })
            ->orderBy('position')
            ->get();
        return view('backend.modules.child', compact('modules', 'id'));
    }



    public function createmodules()
    {
        //$modules = Module::where('active', 1)->orderBy('module_name', 'asc')->pluck('module_name', 'module_id');
		$modules = Module::where('active', 1)
    ->where(function ($query) {
        $query->whereNull('parent_id')
              ->orWhere('parent_id', 0);
    })
    ->orderBy('module_name', 'asc')
    ->pluck('module_name', 'module_id');
        $nextModuleId = (Module::max('module_id') ?? 0) + 1;
        $nextPosition = (Module::max('position') ?? 0) + 1;
        return view('backend.modules.createEdit', compact('modules','nextModuleId','nextPosition'));
    }

    public function editmodules($id)
    {
        $module = Module::findOrFail(decrypt($id));
/* 
        $modules = Module::where('active', 1)
            ->where('id', '!=', $module->id)
            ->pluck('module_name', 'module_id'); */
			
		$modules = Module::where('active', 1)
			->where('id', '!=', $module->id)
			->where(function ($query) {
				$query->whereNull('parent_id')
					  ->orWhere('parent_id', 0);
			})
			->orderBy('module_name', 'asc')
			->pluck('module_name', 'module_id');

        return view('backend.modules.createEdit', compact('modules', 'module'));
    }

    public function saveUpdateModule(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:modules,id',
            'moduleid' => [
                'required',
                'integer',
                'unique:modules,module_id,' . $request->id
            ],
            'parent' => 'nullable|exists:modules,module_id',
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'url' => 'required',
            'position' => ['required', 'integer'],
            'icon' => ['required'],
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ]);

        $data = [
            'module_id' => $validated['moduleid'],
            'parent_id' => $validated['parent'] ?? null,
            'module_name' => $validated['name'],
            'page_url' => $validated['url'],
            'position' => $validated['position'],
            'icon_name' => $validated['icon'],
            'active' => $validated['status'],
        ];

        if (!empty($validated['id'])) {

            $module = Module::findOrFail($validated['id']);

            $data['updated_by'] = Auth::id();

            $module->update($data);

            $message = 'Module updated successfully!';
        } else {

            $data['created_by'] = Auth::id();

            Module::create($data);

            $message = 'Module created successfully!';
        }

        return redirect()
            ->route('modules.list')
            ->with('success', $message);
    }

    /*   public function createmodules()
    {
        $module = Module::where('active', 1)->pluck('module_name', 'module_id');
        //dd($module);
        return view('backend.modules.create', compact('module'));
    }

    public function savemodules(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'moduleid' => ['required', 'integer', 'unique:modules,module_id'],
            'parent' => 'nullable|exists:modules,module_id',
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'url' => 'required',
            'position' => ['required', 'integer'],
            'icon' => ['required'],
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ]);
        Module::create([
            'module_id'  => $validated['moduleid'],
            'parent_id'  => $validated['parent'],
            'module_name'       => $validated['name'],
            'page_url'        => $validated['url'],
            'position'   => $validated['position'],
            'icon_name'       => $validated['icon'],
            'active'     => $validated['status'],
            'created_by' => Auth::id(),
        ]);
        return redirect()->route('modules.list')->with('success', 'Module created successfully!');
    }

      public function editmodules($id)
    {
        $module = Module::findOrFail(decrypt($id));
        $modules = Module::where('active', 1)->pluck('module_name', 'module_id');
        return view('backend.modules.edit', compact('modules', 'module', 'id'));
    }


    public function updatemodule(Request $request)
    {
        $validated = $request->validate([
            'id'  => ['required', 'exists:modules,id'],
            'moduleid' => 'required|integer|unique:modules,module_id,' . $request->id,
            'parent' => 'nullable|exists:modules,module_id',
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'url' => 'required',
            'position' => ['required', 'integer'],
            'icon' => ['required'],
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ]);
        $module = Module::findOrFail($validated['id']);
        $module->update([
            'module_id'   => $validated['moduleid'],
            'parent_id'   => $validated['parent'],
            'module_name' => $validated['name'],
            'page_url'    => $validated['url'],
            'position'    => $validated['position'],
            'icon_name'   => $validated['icon'],
            'active'      => $validated['status'],
            'updated_by'  => Auth::id(),
        ]);
        return redirect()->route('modules.list')->with('success', 'Module updated successfully!');
    } */

    public function modulesstatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:modules,id',
            'status' => 'required|boolean'
        ]);
        $module = Module::with('children')->findOrFail($request->id);
        $hasActiveChildren = $module->children->contains(fn($child) => $child->active);
        if ($hasActiveChildren) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status. Please deactivate all child modules first.'
            ], 400);
        }
        $module->active = $request->status;
        $module->save();
        if ($module->active) {
            return response()->json([
                'success' => true,
                'status' => $module->active,
                'message' => 'Module activated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => $module->active,
                'message' => 'Module deactivated successfully.'
            ]);
        }
    }


    public function modulesdelete(Request $request)
    {
        try {
            $menu = Module::with('children')->findOrFail($request->id);
            if ($menu->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Module cannot be deleted because it has child modules.'
                ], 400);
            }
            $menu->delete();
            return response()->json([
                'success' => true,
                'message' => 'Module deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Module could not be deleted.' . $e
            ], 400);
        }
    }

    public function moduleSorting()
    {
        /* $modules = Module::where('active', 1)
    ->where(function ($query) {
        $query->where('parent_id', 0)
              ->orWhereNull('parent_id');
    })
    ->orderBy('position', 'asc')
    ->get();

    return view('backend.modules.sorting', compact('modules')); */

        $modules = Module::where('active', 1)
            ->where(function ($query) {
                $query->where('parent_id', 0)
                    ->orWhereNull('parent_id');
            })
            ->with(['children' => function ($query) {
                $query->where('active', 1)
                    ->orderBy('position', 'asc');
            }])
            ->orderBy('position', 'asc')
            ->get();
        return view('backend.modules.sorting', compact('modules'));
    }

    public function updatePosition(Request $request)
    {
        try {

            $validated = $request->validate([
                'modules' => 'required|array',
                'modules.*.module_id' => 'required|integer|exists:modules,module_id',
                'modules.*.parent_id' => 'nullable|integer',
                'modules.*.position' => 'required|integer|min:1',
            ]);

            foreach ($validated['modules'] as $module) {

                Module::where('module_id', $module['module_id'])
                    ->update([
                        'position' => $module['position'],
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Module order updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            Log::error('Module sorting failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to update module order.'
            ], 500);
        }
    }
}
