<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class ContentController extends Controller
{
    public function list()
    {
        $contents = Content::with('menu')
            ->orderByDesc('id')
            ->get();

        return view('backend.content.list', compact('contents'));
    }

    public function createEditaaa($id = null)
    {
        $menus = MenuItem::with('parent')
            ->where('active', 1)
            ->where('type', 2)
            ->orderBy('title', 'asc')
            ->get();

        $content = null;

        if ($id) {
            $contentId = Crypt::decrypt($id);

            $content = Content::findOrFail($contentId);
        }

        return view('backend.content.addEdit', compact('content', 'menus'));
    }
    public function createEdit($id = null)
{
    $content = null;

    if ($id) {
        $contentId = Crypt::decrypt($id);
        $content = Content::findOrFail($contentId);
    }

    $menus = MenuItem::with('parent')
        ->where('active', 1)
        ->where('type', 2)
        ->where(function ($query) use ($content) {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('contents')
                    ->whereColumn('contents.menu_id', 'menu_items.id');
            });

            if ($content) {
                $query->orWhere('menu_items.id', $content->menu_id);
            }
        })
        ->orderBy('title', 'asc')
        ->get();

    return view('backend.content.addEdit', compact('content', 'menus'));
}

    public function saveUpdate(Request $request)
    {
        $menuIds = MenuItem::where('active', 1)
            ->where('type', 2)
            ->pluck('id');

        $validatedData = $request->validate([
            'id' => ['nullable'],
            'title' => ['required', 'string', 'max:255'],
            'menu' => [
                'required',
                'integer',
                'in:' . implode(',', $menuIds->toArray())
            ],
            'cktext' => ['required', 'string'],
            'status' => ['required', 'in:0,1'],
        ], [
            'title.required' => 'Please enter Title.',
            'title.string' => 'Title must be a valid text.',
            'title.max' => 'Title may not be greater than 255 characters.',
            'menu.required' => 'Please select Menu.',
            'menu.integer' => 'Invalid Menu selected.',
            'menu.in' => 'Invalid Menu selected.',
            'cktext.required' => 'Please enter Content.',
            'status.required' => 'Please select Status.',
            'status.in' => 'Invalid Status selected.',
        ]);

        $menu = MenuItem::where('id', $validatedData['menu'])
            ->where('active', 1)
            ->where('type', 2)
            ->firstOrFail();

        $slug = null;

        if (!empty($menu->url)) {
            $slug = basename(trim($menu->url, '/'));
        }

        $data = [
            'menu_id' => $menu->id,
            'slug' => $slug,
            'title' => trim($validatedData['title']),
            'content' => $validatedData['cktext'],
            'is_active' => $validatedData['status'],
        ];

        if (!empty($validatedData['id'])) {

            $contentId = Crypt::decrypt($validatedData['id']);

            $content = Content::findOrFail($contentId);

            $content->update($data);

            $message = 'Content updated successfully!';
        } else {

            $data['created_by'] = Auth::id();

            Content::create($data);

            $message = 'Content created successfully!';
        }

        return redirect()
            ->route('content.list')
            ->with('success', $message);
    }

    public function checkContentById(Request $request)
    {
        $request->validate([
            'menu_id' => ['required', 'integer'],
            'id' => ['nullable'],
        ]);

        $query = Content::where('menu_id', $request->menu_id);

        if (!empty($request->id)) {
            $contentId = Crypt::decrypt($request->id);

            $query->where('id', '!=', $contentId);
        }

        if ($query->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Content already exists for this Menu.'
            ], 422);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => [
                'required',
                'integer',
                'exists:contents,id'
            ],
            'status' => [
                'required',
                'boolean'
            ],
        ]);

        $content = Content::findOrFail($request->id);

        $content->is_active = $request->status;
        $content->save();

        return response()->json([
            'success' => true,
            'status' => $content->is_active,
            'message' => $content->is_active
                ? 'Content activated successfully.'
                : 'Content deactivated successfully.',
        ]);
    }
}
