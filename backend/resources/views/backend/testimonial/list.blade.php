@extends('backend.layouts.app')
@section('title','Testimonials')
@section('content')
<div class="container-fluid">
    <div class="row mb-0">
        <div class="col-12 text-center">
            <div class="d-inline-block">
                <h1 class="mb-1 fw-bold">Testimonials Management</h1>
                <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
            </div>
        </div>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    <div class="d-flex justify-content-end gap-2 mb-3">
        @permission('create','testimonial')
            <a href="{{ route('testimonial.create') }}" class="theme-btn rounded-2"><i class="bi bi-plus-lg me-1"></i> Create</a>
        @endpermission
    </div>
    <div class="row py-2">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-data">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Address</th>
                            <th>Testimonial</th>
                            <th>Sort Order</th>
                            <th>Is Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testimonials as $index=>$testimonial)
                            <tr>
                                <th scope="row" class="text-end">{{ $index+1 }}</th>
                                <td>
                                    @if($testimonial->asset)
                                        <img src="{{ asset($testimonial->asset->url) }}" alt="{{ $testimonial->name }}" class="img-thumbnail" style="width:60px;height:60px;object-fit:cover;">
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $testimonial->name ?? '—' }}</td>
                                <td>{{ $testimonial->age ?? '—' }}</td>
                                <td>{{ $testimonial->address ?? '—' }}</td>
                                <td>{{ Str::limit($testimonial->testimonial,100) }}</td>
                                <td>{{ $testimonial->sortorder }}</td>
                                <td class="text-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-change pointer" role="button" type="checkbox" data-id="{{ $testimonial->id }}" data-active="{{ $testimonial->active }}" {{ $testimonial->active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <ul class="d-flex align-items-center justify-content-center gap-3">
                                        @permission('edit','testimonial')
                                            <li><a href="{{ route('testimonial.edit',Crypt::encrypt($testimonial->id)) }}" class="fs-5 edit"><i class="bi bi-pencil-square"></i></a></li>
                                        @endpermission
                                        @permission('delete','testimonial')
                                            <li><a href="javascript:void(0)" class="fs-5 text-danger delete-testimonial" data-id="{{ $testimonial->id }}"><i class="bi bi-trash"></i></a></li>
                                        @endpermission
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(() => {
    let table = new DataTable('.table-data');

    $(document).on('click','.status-change',function(e){
        e.preventDefault();
        let checkbox=$(this),id=checkbox.data('id'),active=checkbox.data('active');
        Swal.fire({title:'Change status?',text:active==1?'Do you want to deactivate this Testimonial?':'Do you want to activate this Testimonial?',icon:'question',showCancelButton:true,confirmButtonText:'Yes, update',cancelButtonText:'Cancel'}).then((result)=>{
            if(!result.isConfirmed)return;
            $.ajax({
                url:"{{ route('testimonial.status') }}",type:"POST",data:{id:id,active:active==1?0:1,_token:"{{ csrf_token() }}"},
                beforeSend:function(){checkbox.prop('disabled',true);},
                success:function(response){
                    if(response.success){checkbox.data('active',response.status);checkbox.prop('checked',response.status==1);Swal.fire('Updated!',response.message,'success');}
                    else{checkbox.prop('checked',active==1);Swal.fire('Error!',response.message,'error');}
                },
                error:function(xhr){checkbox.prop('checked',active==1);let message=xhr.responseJSON?.message||'Something went wrong!';Swal.fire('Error!',message,'error');},
                complete:function(){checkbox.prop('disabled',false);}
            });
        });
    });

    $(document).on('click','.delete-testimonial',function(){
        let id=$(this).data('id');
        Swal.fire({title:'Delete Testimonial?',text:'Are you sure you want to delete this testimonial?',icon:'warning',showCancelButton:true,confirmButtonText:'Yes, delete',cancelButtonText:'Cancel'}).then((result)=>{
            if(!result.isConfirmed)return;
            $.ajax({
                url:"{{ route('testimonial.delete') }}",type:"POST",data:{id:id,_token:"{{ csrf_token() }}"},
                success:function(response){if(response.success){Swal.fire('Deleted!',response.message,'success').then(()=>location.reload());}else{Swal.fire('Error!',response.message,'error');}},
                error:function(xhr){Swal.fire('Error!',xhr.responseJSON?.message||'Something went wrong!','error');}
            });
        });
    });
})();
</script>
@endpush
