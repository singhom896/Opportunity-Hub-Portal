<?php namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Traits\UploadAssetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class FaqsController extends Controller
{
    use UploadAssetTrait;

    public function list(){ $videos=Video::with('asset')->where('is_deleted',0)->orderBy('sortorder')->orderByDesc('id')->get(); return view('backend.video.list',compact('videos')); }

    public function createEdit($id=null){ $video=null; if($id)$video=Video::where('is_deleted',0)->findOrFail(Crypt::decrypt($id)); return view('backend.video.addEdit',compact('video')); }

    public function saveUpdate(Request $request){
        $id=$request->filled('id')?Crypt::decrypt($request->id):null;
        $request->validate([
            'title'=>['required','string','min:3','max:255'],
            'video_type'=>['required','in:1,2'],
            'video_url'=>['nullable','url','max:500','required_if:video_type,1'],
            'video'=>[$request->video_type==2&& !$id?'required':'nullable','file','mimes:mp4','max:10240'],
            'date'=>['nullable','date'],
            'sortorder'=>['required','integer','min:0'],
            'active'=>['required','in:0,1'],
        ],[
            'title.required'=>'Title is required.',
            'video_url.required_if'=>'YouTube URL is required.',
            'video_url.url'=>'Please enter a valid video URL.',
            'video.required'=>'MP4 video is required.',
            'video.mimes'=>'Only MP4 videos are allowed.',
            'video.max'=>'Video size must not exceed 10MB.',
        ]);

        if($request->hasFile('video')){
            $file=$request->file('video');
            $content=file_get_contents($file->getRealPath());
            if(preg_match('/<script\b[^>]*>|javascript:/i',$content))return back()->withErrors(['video'=>'Invalid video file.']);
        }

        if($id){
            $video=Video::where('is_deleted',0)->findOrFail($id);
            if($request->video_type==2 && $request->hasFile('video'))$video->assets_id=$this->updateAssetInPublic($request->file('video'),$video->assets_id,'videos');
            if($request->video_type==1 && $video->assets_id){$this->deleteAssetById($video->assets_id);$video->assets_id=null;}
            $video->title=$request->title;
            $video->video_type=$request->video_type;
            $video->video_url=$request->video_type==1?$this->getYoutubeVideoId($request->video_url):null;
            $video->date=$request->date;
            $video->sortorder=$request->sortorder;
            $video->active=$request->active;
            $video->updated_by=Auth::id();
            $video->save();
            return redirect()->route('video.list')->with('success','Video updated successfully.');
        }

        $assetsId=$request->video_type==2?$this->uploadAssetToPublic($request->file('video'),'videos'):null;
        Video::create(['title'=>$request->title,'video_type'=>$request->video_type,'video_url'=>$request->video_type==1?$this->getYoutubeVideoId($request->video_url):null,'assets_id'=>$assetsId,'date'=>$request->date,'sortorder'=>$request->sortorder,'active'=>$request->active,'created_by'=>Auth::id()]);
        return redirect()->route('video.list')->with('success','Video added successfully.');
    }

    private function getYoutubeVideoId($url){preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/',$url,$matches);return $matches[1]??null;}

    public function changeStatus(Request $request){
        $request->validate(['id'=>'required|integer|exists:videos,id','active'=>'required|in:0,1']);
        $video=Video::where('is_deleted',0)->findOrFail($request->id);
        $video->active=$request->active;
        $video->updated_by=Auth::id();
        $video->save();
        return response()->json(['success'=>true,'status'=>(int)$video->active,'message'=>$video->active?'Video activated successfully.':'Video deactivated successfully.']);
    }

    public function delete(Request $request){
        $request->validate(['id'=>'required|integer|exists:videos,id']);
        $video=Video::where('is_deleted',0)->findOrFail($request->id);
        if($video->assets_id)$this->deleteAssetById($video->assets_id);
        $video->is_deleted=1;
        $video->deleted_by=Auth::id();
        $video->deleted_at=now();
        $video->save();
        return response()->json(['success'=>true,'message'=>'Video deleted successfully.']);
    }
}
