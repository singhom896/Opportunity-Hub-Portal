@extends('backend.layouts.app')
@section('title', isset($artForm) ? 'Edit Art Form' : 'Create Art Form')
@section('content')
<div class="container-fluid">
    <div class="row mb-3 align-items-center"><div class="col-12 position-relative text-center"><div class="d-inline-block">
        <h1 class="mb-1 fw-bold">{{ isset($artForm) ? 'Edit Art Form' : 'Create Art Form' }}</h1>
        <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
    </div>
    <a href="{{ route('cp.art-forms.list') }}" class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y"><i class="bi bi-caret-left-fill"></i> Back</a>
    </div></div>

    @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <div class="row justify-content-between py-2"><div class="col-xl-12 col-12 mb-md-0 mb-3">
        <form id="art-forms-form" action="{{ route('cp.art-forms.saveUpdate') }}" method="post" class="form-box">
            @csrf
            @if (isset($artForm)) <x-text-input name="id" type="hidden" value="{{ $artForm->id }}" /> @endif
            <div class="row">
                <div class="col-lg-3 mb-3">
                    @if (($authRoleId ?? null) === 8)
                        <label class="form-label">Country</label>
                        <select class="form-select rounded-1" disabled>
                            @foreach ($countries as $id => $countryName)
                                @if ((string)$id === (string)$authCountryId)<option value="{{ $id }}" selected>{{ $countryName }}</option>@endif
                            @endforeach
                        </select>
                        <input type="hidden" name="country_id" id="country_id" value="{{ $authCountryId }}">
                    @else
                        <x-select-input name="country_id" icon="bi-globe" label="Country" selected="{{ old('country_id', $artForm->country_id ?? '') }}" :options="$countries" placeholder="Select Country" :required="true" />
                    @endif
                </div>
                <div class="col-lg-3 mb-3">
                    <label for="parent_id" class="form-label">Category Type / Parent Category <span class="text-danger">*</span></label>
                    <select name="parent_id" id="parent_id" class="form-select rounded-1" required>
                        <option value="0" {{ (string)old('parent_id', $artForm->parent_id ?? 0) === '0' ? 'selected' : '' }}>Main Art Form (No Parent)</option>
                        @foreach ($parentCategories as $parentId => $parentName)
                            <option value="{{ $parentId }}" {{ (string)old('parent_id', $artForm->parent_id ?? 0) === (string)$parentId ? 'selected' : '' }}>{{ $parentName }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select Main Art Form to create a category, or select an existing main category to create a subcategory.</small>
                </div>
                <div class="col-lg-3 mb-3">
                    <x-text-input name="name" type="text" value="{{ old('name', $artForm->name ?? '') }}" placeholder="Art Form Name" label="Art Form Name" icon="bi-palette" autocomplete="off" :required="true" />
                </div>
                <div class="col-lg-3 mb-3">
                    <x-select-input name="status" icon="bi-check2-circle" label="Select Status" selected="{{ old('status', $artForm->status ?? 1) }}" :options="statusoptions()" placeholder="Select Status" :required="true" />
                </div>
                <div class="col-lg-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control" placeholder="Enter description">{{ old('description', $artForm->description ?? '') }}</textarea>
                </div>
                <div class="col-lg-12 my-4"><div class="d-flex justify-content-end align-items-center gap-3">
                    <button type="submit" class="cms-btn rounded-2">{{ isset($artForm) ? 'Update' : 'Submit' }}</button>
                    <button type="reset" class="border-btn rounded-2">Reset</button>
                </div></div>
            </div>
        </form>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const countrySelect = document.querySelector('[name="country_id"]:not([type="hidden"])');
    const hiddenCountryInput = document.querySelector('input[name="country_id"][type="hidden"]');
    const parentSelect = document.getElementById('parent_id');
    const selectedParent = @json((string)old('parent_id', $artForm->parent_id ?? 0));
    const endpointTemplate = @json(route('cp.art-forms.parentCategories', ['countryId' => '__COUNTRY__'])).replace('__COUNTRY__', '');

    async function loadParentCategories(countryId, keepSelection = false) {
        const previous = keepSelection ? parentSelect.value : selectedParent;
        parentSelect.innerHTML = '<option value="0">Main Art Form (No Parent)</option>';
        if (!countryId) return;
        try {
            const response = await fetch(`${endpointTemplate}${encodeURIComponent(countryId)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!response.ok) return;
            const categories = await response.json();
            categories.forEach((category) => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                parentSelect.appendChild(option);
            });
            if ([...parentSelect.options].some(option => option.value === String(previous))) parentSelect.value = String(previous);
        } catch (error) {
            console.error('Unable to load main art form categories.', error);
        }
    }

    if (countrySelect) {
        countrySelect.addEventListener('change', () => loadParentCategories(countrySelect.value, false));
        if (countrySelect.value) loadParentCategories(countrySelect.value, false);
    } else if (hiddenCountryInput && hiddenCountryInput.value) {
        loadParentCategories(hiddenCountryInput.value, false);
    }

    $('#art-forms-form').validate({
        errorClass: 'text-danger', errorElement: 'small',
        rules: {
            country_id: { required: {{ (($authRoleId ?? null) === 8) ? 'false' : 'true' }} },
            parent_id: { required: true, digits: true },
            name: { required: true, minlength: 2, maxlength: 150 },
            status: { required: true, digits: true, range: [0, 1] }
        },
        messages: {
            country_id: { required: 'Please select Country' },
            parent_id: { required: 'Please select category type' },
            name: { required: 'Enter Art Form Name', minlength: 'Name must be at least 2 characters', maxlength: 'Name cannot exceed 150 characters' },
            status: { required: 'Please choose Status', range: 'Invalid status selected' }
        },
        highlight: function(element) { $(element).addClass('is-invalid'); },
        unhighlight: function(element) { $(element).removeClass('is-invalid'); }
    });
})();
</script>
@endpush
