@extends('backend.layouts.app')
@section('title', 'Documents')
@section('content')
    <div class="container-fluid">

        <div class="row mb-0">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Document Management</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>
            </div>
        </div>


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('documents.create') }}" class="theme-btn rounded-2"><i class="bi bi-plus-lg me-1"></i> Create
            </a>
        </div>


        <div class="row py-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-data">

                        <thead>
                            <tr class="align-middle">
                                <th scope="col" class="text-center text-nowrap">S.No</th>
                                <th scope="col" class="text-nowrap">Type</th>
                                <th scope="col" class="text-nowrap">Document Type</th>
                                <th scope="col" class="text-start">Title</th>
                                <th scope="col" class="text-center text-nowrap">Publish Date</th>
                                <th scope="col" class="text-center text-nowrap">Size</th>
                                <th scope="col" class="text-center text-nowrap">Created At</th>
                                <th scope="col" class="text-center text-nowrap">Is Active</th>
                                <th scope="col" class="text-center text-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $index => $document)
                                <tr>
                                    <th scope="row" class="text-center">{{ $index + 1 }}</th>
                                    <td>{{ $document->menuItem->title ?? '—' }}</td>
                                    <td>{{ (int) $document->document_type === 1 ? 'PDF Upload' : ((int) $document->document_type === 2 ? 'External URL' : '—') }}</td>
                                    <td>{{ $document->title ?? '—' }}</td>
                                    <td>
                                        <small>
                                            <strong>Start:</strong>
                                            {{ \Carbon\Carbon::parse($document->publish_start_date)->format('d M Y') }}<br>
                                            <strong>End:</strong>
                                            {{ \Carbon\Carbon::parse($document->publish_end_date)->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td align="center">
                                        @if ((int) $document->document_type === 1)
                                            @if (optional($document->asset)->url)
                                                <div class="btn-group" role="group" aria-label="Document actions">
                                                    <a target="_blank" href="{{ asset($document->asset->url) }}"
                                                        class="bg-danger text-white rounded-circle p-1 d-flex align-items-center viewReport tooltip-danger"
                                                        data-bs-toggle="tooltip" title="View PDF" draggable="false">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                    </a>
                                                </div>
                                                <small class="fs-7 d-block text-success">{{ $document->asset->size ? formatBytes($document->asset->size) : '—' }}</small>
                                            @else
                                                —
                                            @endif
                                        @elseif ((int) $document->document_type === 2)
                                            @if ($document->document_url)
                                                <a href="{{ $document->document_url }}" target="_blank" rel="noopener noreferrer"
                                                    class="btn btn-sm btn-outline-primary" title="Open URL">
                                                    <i class="bi bi-box-arrow-up-right"></i> View
                                                </a>
                                            @else
                                                —
                                            @endif
                                            <!--small class="fs-7 d-block text-muted">External URL</small-->
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>
                                        {{ optional($document->created_at)->format('d-M-Y, h:i A') }}
                                    </td>

                                    <td class="text-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-change pointer" role="button"
                                                type="checkbox" data-id="{{ $document->id }}"
                                                data-active="{{ $document->active }}"
                                                {{ $document->active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <ul class="d-flex align-items-center justify-content-center gap-3">
                                            <li>
                                                <a href="{{ route('documents.edit', encrypt($document->id)) }}"
                                                    class="fs-5 edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <button type="submit" class="btn p-0 fs-5 delete delete-btn"
                                                    data-id="{{ $document->id }}">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </li>
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
            $(document).on('click', '.status-change', function(e) {
                e.preventDefault();
                let checkbox = $(this);
                let id = checkbox.data('id');
                let active = checkbox.data('active');
                Swal.fire({
                    title: 'Change status?',
                    text: active == 1 ?
                        'Do you want to deactivate this item?' : 'Do you want to activate this item?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('documents.status') }}",
                        type: "POST",
                        data: {
                            id: id,
                            status: active == 1 ? 0 : 1
                        },
                        beforeSend: function() {
                            checkbox.prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                checkbox.data('active', response.status);
                                checkbox.prop('checked', response.status == 1);
                                Swal.fire(
                                    'Updated!',
                                    response.message,
                                    'success'
                                );
                            }
                        },
                        error: function(err) {
                            let errorMessage = 'Something went wrong!';
                            console.warn(err.responseJSON);
                            if (err.responseJSON && err.responseJSON.message) {
                                errorMessage = err.responseJSON.message;
                            }
                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );
                        },
                        complete: function() {
                            checkbox.prop('disabled', false);
                        }
                    });
                });
            });

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                let button = $(this);
                let id = button.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('documents.delete') }}",
                            type: "POST",
                            data: {
                                id: id,
                            },
                            beforeSend: function() {
                                button.prop('disabled', true);
                            },
                            success: function(response) {
                                if (response.success) {
                                    button.closest('tr').remove();
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(err) {
                                let errorMessage = 'Something went wrong!';
                                if (err.responseJSON && err.responseJSON.message) {
                                    errorMessage = err.responseJSON.message;
                                }
                                Swal.fire(
                                    'Error!',
                                    errorMessage,
                                    'error'
                                );
                            },
                            complete: function() {
                                button.prop('disabled', false);
                            }
                        });
                    }
                });
            });

        })();
    </script>
@endpush
