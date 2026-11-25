@extends('backend.layouts.app')
@section('title', isset($video) ? 'Edit Video' : 'Create Video')
@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($video) ? 'Edit Video' : 'Create Video' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div><a href="{{ route('video.list') }}"
                    class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y"><i
                        class="bi bi-caret-left-fill"></i> Back</a>
            </div>
        </div>
        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12">
                <form id="video-form" action="{{ route('video.saveUpdate') }}" method="post" class="form-box"
                    enctype="multipart/form-data">@csrf
                    @if (isset($video))
                        <x-text-input name="id" type="hidden" value="{{ Crypt::encrypt($video->id) }}" />
                    @endif
                    <div class="row">
                        <div class="col-lg-4 mb-3"><x-text-input name="title" type="text"
                                value="{{ old('title', $video->title ?? '') }}" placeholder="Enter Video Title"
                                label="Video Title" icon="bi-card-text" autocomplete="off" :required="true" /></div>
                        <div class="col-lg-4 mb-3"><x-select-input name="video_type" icon="bi-camera-video"
                                label="Video Type" selected="{{ old('video_type', $video->video_type ?? 1) }}"
                                :options="['1' => 'YouTube Video', '2' => 'Upload MP4']" placeholder="Select Video Type" :required="true" /></div>
                        <div class="col-lg-4 mb-3 video-url"><x-text-input name="video_url" type="url"
                                value="{{ old('video_url', !empty($video->video_url) ? 'https://www.youtube.com/watch?v=' . $video->video_url : '') }}"
                                placeholder="Enter YouTube URL" label="YouTube URL" icon="bi-youtube" autocomplete="off"
                                :required="false" /></div>
                        <div class="col-lg-4 mb-3 upload-video">
                            <div class="d-flex align-items-end gap-2">
                                <div class="flex-grow-1"><x-text-input name="video" type="file" label="Upload Video"
                                        icon="bi-file-earmark-play" accept="video/mp4" :required="!isset($video)" /></div>
                                @if (isset($video) && $video->asset)
                                    <div class="pb-1"><a href="{{ asset($video->asset->url) }}" target="_blank"
                                            class="border-btn rounded-2 text-nowrap"><i class="bi bi-eye me-1"></i> View</a>
                                    </div>
                                @endif
                            </div><small class="text-muted">MP4 only. Maximum 10MB.</small>
                        </div>
                        <div class="col-lg-4 mb-3"><x-text-input name="date" type="date"
                                value="{{ old('date', isset($video->date) ? $video->date->format('Y-m-d') : '') }}"
                                label="Date" icon="bi-calendar" :required="false" /></div>
                        <div class="col-lg-4 mb-3"><x-text-input name="sortorder" type="number"
                                value="{{ old('sortorder', $video->sortorder ?? 0) }}" placeholder="Enter Sort Order"
                                label="Sort Order" icon="bi-sort-numeric-down" min="0" :required="true" /></div>
                        <div class="col-lg-4 mb-3"><x-select-input name="active" icon="bi-check2-circle"
                                label="Select Status" selected="{{ old('active', $video->active ?? 1) }}" :options="statusoptions()"
                                placeholder="Select Status" :required="true" /></div>
                        <div class="col-lg-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3"><button type="submit"
                                    class="cms-btn rounded-2">{{ isset($video) ? 'Update' : 'Submit' }}</button><button
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
            $("#video-form").validate({
                ignore: [],
                errorClass: 'text-danger',
                errorElement: 'small',
                rules: {
                    title: {
                        required: true,
                        minlength: 3,
                        maxlength: 255
                    },
                    video_type: {
                        required: true
                    },
                    video_url: {
                        required: function() {
                            return $('select[name="video_type"]').val() == '1'
                        },
                        url: true,
                        maxlength: 500
                    },
                    video: {
                        required: function() {
                            return $('select[name="video_type"]').val() == '2' && $('input[name="id"]')
                            .val() == ''
                        },
                        extension: "mp4"
                    },
                    date: {
                        date: true
                    },
                    sortorder: {
                        required: true,
                        digits: true,
                        min: 0
                    },
                    active: {
                        required: true,
                        digits: true,
                        range: [0, 1]
                    }
                },
                messages: {
                    title: {
                        required: "Enter Video Title"
                    },
                    video_type: {
                        required: "Please select Video Type"
                    },
                    video_url: {
                        required: "YouTube URL is required",
                        url: "Enter a valid URL"
                    },
                    video: {
                        required: "Please upload an MP4 video",
                        extension: "Only MP4 video is allowed"
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
            });

            function toggleVideoType() {
                let type = $('select[name="video_type"]').val();
                if (type == '1') {
                    $('.video-url').show();
                    $('.upload-video').hide()
                } else {
                    $('.video-url').hide();
                    $('.upload-video').show()
                }
            }
            toggleVideoType();
            $(document).on('change', 'select[name="video_type"]', toggleVideoType)
        })();
    </script>
@endpush
