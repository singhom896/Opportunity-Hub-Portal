<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;


class MenuController extends Controller
{


    public function menus(Request $request)
    {
        $menu = MenuItem::whereNull('parent_id')
            ->with(['user.role', 'childrenRecursive.user.role'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('active', $request->status);
            })
            ->when($request->filled('page_type'), function ($q) use ($request) {
                $q->where('type', $request->page_type);
            })
            ->menuType((int) request('type'))
            ->orderBy('order_index')
            ->get();
        return view('backend.menus.list', compact('menu'));
    }

    public function menustatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:menu_items,id',
            'status' => 'required|boolean'
        ]);
        $menu = MenuItem::with('childrenRecursive')->findOrFail($request->id);
        $hasActiveChildren = $menu->childrenRecursive->contains(fn($child) => $child->active);
        if ($hasActiveChildren) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status. Please deactivate all child menus first.'
            ], 400);
        }
        $menu->active = $request->status;
        $menu->save();
        if ($menu->active) {
            return response()->json([
                'success' => true,
                'status' => $menu->active,
                'message' => 'Menu activated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => $menu->active,
                'message' => 'Menu deactivated successfully.'
            ]);
        }
    }
    public function menudelete(Request $request)
    {
        try {
            $menu = MenuItem::with('children')->findOrFail($request->id);
            if ($menu->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu cannot be deleted because it has child menus.'
                ], 400);
            }
            $menu->delete();
            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu could not be deleted.' . $e
            ], 400);
        }
    }


    public function createmenus()
    {
        $menuItem = MenuItem::pluck('title', 'id');
        $nextOrder = (MenuItem::max('order_index') ?? 0) + 1;
        return view('backend.menus.createEdit', compact('menuItem', 'nextOrder'));
    }

    public function editmenu($id)
    {
        $menuItem = MenuItem::pluck('title', 'id');

        $menu = MenuItem::findOrFail(decrypt($id));

        return view('backend.menus.createEdit', compact('menu', 'menuItem'));
    }


    public function saveUpdateMenu(Request $request)
    {
        /*  $validated = $request->validate([
            'title' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'url' => 'required',
            'parent' => 'nullable|exists:menu_items,id',
            'target' => ['required', 'in:' . implode(',', array_keys(targettype()))],
            'page_type' => ['required', 'in:' . implode(',', array_keys(pagetype()))],
            'menu_type' => ['required', 'in:' . implode(',', array_keys(MenuItem::menuTypeOptions()))],
            'ordering' => [
                'required',
                'integer',
                'unique:menu_items,order_index,' . ($request->id ?? 'NULL')
            ],
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ]);
 */
        $menuId = null;

        if ($request->filled('id')) {
            $menuId = $request->id;
        }

        $validated = $request->validate([
            'title' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'url' => ['required'],
            'parent' => ['nullable', 'exists:menu_items,id'],
            'target' => ['required', 'in:' . implode(',', array_keys(targettype()))],
            'page_type' => ['required', 'in:' . implode(',', array_keys(pagetype()))],
            'menu_type' => ['required', 'in:' . implode(',', array_keys(MenuItem::menuTypeOptions()))],
            'ordering' => [
                'required',
                'integer',
                Rule::unique('menu_items', 'order_index')->ignore($menuId),
            ],
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ]);


        $data = [
            'title'       => $validated['title'],
            'url'         => $validated['url'],
            'target'      => $validated['target'],
            'parent_id'   => $validated['parent'],
            'type'        => $validated['page_type'],
            'order_index' => $validated['ordering'],
            'active'      => $validated['status'],
            'is_main'     => in_array((int) $validated['menu_type'], [1, 3]) ? 1 : 0,
            'is_footer'   => in_array((int) $validated['menu_type'], [2, 3]) ? 1 : 0,
        ];

        // Update
        if ($request->filled('id')) {
            $menu = MenuItem::findOrFail($request->id);
            $menu->update($data);
            $message = 'Menu Item updated successfully!';
        }
        // Create
        else {

            $data['created_by'] = Auth::id();
            MenuItem::create($data);
            $message = 'Menu Item created successfully!';
        }

        return redirect()->route('menus.list')->with('success', $message);
    }


    public function childmenus(Request $request, $id)
    {
        $menu = MenuItem::where('parent_id', decrypt($id))
            ->with(['user.role', 'childrenRecursive.user.role'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('active', $request->status);
            })
            ->when($request->filled('page_type'), function ($q) use ($request) {
                $q->where('type', $request->page_type);
            })
            ->menuType((int) request('type'))
            ->orderBy('order_index')
            ->get();
        return view('backend.menus.child', compact('menu', 'id'));
    }

    public function menuSorting()
    {
        $menus = MenuItem::where('active', 1)
            ->where(function ($query) {
                $query->where('parent_id', 0)
                    ->orWhereNull('parent_id');
            })
            ->with([
                'children' => function ($query) {
                    $query->where('active', 1)
                        ->orderBy('order_index', 'asc');
                }
            ])
            ->orderBy('order_index', 'asc')
            ->get();

        return view('backend.menus.sorting', compact('menus'));
    }

    public function updatePosition_old(Request $request)
    {
        try {
            $validated = $request->validate([
                'menus' => 'required|array',
                'menus.*.id' => 'required|integer|exists:menu_items,id',
                'menus.*.parent_id' => 'nullable|integer',
                'menus.*.order_index' => 'required|integer|min:1',
            ]);

            foreach ($validated['menus'] as $menu) {
                MenuItem::where('id', $menu['id'])->update([
                    'order_index' => $menu['order_index'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Menu order updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Menu sorting failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to update menu order.'
            ], 500);
        }
    }

   public function updatePosition(Request $request)
{
    try {
        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|integer|exists:menu_items,id',
            'menus.*.parent_id' => 'nullable|integer',
            'menus.*.order_index' => 'required|integer|min:1',
        ]);

        // Sort according to received position
        $menus = collect($validated['menus'])
            ->sortBy('order_index')
            ->values();

        // First assign temporary unique values
        foreach ($menus as $menu) {
            MenuItem::where('id', $menu['id'])->update([
                'order_index' => 100000 + $menu['id'],
            ]);
        }

        // Now assign globally unique order_index
        $orderIndex = 1;

        foreach ($menus as $menu) {
            MenuItem::where('id', $menu['id'])->update([
                'order_index' => $orderIndex,
            ]);

            $orderIndex++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu order updated successfully.'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        return response()->json([
            'success' => false,
            'message' => collect($e->errors())->flatten()->first()
        ], 422);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
