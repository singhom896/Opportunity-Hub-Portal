@extends('backend.layouts.app')
@section('title', isset($gallery) ? 'Edit Gallery' : 'Create Gallery')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($gallery) ? 'Edit Gallery' : 'Create Gallery' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>
                <a href="{{ route('gallery.list') }}"
                    class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y"><i
                        class="bi bi-caret-left-fill"></i> Back</a>
            </div>
        </div>
        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12 mb-md-0 mb-3">
                <form id="gallery-form" action="{{ route('gallery.saveUpdate') }}" method="post" class="form-box"
                    enctype="multipart/form-data">
                    @csrf
                    @if (isset($gallery))
                        <x-text-input name="id" type="hidden" value="{{ Crypt::encrypt($gallery->id) }}" />
                    @endif
                    <div class="row">
                         <div class="col-lg-4 mb-3"><x-text-input name="type" type="text"
                                value="{{ old('type', $gallery->type ?? '') }}" placeholder="Enter Type" label="Type"
                                icon="bi-images" autocomplete="off" :required="false" /></div>
                        <div class="col-lg-4 mb-3"><x-text-input name="title" type="text"
                                value="{{ old('title', $gallery->title ?? '') }}" placeholder="Enter Title" label="Title"
                                icon="bi-card-text" autocomplete="off" :required="true" /></div>
                                  <div class="col-lg-4 mb-3">
                            <div class="d-flex align-items-end gap-2">
                                <div class="flex-grow-1"><x-text-input name="image" type="file" label="Image"
                                        icon="bi-image" accept="image/jpeg,image/jpg,image/png" :required="!isset($gallery)" /></div>
                                @if (isset($gallery) && $gallery->asset)
                                    <div class="pb-1"><a href="{{ asset($gallery->asset->url) }}" target="_blank"
                                            class="border-btn rounded-2 text-nowrap"><i class="bi bi-eye me-1"></i> View</a>
                                    </div>
                                @endif
                            </div>
                            <small class="text-muted">JPG, JPEG, PNG only. Maximum 2MB.</small>
                        </div>

                        <div class="col-lg-4 mb-3"><x-text-input name="date" type="date"
                                value="{{ old('date', isset($gallery->date) ? $gallery->date->format('Y-m-d') : '') }}"
                                label="Date" icon="bi-calendar" :required="false" /></div>
                        <div class="col-lg-4 mb-3"><x-text-input name="sortorder" type="number"
                                value="{{ old('sortorder', $gallery->sortorder ?? 0) }}" placeholder="Enter Sort Order"
                                label="Sort Order" icon="bi-sort-numeric-down" min="0" :required="true" /></div>

                        <div class="col-lg-4 mb-3"><x-select-input name="active" icon="bi-check2-circle"
                                label="Select Status" selected="{{ old('active', $gallery->active ?? 1) }}" :options="statusoptions()"
                                placeholder="Select Status" :required="true" /></div>
                        <div class="col-lg-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3"><button type="submit"
                                    class="cms-btn rounded-2">{{ isset($gallery) ? 'Update' : 'Submit' }}</button><button
                                    type="reset" class="border-btn rounded-2">Reset</button></div>
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
            $("#gallery-form").validate({
                ignore: [],
                errorClass: 'text-danger',
                errorElement: 'small',
                rules: {
                    title: {
                        required: true,
                        minlength: 3,
                        maxlength: 255
                    },
                    type: {
                        maxlength: 50
                    },
                    date: {
                        date: true
                    },
                    sortorder: {
                        required: true,
                        digits: true,
                        min: 0
                    },
                    image: {
                        required: {{ isset($gallery) ? 'false' : 'true' }},
                        accept: "image/jpg,image/jpeg,image/png"
                    },
                    active: {
                        required: true,
                        digits: true,
                        range: [0, 1]
                    }
                },
                messages: {
                    title: {
                        required: "Enter Title",
                        minlength: "Title must be at least 3 characters",
                        maxlength: "Title cannot exceed 255 characters"
                    },
                    image: {
                        required: "Image is required",
                        accept: "Please upload JPG, JPEG or PNG image"
                    },
                    sortorder: {
                        required: "Enter Sort Order",
                        digits: "Sort Order must contain digits only"
                    },
                    active: {
                        required: "Please choose Status"
                    }
                },
                highlight: function(e) {
                    $(e).addClass('is-invalid')
                },
                unhighlight: function(e) {
                    $(e).removeClass('is-invalid')
                },
                errorPlacement: function(e, el) {
                    if (el.parent('.input-group').length) e.insertAfter(el.parent());
                    else e.insertAfter(el)
                }
            })
        })();
    </script>
@endpush
