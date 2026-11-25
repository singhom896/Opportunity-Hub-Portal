@extends('backend.layouts.app')

@section('title', 'Event Preview')

@section('content')


<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col-12 position-relative text-center">
            <div class="d-inline-block">
                <h1 class="mb-1 fw-bold">Event Preview</h1>
                <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
            </div>
            <a href="{{ route('event.list') }}" class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y"><i class="bi bi-caret-left-fill"></i> Back</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

    <div class="card mb-4">
        <div class="card-header fw-bold"><i class="bi bi-calendar-event me-1"></i> Event Details</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Event Title</small>
                        <strong>{{ $event->title ?: '-' }}</strong>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Venue</small>
                        {{ $event->venue ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Country</small>
                        {{ $country ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Region / State / Province</small>
                        {{ $region ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">City / Town</small>
                        {{ $event->city ?: '-' }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Event Start Date</small>
                        {{ $event->event_date ? $event->event_date->format('d-m-Y') : '-' }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Event Start Time</small>
                        {{ $event->event_time ?: '-' }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Event End Date</small>
                        {{ $event->event_end_date ? \Illuminate\Support\Carbon::parse($event->event_end_date)->format('d-m-Y') : '-' }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Event End Time</small>
                        {{ $event->event_end_time ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge {{ $event->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $event->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Organised By</small>
                        {{ $event->organised_by ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Mobile</small>
                        {{ $event->mobile ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Other Mobile</small>
                        {{ $event->other_mobile ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Email</small>
                        {{ $event->email ?: '-' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Postal / ZIP Code</small>
                        {{ $event->pincode ?: '-' }}
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Address</small>
                        {{ $event->address ?: '-' }}
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3">
                        <small class="text-muted d-block mb-2">Event Description</small>
                        <div class="event-description ck-content">
                            {!! $event->event_description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-6"><h3 class="mb-1 fw-bold">Event Media</h3><div class="border-bottom border-3 border-primary rounded-pill" style="width:120px"></div></div>
        <div class="col-md-6 text-end">
            @permission('edit', 'event')
                <button type="button" class="cms-btn rounded-2" data-bs-toggle="modal" data-bs-target="#uploadMediaModal"><i class="bi bi-cloud-upload me-1"></i> Upload Media</button>
            @endpermission
        </div>
    </div>

    <div class="row g-3">
        @forelse($event->media as $media)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        @if($media->media_type === 'photo' && $media->asset)
                            <img src="{{ asset($media->asset->url) }}" alt="{{ $media->caption }}" class="img-fluid rounded w-100" style="height:190px;object-fit:cover">
                        @elseif($media->media_type === 'video' && $media->asset)
                            <video controls class="w-100 rounded" style="height:190px"><source src="{{ asset($media->asset->url) }}" type="video/mp4"></video>
                        @elseif($media->media_type === 'youtube')
                            <div class="ratio ratio-16x9"><iframe src="https://www.youtube-nocookie.com/embed/{{ $media->video_url }}" title="{{ $media->caption }}" allowfullscreen loading="lazy"></iframe></div>
                        @endif
                        <p class="mt-3 mb-3">{{ $media->caption }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            @permission('edit', 'event')
                                <div class="form-check form-switch"><input class="form-check-input media-status" type="checkbox" data-id="{{ $media->id }}" {{ $media->is_active ? 'checked' : '' }} aria-label="Toggle media status"></div>
                            @else
                                <span class="badge {{ $media->is_active ? 'bg-success' : 'bg-danger' }}">{{ $media->is_active ? 'Active' : 'Inactive' }}</span>
                            @endpermission
                            @permission('delete', 'event')
                                <button type="button" class="btn btn-sm btn-outline-danger delete-media" data-id="{{ $media->id }}"><i class="bi bi-trash"></i></button>
                            @endpermission
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card"><div class="card-body text-center py-4">No media uploaded for this event.</div></div></div>
        @endforelse
    </div>
</div>

@permission('edit', 'event')
<div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-labelledby="uploadMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('event.media.upload') }}" method="POST" enctype="multipart/form-data" id="media-upload-form">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <div class="modal-header"><h5 class="modal-title fw-bold" id="uploadMediaModalLabel">Upload Event Media</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-collection me-1"></i> Media Type <span class="text-danger">*</span></label>
                            <select name="media_type" id="media_type" class="form-select" required><option value="photo" {{ old('media_type', 'photo') === 'photo' ? 'selected' : '' }}>Photos (JPG/PNG, max 2MB each)</option><option value="video" {{ old('media_type') === 'video' ? 'selected' : '' }}>Videos (MP4, max 10MB each)</option><option value="youtube" {{ old('media_type') === 'youtube' ? 'selected' : '' }}>YouTube URL</option></select>
                            @error('media_type')<small class="text-danger d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-chat-left-text me-1"></i> Caption <span class="text-danger">*</span></label>
                            <input type="text" name="caption" class="form-control" minlength="3" maxlength="250" value="{{ old('caption') }}" placeholder="Enter caption" required>
                            @error('caption')<small class="text-danger d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-12 mb-3 media-field" id="photos-field"><label class="form-label">Select Photos (up to 50 total)</label><input type="file" name="photos[]" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" multiple><small class="text-muted">Each image maximum 2MB.</small>@error('photos')<small class="text-danger d-block">{{ $message }}</small>@enderror</div>
                        <div class="col-12 mb-3 media-field d-none" id="videos-field"><label class="form-label">Select MP4 Videos (up to 6 total)</label><input type="file" name="videos[]" class="form-control" accept=".mp4,video/mp4" multiple><small class="text-muted">Each video maximum 10MB.</small>@error('videos')<small class="text-danger d-block">{{ $message }}</small>@enderror</div>
                        <div class="col-12 mb-3 media-field d-none" id="youtube-field"><label class="form-label">YouTube URL</label><input type="url" name="youtube_url" class="form-control" value="{{ old('youtube_url') }}" placeholder="https://www.youtube.com/watch?v=...">@error('youtube_url')<small class="text-danger d-block">{{ $message }}</small>@enderror</div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary rounded-2" data-bs-dismiss="modal">Cancel</button><button type="submit" class="cms-btn rounded-2"><i class="bi bi-upload me-1"></i> Upload</button></div>
            </form>
        </div>
    </div>
</div>
@if($errors->hasAny(['media_type', 'caption', 'photos', 'videos', 'youtube_url']))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('uploadMediaModal');
        if (modal && window.bootstrap) bootstrap.Modal.getOrCreateInstance(modal).show();
    });
</script>
@endif
@endpermission

@push('styles')
<style>
    .event-description {
        overflow-wrap: anywhere;
    }
    .event-description img {
        max-width: 100%;
        height: auto;
    }
    .event-description table {
        width: 100%;
        border-collapse: collapse;
    }
    .event-description table td,
    .event-description table th {
        border: 1px solid #dee2e6;
        padding: .5rem;
    }
</style>
@endpush

@endsection

@push('scripts')
<script>
$(function () {
    function toggleMediaFields() {
        const type = $('#media_type').val();
        $('.media-field').addClass('d-none').find('input').prop('disabled', true).prop('required', false);
        const field = type === 'photo' ? '#photos-field' : (type === 'video' ? '#videos-field' : '#youtube-field');
        $(field).removeClass('d-none').find('input').prop('disabled', false).prop('required', true);
    }
    $('#media_type').on('change', toggleMediaFields);
    toggleMediaFields();

    $('.media-status').on('change', function () {
        const el = $(this), active = el.is(':checked') ? 1 : 0;
        const update = function () {
            $.post("{{ route('event.media.status') }}", {_token:'{{ csrf_token() }}', id:el.data('id'), active:active})
                .fail(function(){el.prop('checked', !active); alert('Unable to update media status.');});
        };
        if (window.Swal) Swal.fire({title:'Change media status?', icon:'question', showCancelButton:true, confirmButtonText:'Yes, update'}).then(function(r){if(r.isConfirmed)update();else el.prop('checked', !active);});
        else if(confirm('Change media status?')) update(); else el.prop('checked', !active);
    });

    $('.delete-media').on('click', function () {
        const id = $(this).data('id'), card = $(this).closest('.col-xl-3');
        const remove = function () {
            $.post("{{ route('event.media.delete') }}", {_token:'{{ csrf_token() }}', id:id})
                .done(function(res){if(res.success){card.remove(); if(window.Swal)Swal.fire({icon:'success',title:'Deleted',timer:1000,showConfirmButton:false});}else alert(res.message || 'Unable to delete media.');})
                .fail(function(){alert('Unable to delete media.');});
        };
        if (window.Swal) Swal.fire({title:'Delete media?', text:'This file will be permanently deleted.', icon:'warning', showCancelButton:true, confirmButtonText:'Delete'}).then(function(r){if(r.isConfirmed)remove();});
        else if(confirm('Delete this media?')) remove();
    });
});
</script>
@endpush
