@extends('backend.layouts.app')
@section('title', 'Module Sorting')
@section('content')

@push('styles')
<style>
.sortable-ghost{opacity:.5;background:#e9ecef}
.sortable-chosen{box-shadow:0 .25rem .75rem rgba(0,0,0,.08)}
.drag-handle{cursor:grab}
.drag-handle:active{cursor:grabbing}
.child-module{margin-left:45px}
.child-module-list{min-height:10px}
.parent-module{border-left-color:#0d6efd!important}
.child-toggle{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center}
@media(max-width:767px){.child-module{margin-left:20px}.position-label{display:none}}
</style>
@endpush

<div class="container-fluid">

    <div class="row mb-3 align-items-center">
        <div class="col-12 position-relative text-center">
            <div class="d-inline-block">
                <h1 class="mb-1 fw-bold">Module Sorting</h1>
                <div class="d-flex justify-content-center">
                    <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                </div>
            </div>

            <a href="{{ route('modules.list') }}" class="btn btn-outline-secondary  rounded-2 position-absolute top-50 end-0 translate-middle-y">
                <i class="bi bi-caret-left-fill" aria-hidden="true"></i> Back
            </a>

        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">

        <div class="card-header bg-light border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-bold">Sort Modules</h5>
                    <small class="text-muted">Drag and drop modules to change their display order.</small>
                </div>

            </div>
        </div>

        <div class="card-body">
            <div id="parentModuleList">
                @forelse($modules as $index => $module)
                    <div class="module-wrapper mb-3">
                        <div class="module-sort-item parent-module border border-start border-4 rounded-2 p-3 bg-white shadow-sm" data-module-id="{{ $module->module_id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="drag-handle me-3 text-secondary">
                                        <i class="bi bi-grip-vertical fs-5"></i>
                                    </span>

                                    <span class="badge bg-primary rounded-2 me-3 px-3 py-2 module-position">
                                        {{ $index + 1 }}
                                    </span>

                                    <div>
                                        <div class="fw-semibold">{{ $module->module_name }}</div>
                                        @if($module->page_url)
                                            <small class="text-muted">{{ $module->page_url }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small position-label">
                                        Position: <span>{{ $index + 1 }}</span>
                                    </span>

                                    @if($module->children->count())
                                        <span class="badge bg-light text-dark border">
                                            {{ $module->children->count() }}
                                            {{ $module->children->count() == 1 ? 'Submodule' : 'Submodules' }}
                                        </span>

                                        <button type="button" class="btn btn-sm btn-light border child-toggle" data-target="children-{{ $module->module_id }}" aria-expanded="false" title="Show/Hide Submodules">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($module->children->count())
                            <div class="child-module-list mt-2 d-none" id="children-{{ $module->module_id }}" data-parent-id="{{ $module->module_id }}">
                                @foreach($module->children as $childIndex => $child)
                                    <div class="module-sort-item child-module border rounded-2 p-3 mb-2 bg-light shadow-sm" data-module-id="{{ $child->module_id }}" data-parent-id="{{ $module->module_id }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="drag-handle me-3 text-secondary">
                                                    <i class="bi bi-grip-vertical"></i>
                                                </span>

                                                <span class="badge bg-secondary rounded-2 me-3 px-3 py-2 child-position">
                                                    {{ $childIndex + 1 }}
                                                </span>

                                                <div>
                                                    <div class="fw-semibold">
                                                        <i class="bi bi-arrow-return-right me-2 text-muted"></i>{{ $child->module_name }}
                                                    </div>

                                                    @if($child->page_url)
                                                        <small class="text-muted">{{ $child->page_url }}</small>
                                                    @endif
                                                </div>
                                            </div>

                                            <span class="text-muted small position-label">
                                                Position: <span>{{ $childIndex + 1 }}</span>
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-folder2-open fs-1 text-muted"></i>
                        <p class="mt-3 mb-0 text-muted">No active modules found.</p>
                    </div>
                @endforelse
            </div>

            @if($modules->count())
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" id="saveModuleOrder" class="btn btn-primary rounded-2 px-4 py-2">
                        <i class="bi bi-check2-circle me-1"></i> Save Order
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

<script>
(() => {
    const parentList = document.getElementById('parentModuleList');

    if (parentList) {
        new Sortable(parentList, {
            animation: 150,
            handle: '.parent-module .drag-handle',
            draggable: '.module-wrapper',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function () {
                updateParentPositions();
            }
        });
    }

    document.querySelectorAll('.child-module-list').forEach(function (list) {
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            draggable: '.child-module',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            group: {
                name: 'child-modules',
                pull: false,
                put: false
            },
            onEnd: function () {
                updateChildPositions(list);
            }
        });
    });

    $(document).on('click', '.child-toggle', function () {
        const button = $(this);
        const target = $('#' + button.data('target'));
        const icon = button.find('i');
        target.toggleClass('d-none');

        const isVisible = !target.hasClass('d-none');

        button.attr('aria-expanded', isVisible);
        icon.toggleClass('bi-chevron-down', !isVisible);
        icon.toggleClass('bi-chevron-up', isVisible);
    });

    function updateParentPositions() {
        document.querySelectorAll('#parentModuleList > .module-wrapper').forEach(function (wrapper, index) {
            const position = index + 1;
            const positionElement = wrapper.querySelector('.parent-module .module-position');
            const positionLabel = wrapper.querySelector('.parent-module .position-label span');

            if (positionElement) {
                positionElement.textContent = position;
            }

            if (positionLabel) {
                positionLabel.textContent = position;
            }
        });
    }

    function updateChildPositions(list) {
        list.querySelectorAll('.child-module').forEach(function (item, index) {
            const position = index + 1;
            const positionElement = item.querySelector('.child-position');
            const positionLabel = item.querySelector('.position-label span');

            if (positionElement) {
                positionElement.textContent = position;
            }

            if (positionLabel) {
                positionLabel.textContent = position;
            }
        });
    }

    $('#saveModuleOrder').on('click', function () {
        const button = $(this);
        let modules = [];

        document.querySelectorAll('#parentModuleList > .module-wrapper').forEach(function (wrapper, index) {
            const parent = wrapper.querySelector('.parent-module');

            if (parent) {
                modules.push({
                    module_id: parent.dataset.moduleId,
                    parent_id: 0,
                    position: index + 1
                });
            }

            const childList = wrapper.querySelector('.child-module-list');

            if (childList) {
                childList.querySelectorAll('.child-module').forEach(function (child, childIndex) {
                    modules.push({
                        module_id: child.dataset.moduleId,
                        parent_id: parent.dataset.moduleId,
                        position: childIndex + 1
                    });
                });
            }
        });

        if (!modules.length) {
            Swal.fire({
                title: 'No Modules',
                text: 'No modules available to update.',
                icon: 'warning'
            });
            return;
        }

        $.ajax({
            url: "{{ route('modules.updatePosition') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                modules: modules
            },
            beforeSend: function () {
                button.prop('disabled', true);
                button.html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Updated!',
                        text: response.message,
                        icon: 'success',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            },
            error: function (xhr) {
                let message = 'Unable to update module order.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error'
                });
            },
            complete: function () {
                button.prop('disabled', false);
                button.html('<i class="bi bi-check2-circle me-1"></i> Save Order');
            }
        });
    });
})();
</script>
@endpush
