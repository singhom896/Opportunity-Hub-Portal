@extends('backend.layouts.app')

@section('title', isset($menu) ? 'Edit Menu' : 'Create Menu')

@section('content')

    <div class="container-fluid">

        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($menu) ? 'Edit Menu' : 'Create Menu' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>

                <a href="{{ route('menus.list') }}" class="btn btn-outline-secondary  rounded-2 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-caret-left-fill" aria-hidden="true"></i> Back
                </a>

            </div>
        </div>

        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12 mb-md-0 mb-3">

                <form id="document-form" action="{{ route('menus.saveUpdate') }}" method="post" class="form-box">

                    @csrf


                    @if (isset($menu))
                        <input type="hidden" name="id" value="{{ $menu->id }}">
                    @endif

                    <div class="row">

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="parent" selected="{{ old('parent', $menu->parent_id ?? '') }}"
                                icon="bi-check2-circle" label="Parent (If menu has no parent then select Root)"
                                :options="$menuItem" placeholder="Choose Parent" :required="false" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="page_type" selected="{{ old('page_type', $menu->type ?? '') }}"
                                icon="bi-check2-circle" label="Page Type" :options="pagetype()"
                                placeholder="Choose Page Type" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="menu_type"
                                selected="{{ old('menu_type', isset($menu) ? ($menu->is_main && $menu->is_footer ? 3 : ($menu->is_main ? 1 : ($menu->is_footer ? 2 : ''))) : '') }}"
                                label="Menu Type" :options="\App\Models\MenuItem::menuTypeOptions()" placeholder="Select Menu Type" />
                        </div>


                        <div class="col-lg-3 mb-3">
                            <x-select-input name="target" selected="{{ old('target', $menu->target ?? '') }}"
                                icon="bi-check2-circle" label="Menu Target" :options="targettype()" placeholder="Choose Target" />
                        </div>



                        <div class="col-lg-3 mb-3">
                            <x-text-input name="title" type="text" value="{{ old('title', $menu->title ?? '') }}"
                                placeholder="Menu Title" label="Menu Title" icon="bi-card-checklist" autocomplete="off"
                                :required="true" />
                        </div>


                        <div class="col-lg-3 mb-3">
                            <x-text-input name="url" type="text" value="{{ old('url', $menu->url ?? '') }}"
                                placeholder="Menu URL" label="Menu URL" icon="bi-card-checklist" autocomplete="off"
                                :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="ordering" type="text"
                                 value="{{ old('ordering', $menu->order_index ?? $nextOrder ?? 1) }}" placeholder="Enter Ordering"
                                label="Enter Ordering" icon="bi-card-checklist" autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="status" selected="{{ old('status', $menu->active ?? '') }}"
                                icon="bi-check2-circle" label="Status" :options="statusoptions()" placeholder="Select Status" />
                        </div>


                        <div class="col-lg-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <button type="submit" class="cms-btn rounded-2"> {{ isset($menu) ? 'Update' : 'Submit' }}
                                </button>
                                <button type="reset" class="border-btn rounded-2"> Reset </button>
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

            $("#document-form").validate({

                ignore: [],

                errorClass: 'text-danger',

                errorElement: 'small',

                rules: {

                    page_type: {
                        required: true
                    },

                    menu_type: {
                        required: true
                    },

                    target: {
                        required: true
                    },

                    title: {
                        required: true,
                        lettersonly: true
                    },

                    url: {
                        required: true
                    },

                    ordering: {
                        required: true,
                        digits: true
                    },

                    status: {
                        required: true,
                        digits: true,
                        range: [0, 1]
                    }

                },

                messages: {

                    page_type: {
                        required: "Please Choose Page Type"
                    },

                    menu_type: {
                        required: "Please Choose Menu Type"
                    },

                    target: {
                        required: "Please Choose Menu Target"
                    },

                    title: {
                        required: "Enter Menu Title"
                    },

                    url: {
                        required: "Enter Menu URL"
                    },

                    ordering: {
                        required: "Enter Ordering",
                        digits: "Ordering must be a number"
                    },

                    status: {
                        required: "Please Choose Status",
                        digits: "Status must be an integer",
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
