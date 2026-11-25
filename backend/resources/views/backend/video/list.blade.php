@extends('backend.layouts.app')
@section('title','Videos')
@section('content')
<div class="container-fluid">
    <div class="row mb-0"><div class="col-12 text-center"><div class="d-inline-block"><h1 class="mb-1 fw-bold">Videos Management</h1><div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div></div></div></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <div class="d-flex justify-content-end gap-2 mb-3">@permission('create','video')<a href="{{ route('video.create') }}" class="theme-btn rounded-2"><i class="bi bi-plus-lg me-1"></i> Create</a>@endpermission</div>
    <div class="row py-2"><div class="col-12"><div class="table-responsive">
        <table class="table table-bordered table-data"><thead><tr><th>S.No</th><th>Video</th><th>Title</th><th>Type</th><th>Date</th><th>Sort Order</th><th>Is Active</th><th>Action</th></tr></thead>
        <tbody>
        @foreach($videos as $index=>$video)
            <tr>
                <th scope="row" class="text-end">{{ $index+1 }}</th>
                <td>
                    @if($video->video_type==2 && $video->asset)
                        <video width="180" height="100" controls class="rounded"><source src="{{ asset($video->asset->url) }}" type="video/mp4"></video>
                    @elseif($video->video_type==1 && $video->video_url)
                        <iframe width="180" height="100" class="rounded" src="https://www.youtube.com/embed/{{ $video->video_url }}" title="{{ $video->title }}" frameborder="0" allowfullscreen></iframe>
                    @else
                        —
                    @endif
                </td>
                <td>{{ $video->title }}</td>
                <td>{{ $video->video_type==1?'YouTube':'MP4' }}</td>
                <td>{{ $video->date?->format('d-m-Y')??'—' }}</td>
                <td>{{ $video->sortorder }}</td>
                <td class="text-center"><div class="form-check form-switch"><input class="form-check-input status-change pointer" role="button" type="checkbox" data-id="{{ $video->id }}" data-active="{{ $video->active }}" {{ $video->active?'checked':'' }}></div></td>
                <td><ul class="d-flex align-items-center justify-content-center gap-3">@permission('edit','video')<li><a href="{{ route('video.edit',Crypt::encrypt($video->id)) }}" class="fs-5 edit"><i class="bi bi-pencil-square"></i></a></li>@endpermission @permission('delete','video')<li><a href="javascript:void(0)" class="fs-5 text-danger delete-video" data-id="{{ $video->id }}"><i class="bi bi-trash"></i></a></li>@endpermission</ul></td>
            </tr>
        @endforeach
        </tbody></table>
    </div></div></div>
</div>
@endsection
@push('scripts')
<script>
(()=>{let table=new DataTable('.table-data');$(document).on('click','.status-change',function(e){e.preventDefault();let checkbox=$(this),id=checkbox.data('id'),active=checkbox.data('active');Swal.fire({title:'Change status?',text:active==1?'Do you want to deactivate this Video?':'Do you want to activate this Video?',icon:'question',showCancelButton:true,confirmButtonText:'Yes, update',cancelButtonText:'Cancel'}).then((result)=>{if(!result.isConfirmed)return;$.ajax({url:"{{ route('video.status') }}",type:"POST",data:{id:id,active:active==1?0:1,_token:"{{ csrf_token() }}"},beforeSend:function(){checkbox.prop('disabled',true)},success:function(response){if(response.success){checkbox.data('active',response.status);checkbox.prop('checked',response.status==1);Swal.fire('Updated!',response.message,'success')}else{checkbox.prop('checked',active==1);Swal.fire('Error!',response.message,'error')}},error:function(xhr){checkbox.prop('checked',active==1);Swal.fire('Error!',xhr.responseJSON?.message||'Something went wrong!','error')},complete:function(){checkbox.prop('disabled',false)}})})});$(document).on('click','.delete-video',function(){let id=$(this).data('id');Swal.fire({title:'Delete Video?',text:'Are you sure you want to delete this video?',icon:'warning',showCancelButton:true,confirmButtonText:'Yes, delete',cancelButtonText:'Cancel'}).then((result)=>{if(!result.isConfirmed)return;$.ajax({url:"{{ route('video.delete') }}",type:"POST",data:{id:id,_token:"{{ csrf_token() }}"},success:function(response){if(response.success)Swal.fire('Deleted!',response.message,'success').then(()=>location.reload());else Swal.fire('Error!',response.message,'error')},error:function(xhr){Swal.fire('Error!',xhr.responseJSON?.message||'Something went wrong!','error')}})})})})();
</script>
@endpush