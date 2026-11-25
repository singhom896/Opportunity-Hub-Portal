@extends('backend.layouts.app')

@section('title', 'Event Management')

@section('content')
    <div class="container-fluid">
       
        
           <div class="row mb-0">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Event Management</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>
            </div>
        </div>


        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @permission('create', 'event')
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('event.create') }}" class="theme-btn rounded-2"><i class="bi bi-plus-lg me-1"></i> Create
                Event</a>
         </div>
        @endpermission


        <div class="row py-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="eventTable" class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th width="5%">S.No.</th>
                                <th>Country</th>
                                <th>Title</th>
                                <th>Venue</th>
                                <th>Date &amp; Time</th>
                                <th width="5%">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                @php
                                    $eventRouteId = rtrim(strtr(encrypt((string) $event->id), '+/', '-_'), '=');
                                    $eventEnded = !empty($event->event_end_date)
                                        && !empty($event->event_end_time)
                                        && \Illuminate\Support\Carbon::parse(\Illuminate\Support\Carbon::parse($event->event_end_date)->toDateString() . ' ' . $event->event_end_time)->lte(now());
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
									<td>{{ $event->country->name ?? '-' }}</td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->venue }}</td>
                                    <td>{{ $event->event_date?->format('d-m-Y') }} {{ $event->event_time }}</td>
                                 
                                    <td>
                                        @permission('edit', 'event')
                                            <div class="form-check form-switch"><input type="checkbox"
                                                    class="form-check-input event-status" data-id="{{ $event->id }}"
                                                    {{ $event->is_active ? 'checked' : '' }}
                                                    title="{{ $event->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                            </div>
                                        @else
                                            <span
                                                class="badge {{ $event->is_active ? 'bg-success' : 'bg-danger' }}">{{ $event->is_active ? 'Active' : 'Inactive' }}</span>
                                        @endpermission
                                    </td>
                                    <td class="text-nowrap">
                                        @permission('view', 'event')
                                            <a href="{{ route('event.preview', $eventRouteId) }}"
                                                class="btn btn-sm btn-outline-info" title="Preview"><i
                                                    class="bi bi-eye"></i></a>
                                        @endpermission
                                        @permission('edit', 'event')
                                            @if (!$eventEnded)
                                                <a href="{{ route('event.edit', $eventRouteId) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit"><i
                                                        class="bi bi-pencil-square"></i></a>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    title="Event has ended; editing is disabled" disabled>
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            @endif
                                        @endpermission
                                        @permission('delete', 'event')
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-event"
                                                data-id="{{ $event->id }}" title="Delete"><i
                                                    class="bi bi-trash"></i></button>
                                        @endpermission
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No events found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#eventTable')) {
                $('#eventTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    columnDefs: [{
                        orderable: false,
                        targets: [5, 6]
                    }]
                });
            }

            $('#eventTable').on('change', '.event-status', function() {
                const checkbox = $(this),
                    active = checkbox.is(':checked') ? 1 : 0;
                const updateStatus = function() {
                    $.ajax({
                        url: "{{ route('event.status') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: checkbox.data('id'),
                            active: active
                        },
                        success: function(res) {
                            if (res.success) {
                                if (window.Swal) Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: res.message,
                                    timer: 1200,
                                    showConfirmButton: false
                                });
                            } else {
                                checkbox.prop('checked', !active);
                                alert(res.message || 'Unable to update status.');
                            }
                        },
                        error: function() {
                            checkbox.prop('checked', !active);
                            alert('Unable to update event status.');
                        }
                    });
                };
                if (window.Swal) {
                    Swal.fire({
                        title: 'Change event status?',
                        text: active ? 'Are you sure you want to activate this event?' :
                            'Are you sure you want to deactivate this event?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update'
                    }).then(function(result) {
                        if (result.isConfirmed) updateStatus();
                        else checkbox.prop('checked', !active);
                    });
                } else if (confirm(active ? 'Activate this event?' : 'Deactivate this event?'))
                    updateStatus();
                else checkbox.prop('checked', !active);
            });

            $('#eventTable').on('click', '.delete-event', function() {
                const id = $(this).data('id');
                const remove = function() {
                    $.ajax({
                        url: "{{ route('event.delete') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(res) {
                            if (res.success) {
                                if (window.Swal) Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: res.message,
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(function() {
                                    location.reload();
                                });
                                else location.reload();
                            } else alert(res.message || 'Unable to delete event.');
                        },
                        error: function() {
                            alert('Unable to delete event.');
                        }
                    });
                };
                if (window.Swal) Swal.fire({
                    title: 'Delete event?',
                    text: 'This event will be moved to deleted status.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete'
                }).then(function(r) {
                    if (r.isConfirmed) remove();
                });
                else if (confirm('Delete this event?')) remove();
            });
        });
    </script>
@endpush
