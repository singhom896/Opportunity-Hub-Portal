<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Traits\UploadAssetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TestimonialController extends Controller
{
    use UploadAssetTrait;

    public function list()
    {
        $testimonials = Testimonial::with('asset')->orderBy('sortorder')->orderByDesc('id')->get();
        return view('backend.testimonial.list', compact('testimonials'));
    }

    public function createEdit($id = null)
    {
        $testimonial = null;
        if ($id) {
            $testimonial = Testimonial::findOrFail(Crypt::decrypt($id));
        }
        return view('backend.testimonial.addEdit', compact('testimonial'));
    }

    public function saveUpdate(Request $request)
    {
        $id = $request->filled('id') ? Crypt::decrypt($request->id) : null;

        $request->validate([
            'name' => ['required','string','min:3','max:150','regex:/^[A-Za-z ]+$/'],
            'age' => ['nullable','integer','min:1','max:99'],
            'address' => ['nullable','string','max:250'],
            'testimonial' => ['required','string','max:1000'],
            'sortorder' => ['required','integer','min:0'],
            'active' => ['required','in:0,1'],
            'photo' => [$id ? 'nullable' : 'required','image','mimes:jpg,jpeg,png','max:2048','dimensions:max_width=400,max_height=400'],
        ], [
            'name.regex' => 'Name may contain only letters and spaces.',
            'photo.mimes' => 'Photo must be JPG, JPEG or PNG.',
            'photo.max' => 'Photo size must not exceed 2MB.',
            'photo.dimensions' => 'Photo dimensions must not exceed 400x400 pixels.',
        ]);

        if ($id) {
            $testimonial = Testimonial::findOrFail($id);

            if ($request->hasFile('photo')) {
                $testimonial->assets_id = $this->updateAssetInPublic($request->file('photo'), $testimonial->assets_id, 'testimonial');
            }

            $testimonial->name = $request->name;
            $testimonial->age = $request->age;
            $testimonial->address = $request->address;
            $testimonial->testimonial = $request->testimonial;
            $testimonial->sortorder = $request->sortorder;
            $testimonial->active = $request->active;
            $testimonial->updated_by = Auth::id();
            $testimonial->save();

            return redirect()->route('testimonial.list')->with('success','Testimonial updated successfully.');
        }

        $assetsId = $this->uploadAssetToPublic($request->file('photo'), 'testimonial');

        Testimonial::create(['assets_id'=>$assetsId,'name'=>$request->name,'age'=>$request->age,'address'=>$request->address,'testimonial'=>$request->testimonial,'sortorder'=>$request->sortorder,'active'=>$request->active,'created_by'=>Auth::id()]);

        return redirect()->route('testimonial.list')->with('success','Testimonial added successfully.');
    }

    public function changeStatus(Request $request)
    {
        $request->validate(['id'=>'required|integer|exists:testimonials,id','active'=>'required|in:0,1']);
        $testimonial = Testimonial::findOrFail($request->id);
        $testimonial->update(['active'=>$request->active,'updated_by'=>Auth::id()]);
        return response()->json(['success'=>true,'message'=>$request->active ? 'Testimonial activated successfully.' : 'Testimonial deactivated successfully.']);
    }

    public function delete(Request $request)
    {
        $request->validate(['id'=>'required|integer|exists:testimonials,id']);
        $testimonial = Testimonial::findOrFail($request->id);
        if ($testimonial->assets_id) $this->deleteAssetById($testimonial->assets_id);
        $testimonial->delete();
        return response()->json(['success'=>true,'message'=>'Testimonial deleted successfully.']);
    }
}
