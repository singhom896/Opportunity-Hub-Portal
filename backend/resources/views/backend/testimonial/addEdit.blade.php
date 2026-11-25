@extends('backend.layouts.app')
@section('title', isset($testimonial) ? 'Edit Testimonial' : 'Create Testimonial')
@section('content')
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col-12 position-relative text-center">
            <div class="d-inline-block">
                <h1 class="mb-1 fw-bold">{{ isset($testimonial) ? 'Edit Testimonial' : 'Create Testimonial' }}</h1>
                <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
            </div>
            <a href="{{ route('testimonial.list') }}" class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y"><i class="bi bi-caret-left-fill"></i> Back</a>
        </div>
    </div>
    <div class="row justify-content-between py-2">
        <div class="col-xl-12 col-12 mb-md-0 mb-3">
            <form id="testimonial-form" action="{{ route('testimonial.saveUpdate') }}" method="post" class="form-box" enctype="multipart/form-data">
                @csrf
                @if(isset($testimonial)) <x-text-input name="id" type="hidden" value="{{ Crypt::encrypt($testimonial->id) }}" /> @endif
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <x-text-input name="name" type="text" value="{{ old('name',$testimonial->name ?? '') }}" placeholder="Enter Name" label="Name" icon="bi-person" autocomplete="off" :required="true" />
                    </div>
                    <div class="col-lg-4 mb-3">
                        <x-text-input name="age" type="number" value="{{ old('age',$testimonial->age ?? '') }}" placeholder="Enter Age" label="Age" icon="bi-calendar" min="1" max="99" autocomplete="off" :required="false" />
                    </div>
                    <div class="col-lg-4 mb-3">
                        <x-text-input name="sortorder" type="number" value="{{ old('sortorder',$testimonial->sortorder ?? 0) }}" placeholder="Enter Sort Order" label="Sort Order" icon="bi-sort-numeric-down" min="0" autocomplete="off" :required="true" />
                    </div>
                    <div class="col-lg-6 mb-3">
                        <x-text-input name="address" type="text" value="{{ old('address',$testimonial->address ?? '') }}" placeholder="Enter Address" label="Address" icon="bi-house" autocomplete="off" :required="false" />
                    </div>
                    <div class="col-lg-6 mb-3">
                        <div class="d-flex align-items-end gap-2">
                            <div class="flex-grow-1">
                                <x-text-input name="photo" type="file" placeholder="Upload Photo" label="Photo" icon="bi-image" accept="image/jpeg,image/jpg,image/png" :required="!isset($testimonial)" />
                            </div>
                            @if(isset($testimonial) && $testimonial->asset)
                                <div class="pb-1"><a href="{{ asset($testimonial->asset->url) }}" target="_blank" class="border-btn rounded-2 text-nowrap"><i class="bi bi-eye me-1"></i> View</a></div>
                            @endif
                        </div>
                        <small class="text-muted">JPG, JPEG, PNG only. Maximum 2MB.</small>
                    </div>
                    <div class="col-lg-12 mb-3">
                        <label class="form-label">Testimonial <span class="text-danger">*</span></label>
                        <textarea name="testimonial" class="form-control" rows="6" placeholder="Enter Testimonial">{{ old('testimonial',$testimonial->testimonial ?? '') }}</textarea>
                        @error('testimonial') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-lg-4 mb-3">
                        <x-select-input name="active" icon="bi-check2-circle" label="Select Status" selected="{{ old('active',$testimonial->active ?? 1) }}" :options="statusoptions()" placeholder="Select Status" :required="true" />
                    </div>
                    <div class="col-lg-12 my-4">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <button type="submit" class="cms-btn rounded-2">{{ isset($testimonial) ? 'Update' : 'Submit' }}</button>
                            <button type="reset" class="border-btn rounded-2">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
(() => {
    $.validator.addMethod("lettersWithSpaces",function(value,element){return this.optional(element)||/^[A-Za-z]+(?:\s[A-Za-z]+)*$/.test(value);},"Only letters and spaces are allowed");
    $("#testimonial-form").validate({
        ignore:[],errorClass:'text-danger',errorElement:'small',
        rules:{
            name:{required:true,lettersWithSpaces:true,minlength:2,maxlength:150},
            age:{digits:true,min:1,max:99},
            address:{minlength:5,maxlength:250},
            sortorder:{required:true,digits:true,min:0},
            photo:{required:{{ isset($testimonial) ? 'false' : 'true' }},accept:"image/jpg,image/jpeg,image/png"},
            testimonial:{required:true,minlength:10,maxlength:1000},
            active:{required:true,digits:true,range:[0,1]}
        },
        messages:{
            name:{required:"Enter Name",lettersWithSpaces:"Name can contain only letters and spaces",minlength:"Name must be at least 2 characters",maxlength:"Name cannot exceed 150 characters"},
            age:{digits:"Age must contain digits only",min:"Age must be at least 1",max:"Age cannot exceed 99"},
            address:{minlength:"Address must be at least 5 characters",maxlength:"Address cannot exceed 250 characters"},
            sortorder:{required:"Enter Sort Order",digits:"Sort Order must contain digits only"},
            photo:{required:"Photo is required",accept:"Please upload JPG, JPEG or PNG image"},
            testimonial:{required:"Testimonial is required",minlength:"Minimum 10 characters required",maxlength:"Maximum 1000 characters allowed"},
            active:{required:"Please choose Status",digits:"Status must be numeric",range:"Invalid status selected"}
        },
        highlight:function(element){$(element).addClass('is-invalid');},
        unhighlight:function(element){$(element).removeClass('is-invalid');},
        errorPlacement:function(error,element){if(element.parent('.input-group').length){error.insertAfter(element.parent());}else{error.insertAfter(element);}}
    });
})();
</script>
@endpush
