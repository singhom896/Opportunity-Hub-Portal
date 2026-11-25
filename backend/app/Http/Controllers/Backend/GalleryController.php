<?php namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Traits\UploadAssetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class GalleryController extends Controller
{
    use UploadAssetTrait;

    public function list(){ $galleries=Gallery::with('asset')->where('is_deleted',0)->orderBy('sortorder')->orderByDesc('id')->get(); return view('backend.gallery.list',compact('galleries')); }

    public function createEdit($id=null){ $gallery=null; if($id)$gallery=Gallery::where('is_deleted',0)->findOrFail(Crypt::decrypt($id)); return view('backend.gallery.addEdit',compact('gallery')); }

    public function saveUpdate(Request $request){
        $id=$request->filled('id')?Crypt::decrypt($request->id):null;
        $request->validate([
            'title'=>['required','string','min:3','max:255'],
            'type'=>['nullable','string','max:50'],
            'date'=>['nullable','date'],
            'sortorder'=>['required','integer','min:0'],
            'active'=>['required','in:0,1'],
            'image'=>[$id?'nullable':'required','image','mimes:jpg,jpeg,png','max:2048'],
        ],[
            'title.required'=>'Title is required.',
            'image.required'=>'Image is required.',
            'image.mimes'=>'Image must be JPG, JPEG or PNG.',
            'image.max'=>'Image size must not exceed 2MB.',
        ]);

        if($id){
            $gallery=Gallery::where('is_deleted',0)->findOrFail($id);
            if($request->hasFile('image'))$gallery->assets_id=$this->updateAssetInPublic($request->file('image'),$gallery->assets_id,'gallery');
            $gallery->title=$request->title;
            $gallery->type=$request->type;
            $gallery->date=$request->date;
            $gallery->sortorder=$request->sortorder;
            $gallery->active=$request->active;
            $gallery->updated_by=Auth::id();
            $gallery->save();
            return redirect()->route('gallery.list')->with('success','Gallery updated successfully.');
        }

        $assetsId=$this->uploadAssetToPublic($request->file('image'),'gallery');
        Gallery::create(['assets_id'=>$assetsId,'type'=>$request->type,'title'=>$request->title,'date'=>$request->date,'sortorder'=>$request->sortorder,'active'=>$request->active,'created_by'=>Auth::id()]);
        return redirect()->route('gallery.list')->with('success','Gallery added successfully.');
    }

    public function changeStatus(Request $request){
        $request->validate(['id'=>'required|integer|exists:galleries,id','active'=>'required|in:0,1']);
        $gallery=Gallery::where('is_deleted',0)->findOrFail($request->id);
        $gallery->active=$request->active;
        $gallery->updated_by=Auth::id();
        $gallery->save();
        return response()->json(['success'=>true,'status'=>(int)$gallery->active,'message'=>$gallery->active?'Gallery activated successfully.':'Gallery deactivated successfully.']);
    }

    public function delete(Request $request){
        $request->validate(['id'=>'required|integer|exists:galleries,id']);
        $gallery=Gallery::where('is_deleted',0)->findOrFail($request->id);
        if($gallery->assets_id)$this->deleteAssetById($gallery->assets_id);
        $gallery->is_deleted=1;
        $gallery->deleted_by=Auth::id();
        $gallery->deleted_at=now();
        $gallery->save();
        return response()->json(['success'=>true,'message'=>'Gallery deleted successfully.']);
    }
}
