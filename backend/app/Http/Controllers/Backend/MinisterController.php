<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Minister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\UploadAssetTrait;

class MinisterController extends Controller
{
    use UploadAssetTrait;
	
    public function ministers()
    {
        $ministers = Minister::with(['asset', 'user.role'])->get();
        return view('backend.ministers.list', compact('ministers'));
    }
    public function createminister()
    {
        return view('backend.ministers.create');
    }
    public function saveminister(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'file' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'ordering' => 'required|unique:ministers,priority_ordering',
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ], [
            'title.required' => 'Minister name is required',
            'title.regex' => 'Minister name can only contain letters and spaces',
            'file.max' => 'Please upload a file smaller or equal to 5MB.'
        ]);
        $minister = new Minister();
        $minister->name = $validated['title'];
        $minister->priority_ordering = $validated['ordering'];
        $minister->active = $validated['status'];
        $minister->created_by = Auth::id();
        $minister->assets_id = $this->uploadAssetToPublic($request->file('file'), 'images/ministers');
        $minister->save();
        return redirect()->route('ministers.list')->with('success', 'Minister created successfully');
    }
    public function editminister($ministerId)
    {
        $minister = Minister::with(['asset'])->findOrFail(decrypt($ministerId));
        return view('backend.ministers.edit', compact('minister'));
    }
    public function updateminister(Request $request)
    {
        $minister = Minister::findOrFail($request->id);
        $request->validate([
            'title' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'file' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'ordering' => 'required|unique:ministers,priority_ordering,' . $minister->id,
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ], [
            'title.required' => 'Minister name is required',
            'title.regex' => 'Minister name can only contain letters and spaces',
            'file.max' => 'Please upload a file smaller or equal to 5MB.'
        ]);

        $data = [
            'name'             => $request->title,
            'priority_ordering' => $request->ordering,
            'active'            => $request->status,
        ];
        if ($request->hasFile('file')) {
            $newAssetId = $this->updateAssetInPublic($request->file('file'), $minister->assets_id, 'images/ministers');
        }
        $minister->update($data);
        return redirect()->route('ministers.list')->with('success', 'Minister updated successfully!');
    }
    public function ministerstatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ministers,id',
            'status' => 'required|boolean'
        ]);
        $minister = Minister::findOrFail($request->id);
        $minister->active = $request->status;
        $minister->save();
        if ($minister->active) {
            return response()->json([
                'success' => true,
                'status' => $minister->active,
                'message' => 'Minister activated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => $minister->active,
                'message' => 'Minister deactivated successfully.'
            ]);
        }
    }
    public function ministerdelete(Request $request)
    {
        try {
            $minister = Minister::findOrFail($request->id);
            if ($minister->assets_id) {
                $this->deleteAssetById($minister->assets_id);
            }
            $minister->delete();
            return response()->json([
                'success' => true,
                'message' => 'Minister and related assets deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Minister could not be deleted.'
            ], 400);
        }
    }
	
	
}
