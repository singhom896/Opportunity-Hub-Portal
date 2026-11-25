<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\UploadAssetTrait;


class BannerController extends Controller
{
    use UploadAssetTrait;

    public function banners()
    {
        $banners = Banner::with(['asset', 'user.role'])->get();
        return view('backend.banners.list', compact('banners'));
    }
    public function create()
    {
        return view('backend.banners.create');
    }
    public function createbanner(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'title' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'file' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'ordering' => 'required|unique:banners,priority_ordering',
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ], [
            'title.required' => 'Banner title is required',
            'title.regex' => 'Banner title can only contain letters and spaces',
            'file.max' => 'Please upload a file smaller or equal to 5MB.'
        ]);
        $banner = new Banner();
        $banner->title = $validated['title'];
        $banner->priority_ordering = $validated['ordering'];
        $banner->active = $validated['status'];
        $banner->created_by = Auth::id();
        $banner->assets_id = $this->uploadAssetToPublic($request->file('file'), 'images/banners');
        $banner->save();
        return redirect()->route('banners.list')->with('success', 'Banner created successfully');
    }
    public function editbanner($bannerId)
    {
        $banner = Banner::with(['asset'])->findOrFail(decrypt($bannerId));
        return view('backend.banners.edit', compact('banner'));
    }
    public function updatebanner(Request $request)
    {
        $banner = Banner::findOrFail($request->id);
        $request->validate([
            'title' => ['required', 'regex:/^[A-Za-z\s]+$/'],
            'file' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'ordering' => 'required|unique:banners,priority_ordering,' . $banner->id,
            'status' => ['required', 'in:' . implode(',', array_keys(statusoptions()))],
        ], [
            'title.required' => 'Banner title is required',
            'title.regex' => 'Banner title can only contain letters and spaces',
            'file.max' => 'Please upload a file smaller or equal to 5MB.'
        ]);

        $data = [
            'title'             => $request->title,
            'priority_ordering' => $request->ordering,
            'active'            => $request->status,
        ];
        if ($request->hasFile('file')) {
            $newAssetId = $this->updateAssetInPublic($request->file('file'), $banner->assets_id, 'images/banners');
            $data['assets_id'] = $newAssetId;
        }
        $banner->update($data);
        return redirect()->route('banners.list')->with('success', 'Banner updated successfully!');
    }
    public function bannerstatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banners,id',
            'status' => 'required|boolean'
        ]);
        $banner = Banner::findOrFail($request->id);
        $banner->active = $request->status;
        $banner->save();
        if ($banner->active) {
            return response()->json([
                'success' => true,
                'status' => $banner->active,
                'message' => 'Banner activated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => $banner->active,
                'message' => 'Banner deactivated successfully.'
            ]);
        }
    }
    public function bannerdelete(Request $request)
    {
        try {
            $banner = Banner::findOrFail($request->id);
            if ($banner->assets_id) {
                $this->deleteAssetById($banner->assets_id);
            }
            $banner->delete();
            return response()->json([
                'success' => true,
                'message' => 'Banner and related assets deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Banner could not be deleted.'
            ], 400);
        }
    }


}
