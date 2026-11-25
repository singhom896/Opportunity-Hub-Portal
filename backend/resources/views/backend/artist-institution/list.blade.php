@extends('backend.layouts.app')
@section('title', 'Artist Institution Management')
@section('content')
    <div class="container-fluid">

        <div class="row mb-0">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Artist Institution Management</h1>
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

        @if (($authRoleId ?? null) !== 8)
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-light border rounded-3 shadow-sm p-3">
                    <form action="{{ route('cp.artist-institution.countrySearch') }}" method="POST">
                        @csrf
                        <div class="row align-items-end justify-content-center">
                            <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                                <label for="country_filter" class="form-label fw-bold mb-2"> Country</label>
                                <select class="form-select rounded-1" id="country_filter" name="country_id">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ (string)($selectedCountry ?? '') === (string)$id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" class="theme-btn rounded-2 py-2 px-4">Search</button>
                                    <a href="{{ route('cp.artist-institution.list') }}" class="border-btn rounded-2 py-2 px-4">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="d-flex justify-content-end gap-2">
            @permission('create', 'artist-institution')
            <a href="{{ route('cp.artist-institution.create') }}" class="theme-btn rounded-2">
                <i class="bi bi-plus-lg me-1"></i> Create
            </a>
            @endpermission
        </div>

        <div class="row py-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-data">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Country</th>
                                <th scope="col">Institution</th>
                                <th scope="col">Description</th>
                                <th scope="col">Is Active</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($institutionTypes as $index => $institutionType)
                                <tr>
                                    <th scope="row" class="text-end">{{ $index + 1 }}</th>
                                    <td>{{ $institutionType->country_name ?? '-' }}</td>
                                    <td>{{ $institutionType->name ?? '-' }}</td>
                                    <td>{{ $institutionType->description ?? '-' }}</td>
                                    <td class="text-center">
                                        @permission('edit', 'artist-institution')
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-change pointer" role="button"
                                                    type="checkbox" data-id="{{ $institutionType->id }}"
                                                    data-status="{{ $institutionType->status }}"
                                                    {{ $institutionType->status ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <span class="badge {{ $institutionType->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $institutionType->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endpermission
                                    </td>
                                    <td>
                                        <ul class="d-flex align-items-center justify-content-center gap-3">
                                            @permission('edit', 'artist-institution')
                                                <li>
                                                    <a href="{{ route('cp.artist-institution.edit', encrypt($institutionType->id)) }}" class="fs-5 edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                </li>
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
            let table = new DataTable('.table-data', {
                responsive: true,
                pageLength: 10,
                language: {
                    emptyTable: 'No institution types found.',
                    zeroRecords: 'No matching institution types found.'
                },
                columnDefs: [{ orderable: false, targets: [4, 5] }]
            });

            $(document).on('click', '.status-change', function(e) {
                e.preventDefault();
                let checkbox = $(this);
                let id = checkbox.data('id');
                let status = Number(checkbox.data('status'));

                Swal.fire({
                    title: 'Change status?',
                    text: status === 1
                        ? 'Do you want to deactivate this Institution Type?'
                        : 'Do you want to activate this Institution Type?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: @json(route('cp.artist-institution.status')),
                        type: 'POST',
                        data: {
                            _token: @json(csrf_token()),
                            id: id,
                            status: status === 1 ? 0 : 1
                        },
                        beforeSend: function() {
                            checkbox.prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.success) {
                                checkbox.data('status', response.status);
                                checkbox.prop('checked', response.status == 1);
                                Swal.fire('Updated!', response.message, 'success');
                            } else {
                                checkbox.prop('checked', status == 1);
                            }
                        },
                        error: function(err) {
                            checkbox.prop('checked', status == 1);
                            Swal.fire('Error!', err.responseJSON?.message || 'Something went wrong!', 'error');
                        },
                        complete: function() {
                            checkbox.prop('disabled', false);
                        }
                    });
                });
            });
        })();
    </script>
@endpush
