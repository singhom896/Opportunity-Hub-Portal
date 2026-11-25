@extends('backend.layouts.app')

@section('title', isset($module) ? 'Edit Module' : 'Create Module')

@section('content')

    <div class="container-fluid">



        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($menu) ? 'Edit Module' : 'Create Module' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>

                <a href="{{ route('modules.list') }}" class="btn btn-outline-secondary  rounded-2 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-caret-left-fill" aria-hidden="true"></i> Back
                </a>

            </div>
        </div>

        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12 mb-md-0 mb-3">

                <form id="modules-form" action="{{ route('modules.saveUpdate') }}" method="post" class="form-box">

                    @csrf

                    @if (isset($module))
                        <x-text-input name="id" type="hidden" value="{{ $module->id }}" />
                    @endif

                    <div class="row">



                        <div class="col-lg-3 mb-3">
                            <x-select-input name="parent" icon="bi-diagram-2"
                                selected="{{ old('parent', $module->parent_id ?? '') }}" label="Choose Parent"
                                :options="$modules" placeholder="Root" :required="false" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="moduleid" type="number"
                                value="{{ old('moduleid', $module->module_id ?? $nextModuleId ?? '') }}" placeholder="Enter Module Id"
                                label="Enter Module Id" icon="bi-list-ol" autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="name" type="text"
                                value="{{ old('name', $module->module_name ?? '') }}" placeholder="Enter Module Name"
                                label="Enter Module Name" icon="bi-textarea-t" autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="url" type="text" value="{{ old('url', $module->page_url ?? '') }}"
                                placeholder="Enter Page URL" label="Enter Page URL" icon="bi-link" autocomplete="off"
                                :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="position" type="number"
                                value="{{ old('position', $module->position ?? $nextPosition ?? '') }}" placeholder="Enter Position"
                                label="Enter Position" icon="bi-list-ol" autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="icon" type="text" value="{{ old('icon', $module->icon_name ?? '') }}"
                                placeholder="Enter Icon" label="Enter Icon" icon="bi-ui-checks-grid" autocomplete="off"
                                :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="status" icon="bi-check2-circle" label="Select Status"
                                selected="{{ old('status', $module->active ?? '') }}" :options="statusoptions()"
                                placeholder="Select Status" :required="true" />
                        </div>

                        <div class="col-lg-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <button type="submit" class="cms-btn rounded-2">
                                    {{ isset($module) ? 'Update' : 'Submit' }}
                                </button>

                                <button type="reset" class="border-btn rounded-2">
                                    Reset
                                </button>
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

            $("#modules-form").validate({

                ignore: [],

                errorClass: 'text-danger',

                errorElement: 'small',

                rules: {
                    moduleid: {
                        required: true,
                        digits: true
                    },

                    name: {
                        required: true,
                        lettersonly: true
                    },

                    url: {
                        required: true
                    },

                    position: {
                        required: true,
                        digits: true
                    },

                    icon: {
                        required: true
                    },

                    status: {
                        required: true,
                        digits: true,
                        range: [0, 1]
                    }
                },

                messages: {
                    moduleid: {
                        required: "Please enter module ID",
                        digits: "Module ID must be a number"
                    },

                    name: {
                        required: "Please enter module name",
                        lettersonly: "Only letters are allowed"
                    },

                    url: {
                        required: "Please enter module URL"
                    },

                    position: {
                        required: "Please enter module position",
                        digits: "Position must be a number"
                    },

                    icon: {
                        required: "Please enter module icon"
                    },

                    status: {
                        required: "Please choose status",
                        digits: "Status must be numeric",
                        range: "Invalid status selected"
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
