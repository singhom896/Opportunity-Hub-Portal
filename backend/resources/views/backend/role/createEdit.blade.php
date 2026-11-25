@extends('backend.layouts.app')

@section('title', isset($role) ? 'Edit Role' : 'Create Role')

@section('content')
    <div class="container-fluid">



        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($menu) ? 'Edit Role' : 'Create Role' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>

                <a href="{{ route('role.list') }}"
                    class="btn btn-outline-secondary  rounded-2 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-caret-left-fill" aria-hidden="true"></i> Back
                </a>

            </div>
        </div>

        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12">
                <form id="role-form" action="{{ route('role.saveUpdate') }}" method="POST" class="form-box">
                    @csrf

                    @if (isset($role))
                        <input type="hidden" name="id" value="{{ $role->id }}">
                    @endif

                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-12 mb-3">
                            <x-text-input name="role" type="text" value="{{ old('role', $role->name ?? '') }}"
                                placeholder="Enter Role Name" label="Enter Role Name" icon="bi-card-checklist"
                                autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 col-md-4 col-12 mb-3">
                            <x-text-input name="roleid" type="number" value="{{ old('roleid', $role->role_id ?? '') }}"
                                placeholder="Enter Role ID" label="Enter Role ID" icon="bi-card-checklist"
                                autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 col-md-4 col-12 mb-3">
                            <x-select-input name="status" selected="{{ old('status', $role->active ?? '') }}"
                                icon="bi-check2-circle" label="Select Status" :options="statusoptions()"
                                placeholder="Select Status" />
                        </div>

                        <div class="col-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <button type="submit"
                                    class="cms-btn rounded-2">{{ isset($role) ? 'Update' : 'Submit' }}</button>
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
            $("#role-form").validate({
                ignore: [],
                errorClass: 'text-danger',
                errorElement: 'small',
                rules: {
                    roleid: {
                        required: true,
                        digits: true
                    },
                    role: {
                        required: true,
                        pattern: /^[a-zA-Z\s]+$/
                    },
                    status: {
                        required: true,
                        digits: true,
                        range: [0, 1]
                    }
                },
                messages: {
                    roleid: {
                        required: "Enter Role ID",
                        digits: "Role ID must be a number"
                    },
                    role: {
                        required: "Enter Role Name",
                        pattern: "Role Name can contain letters and spaces only"
                    },
                    status: {
                        required: "Please choose Status",
                        digits: "Status must be an integer",
                        range: "Invalid Status selected"
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        })();
    </script>
@endpush
