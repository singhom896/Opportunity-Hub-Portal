@extends('backend.layouts.app')
@section('title', 'Permissions')
@section('content')

    <div class="container-fluid">



        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Role Permissions</h1>
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
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                                    <option value="{{ route('permissions.role', ['role_id' => encrypt($role->role_id)]) }}"
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

                                <a href="{{ route('permissions.list') }}" class="border-btn rounded-2 py-2 px-4">
                                    Reset
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        @if ($selectedRole)

            <div class="row py-2 align-items-center">
                <div class="col-12 text-end">
                    <span class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        {{ $permissions->count() }} Modules
                    </span>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-2">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-bordered table-data text-center align-middle mb-0">

                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Module</th>
                                    <th scope="col">Role</th>

                                    <th scope="col">
                                        View
                                        <input type="checkbox" class="form-check-input permission-all pointer ms-1"
                                            data-permission="can_view" data-status="0">
                                    </th>

                                    <th scope="col">
                                        Create
                                        <input type="checkbox" class="form-check-input permission-all pointer ms-1"
                                            data-permission="can_create" data-status="0">
                                    </th>

                                    <th scope="col">
                                        Edit
                                        <input type="checkbox" class="form-check-input permission-all pointer ms-1"
                                            data-permission="can_edit" data-status="0">
                                    </th>

                                    <th scope="col">
                                        Delete
                                        <input type="checkbox" class="form-check-input permission-all pointer ms-1"
                                            data-permission="can_delete" data-status="0">
                                    </th>

                                    <th scope="col">All</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($permissions as $index => $permission)
                                    @php
                                        $allPermission =
                                            ($permission->can_view ?? 0) &&
                                            ($permission->can_create ?? 0) &&
                                            ($permission->can_edit ?? 0) &&
                                            ($permission->can_delete ?? 0);
                                    @endphp

                                    <tr>

                                        <th scope="row">
                                            {{ $index + 1 }}
                                        </th>

                                        <td class="text-start">
                                            {{ $permission->module_name }}
                                        </td>

                                        <td>
                                            {{ $roles->firstWhere('role_id', $selectedRole)->name ?? '—' }}
                                        </td>

                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input status-change pointer" type="checkbox"
                                                    role="button" data-id="{{ $permission->permission_id }}"
                                                    data-module-id="{{ $permission->mid }}"
                                                    data-role-id="{{ $selectedRole }}" data-permission="can_view"
                                                    data-status="{{ $permission->can_view ?? 0 }}"
                                                    {{ $permission->can_view ?? 0 ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input status-change pointer" type="checkbox"
                                                    role="button" data-id="{{ $permission->permission_id }}"
                                                    data-module-id="{{ $permission->mid }}"
                                                    data-role-id="{{ $selectedRole }}" data-permission="can_create"
                                                    data-status="{{ $permission->can_create ?? 0 }}"
                                                    {{ $permission->can_create ?? 0 ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input status-change pointer" type="checkbox"
                                                    role="button" data-id="{{ $permission->permission_id }}"
                                                    data-module-id="{{ $permission->mid }}"
                                                    data-role-id="{{ $selectedRole }}" data-permission="can_edit"
                                                    data-status="{{ $permission->can_edit ?? 0 }}"
                                                    {{ $permission->can_edit ?? 0 ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input status-change pointer" type="checkbox"
                                                    role="button" data-id="{{ $permission->permission_id }}"
                                                    data-module-id="{{ $permission->mid }}"
                                                    data-role-id="{{ $selectedRole }}" data-permission="can_delete"
                                                    data-status="{{ $permission->can_delete ?? 0 }}"
                                                    {{ $permission->can_delete ?? 0 ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input all-permission pointer" type="checkbox"
                                                    role="button" data-id="{{ $permission->permission_id }}"
                                                    data-module-id="{{ $permission->mid }}"
                                                    data-role-id="{{ $selectedRole }}" data-permission="all"
                                                    data-status="{{ $allPermission ? 1 : 0 }}"
                                                    {{ $allPermission ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded-2">
                <div class="card-body text-center py-5">
                    <i class="bi bi-shield-lock fs-2 text-secondary"></i>

                    <p class="mb-1 mt-2 fw-semibold">
                        Select a Role
                    </p>

                    <p class="text-muted mb-0">
                        Please select a role to manage permissions.
                    </p>
                </div>
            </div>

        @endif

    </div>

@endsection

@push('scripts')
    <script>
        (() => {

            /*
             * DataTable
             */
            let table = new DataTable('.table-data', {
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All']
                ],
                columnDefs: [{
                    targets: [2, 3, 4, 5, 6, 7],
                    orderable: false
                }]
            });


            /*
             * Role Search
             */
            $('#searchPermission').on('click', function() {

                let url = $('#role_id').val();

                if (url) {
                    window.location.href = url;
                }

            });


            /*
             * Update row All checkbox
             */
            function updateAllCheckbox(moduleId) {

                let permissions = $('.status-change[data-module-id="' + moduleId + '"]');

                let allChecked =
                    permissions.length === 4 &&
                    permissions.filter(':checked').length === 4;

                let allCheckbox =
                    $('.all-permission[data-module-id="' + moduleId + '"]');

                allCheckbox
                    .prop('checked', allChecked)
                    .attr('data-status', allChecked ? 1 : 0)
                    .data('status', allChecked ? 1 : 0);
            }


            /*
             * Update header All checkbox
             */
            function updateHeaderCheckbox(permission) {

                let checkboxes =
                    $('.status-change[data-permission="' + permission + '"]');

                if (!checkboxes.length) {
                    return;
                }

                let allChecked =
                    checkboxes.filter(':checked').length === checkboxes.length;

                let headerCheckbox =
                    $('.permission-all[data-permission="' + permission + '"]');

                headerCheckbox
                    .prop('checked', allChecked)
                    .attr('data-status', allChecked ? 1 : 0)
                    .data('status', allChecked ? 1 : 0);
            }


            /*
             * Single Permission
             */
            $(document).on('click', '.status-change', function(e) {

                e.preventDefault();

                let checkbox = $(this);

                let dataAttrs = checkbox.data();

                let oldStatus = Number(dataAttrs.status);

                let newStatus = oldStatus === 1 ? 0 : 1;


                Swal.fire({
                    title: 'Change Permission?',
                    text: newStatus === 1 ?
                        'Do you want to grant this permission?' :
                        'Do you want to revoke this permission?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {

                    if (!result.isConfirmed) {

                        checkbox.prop('checked', oldStatus === 1);

                        return;
                    }


                    $.ajax({

                        url: "{{ route('permissions.status') }}",

                        type: "POST",

                        data: {

                            _token: "{{ csrf_token() }}",

                            id: dataAttrs.id || '',

                            module_id: dataAttrs.moduleId,

                            role_id: dataAttrs.roleId,

                            permission: dataAttrs.permission,

                            status: newStatus

                        },

                        beforeSend: function() {

                            checkbox.prop('disabled', true);

                        },

                        success: function(response) {

                            if (response.success) {

                                checkbox
                                    .prop('checked', newStatus === 1)
                                    .attr('data-status', newStatus)
                                    .data('status', newStatus);


                                if (response.permission_id) {

                                    checkbox
                                        .attr('data-id', response.permission_id)
                                        .data('id', response.permission_id);

                                }


                                updateAllCheckbox(dataAttrs.moduleId);

                                updateHeaderCheckbox(dataAttrs.permission);


                                Swal.fire({

                                    title: 'Updated!',

                                    text: response.message,

                                    icon: 'success',

                                    timer: 1000,

                                    showConfirmButton: false

                                });

                            }

                        },

                        error: function(err) {

                            checkbox.prop('checked', oldStatus === 1);

                            let errorMessage =
                                err.responseJSON &&
                                err.responseJSON.message ?
                                err.responseJSON.message :
                                'Something went wrong!';

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


            /*
             * Single Module - All Permissions
             */
            $(document).on('click', '.all-permission', function(e) {

                e.preventDefault();

                let checkbox = $(this);

                let dataAttrs = checkbox.data();

                let oldStatus = Number(dataAttrs.status);

                let newStatus = oldStatus === 1 ? 0 : 1;


                Swal.fire({

                    title: 'Change All Permissions?',

                    text: newStatus === 1 ?
                        'Do you want to grant all permissions for this module?' :
                        'Do you want to revoke all permissions for this module?',

                    icon: 'question',

                    showCancelButton: true,

                    confirmButtonText: 'Yes, update',

                    cancelButtonText: 'Cancel'

                }).then((result) => {

                    if (!result.isConfirmed) {

                        checkbox.prop('checked', oldStatus === 1);

                        return;
                    }


                    $.ajax({

                        url: "{{ route('permissions.status') }}",

                        type: "POST",

                        data: {

                            _token: "{{ csrf_token() }}",

                            id: dataAttrs.id || '',

                            module_id: dataAttrs.moduleId,

                            role_id: dataAttrs.roleId,

                            permission: 'all',

                            status: newStatus

                        },

                        beforeSend: function() {

                            checkbox.prop('disabled', true);

                            $('.status-change[data-module-id="' + dataAttrs.moduleId + '"]')
                                .prop('disabled', true);

                        },

                        success: function(response) {

                            if (response.success) {

                                $('.status-change[data-module-id="' + dataAttrs.moduleId +
                                        '"]')
                                    .each(function() {

                                        $(this)
                                            .prop('checked', newStatus === 1)
                                            .attr('data-status', newStatus)
                                            .data('status', newStatus);


                                        if (response.permission_id) {

                                            $(this)
                                                .attr('data-id', response.permission_id)
                                                .data('id', response.permission_id);

                                        }

                                    });


                                checkbox
                                    .prop('checked', newStatus === 1)
                                    .attr('data-status', newStatus)
                                    .data('status', newStatus);


                                updateHeaderCheckbox('can_view');
                                updateHeaderCheckbox('can_create');
                                updateHeaderCheckbox('can_edit');
                                updateHeaderCheckbox('can_delete');


                                Swal.fire({

                                    title: 'Updated!',

                                    text: newStatus === 1 ?
                                        'All permissions activated for this module.' :
                                        'All permissions deactivated for this module.',

                                    icon: 'success',

                                    timer: 1000,

                                    showConfirmButton: false

                                });

                            }

                        },

                        error: function(err) {

                            checkbox.prop('checked', oldStatus === 1);

                            $('.status-change[data-module-id="' + dataAttrs.moduleId + '"]')
                                .each(function() {

                                    let permissionStatus =
                                        $(this).data('status');

                                    $(this).prop(
                                        'checked',
                                        Number(permissionStatus) === 1
                                    );

                                });


                            let errorMessage =
                                err.responseJSON &&
                                err.responseJSON.message ?
                                err.responseJSON.message :
                                'Something went wrong!';


                            Swal.fire(
                                'Error!',
                                errorMessage,
                                'error'
                            );

                        },

                        complete: function() {

                            checkbox.prop('disabled', false);

                            $('.status-change[data-module-id="' + dataAttrs.moduleId + '"]')
                                .prop('disabled', false);

                        }

                    });

                });

            });


            /*
             * Header - View/Create/Edit/Delete All Modules
             */
            $(document).on('click', '.permission-all', function(e) {

                e.preventDefault();

                let checkbox = $(this);

                let permission = checkbox.data('permission');


                Swal.fire({

                    title: 'Change Permission',

                    text: 'Select an action for all modules.',

                    icon: 'question',

                    showCancelButton: true,

                    showDenyButton: true,

                    confirmButtonText: 'Activate All',

                    denyButtonText: 'Deactivate All',

                    cancelButtonText: 'Cancel',

                    confirmButtonColor: '#198754',

                    denyButtonColor: '#dc3545'

                }).then(function(result) {


                    if (result.isConfirmed) {

                        updateAllPermission(
                            permission,
                            1,
                            checkbox
                        );

                    } else if (result.isDenied) {

                        updateAllPermission(
                            permission,
                            0,
                            checkbox
                        );

                    } else {

                        let currentStatus =
                            Number(checkbox.data('status'));

                        checkbox.prop(
                            'checked',
                            currentStatus === 1
                        );

                    }

                });

            });


            /*
             * Header - Update Permission For All Modules
             */
            function updateAllPermission(
                permission,
                status,
                checkbox
            ) {

                $.ajax({

                    url: "{{ route('permissions.status') }}",

                    type: "POST",

                    data: {

                        _token: "{{ csrf_token() }}",

                        role_id: "{{ $selectedRole }}",

                        permission: permission,

                        status: status,

                        all_modules: 1

                    },

                    beforeSend: function() {

                        checkbox.prop('disabled', true);

                        $('.status-change[data-permission="' + permission + '"]')
                            .prop('disabled', true);

                    },

                    success: function(response) {

                        if (response.success) {

                            $('.status-change[data-permission="' + permission + '"]')
                                .each(function() {

                                    $(this)
                                        .prop('checked', status === 1)
                                        .attr('data-status', status)
                                        .data('status', status);


                                    if (response.permission_id) {

                                        $(this)
                                            .attr('data-id', response.permission_id)
                                            .data('id', response.permission_id);

                                    }

                                });


                            checkbox
                                .prop('checked', status === 1)
                                .attr('data-status', status)
                                .data('status', status);


                            Swal.fire({

                                title: 'Updated!',

                                text: status === 1 ?
                                    'Permission activated for all modules.' :
                                    'Permission deactivated for all modules.',

                                icon: 'success',

                                timer: 1000,

                                showConfirmButton: false

                            });

                        }

                    },

                    error: function(err) {

                        let currentStatus =
                            Number(checkbox.data('status'));

                        checkbox.prop(
                            'checked',
                            currentStatus === 1
                        );


                        let message =
                            err.responseJSON &&
                            err.responseJSON.message ?
                            err.responseJSON.message :
                            'Something went wrong!';


                        Swal.fire(
                            'Error!',
                            message,
                            'error'
                        );

                    },

                    complete: function() {

                        checkbox.prop('disabled', false);

                        $('.status-change[data-permission="' + permission + '"]')
                            .prop('disabled', false);

                    }

                });

            }


            /*
             * Set initial state of row All checkboxes
             */
            $('.status-change').each(function() {

                updateAllCheckbox(
                    $(this).data('module-id')
                );

            });


            /*
             * Set initial state of header checkboxes
             */
            updateHeaderCheckbox('can_view');
            updateHeaderCheckbox('can_create');
            updateHeaderCheckbox('can_edit');
            updateHeaderCheckbox('can_delete');

        })();
    </script>
@endpush
