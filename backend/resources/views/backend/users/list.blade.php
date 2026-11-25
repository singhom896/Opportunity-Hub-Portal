@extends('backend.layouts.app')
@section('title', 'Users Details')
@section('content')
    <div class="container-fluid">

        <div class="row mb-0">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Users Management</h1>
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

        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-light border rounded-3 shadow-sm p-3">

                    <div class="row align-items-end justify-content-center">

                        <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                            <label for="role_id" class="form-label fw-bold mb-2">
                                Select Role
                            </label>

                            <select class="form-select rounded-1" id="role_id">
                                <option value="">Select Role</option>

                                @foreach ($roles as $role)
                                    <option value="{{ route('users.role', ['role_id' => encrypt($role->role_id)]) }}"
                                        {{ ($selectedRole ?? '') == $role->role_id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="searchPermission" class="theme-btn rounded-2 py-2 px-4">
                                    Search
                                </button>

                                <a href="{{ route('users.list') }}" class="border-btn rounded-2 py-2 px-4">
                                    Reset
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('users.create') }}" class="theme-btn rounded-2"><i class="bi bi-plus-lg me-1"></i> Create
            </a>
        </div>


        <div class="row py-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-data">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Role</th>
								<th scope="col">Country</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Mobile</th>
                                
                                <th scope="col">Created Date</th>
                                <th scope="col">Is Active</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <th scope="row" class="text-end">{{ $index + 1 }}</th>
                                     <td>{{ $user->role->name ?? '-' }}</td>
									<td>{{ $user->country_name ?? '—' }}</td>
                                    <td>{{ $user->full_name ?? 'NA' }}</td>

                                    <td>{{ $user->email ?? '—' }}</td>

                                    <td>{{ $user->mobile ?? '—' }}</td>
                                  
                                    <td>{{ $user->created_at->format('d-M-Y') }}</td>
                                    <td class="text-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-change pointer" role="button"
                                                type="checkbox" data-id="{{ $user->id }}"
                                                data-active="{{ $user->active }}" {{ $user->active ? 'checked' : '' }}>
                                        </div>
                                    </td>

                                    <td>
                                        <ul class="d-flex align-items-center justify-content-center gap-3">
                                            <li>
                                                <a href="{{ route('users.edit', encrypt($user->id)) }}" class="fs-5 edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
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
                        'Do you want to deactivate this User?' : 'Do you want to activate this User?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('users.status') }}",
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
                            //console.warn(err.responseJSON);
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

        })();

        $('#searchPermission').on('click', function() {

            let url = $('#role_id').val();

            if (url) {
                window.location.href = url;
            }

        });
    </script>
@endpush
