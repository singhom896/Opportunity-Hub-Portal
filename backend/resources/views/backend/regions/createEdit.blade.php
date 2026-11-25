@extends('backend.layouts.app')

@section('title', isset($region) ? 'Edit Region / State / Province' : 'Create Region / State / Province')

@section('content')
    <div class="container-fluid">


        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($region) ? 'Edit Region / State / Province' : 'Create Region / State / Province' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>

                <a href="{{ route('cp.regions.list') }}"
                    class="btn btn-outline-secondary  rounded-2 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-caret-left-fill" aria-hidden="true"></i> Back
                </a>

            </div>
        </div>

        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12 mb-md-0 mb-3">
                <form id="region-form" action="{{ route('cp.regions.saveUpdate') }}" method="post" class="form-box">
                    @csrf

                    @if (isset($region))
                        <x-text-input name="id" type="hidden" value="{{ $region->id }}" />
                    @endif

                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            @if (($authRoleId ?? null) === 8)
                                <label class="form-label">Country</label>
                                <select class="form-select rounded-1" disabled>
                                    @foreach ($countries as $id => $countryName)
                                        @if ((string) $id === (string) $authCountryId)
                                            <option value="{{ $id }}" selected>{{ $countryName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type="hidden" name="country_id" value="{{ $authCountryId }}">
                            @else
                                <x-select-input name="country_id" icon="bi-globe" label="Country"
                                    selected="{{ old('country_id', $region->country_id ?? '') }}" :options="$countries"
                                    placeholder="Select Country" :required="true" />
                            @endif
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="name" type="text"
                                value="{{ old('name', $region->name ?? '') }}" placeholder="Region / State / Province Name"
                                label="Region / State / Province Name" icon="bi-geo-alt" autocomplete="off" :required="true" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="short_name" type="text"
                                value="{{ old('short_name', $region->short_name ?? '') }}" placeholder="Short Name"
                                label="Short Name" icon="bi-card-text" autocomplete="off" :required="false" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="region_code" type="number"
                                value="{{ old('region_code', $region->region_code ?? '') }}" placeholder="Region Code"
                                label="Region Code" icon="bi-hash" autocomplete="off" :required="false" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="region_type" type="text"
                                value="{{ old('region_type', $region->region_type ?? '') }}" placeholder="Region Type"
                                label="Region Type" icon="bi-list-check" autocomplete="off" :required="false" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-select-input name="status" icon="bi-check2-circle" label="Select Status"
                                selected="{{ old('status', $region->status ?? 1) }}" :options="statusoptions()"
                                placeholder="Select Status" :required="true" />
                        </div>
                        <div class="col-lg-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <button type="submit" class="cms-btn rounded-2">
                                    {{ isset($region) ? 'Update' : 'Submit' }}
                                </button>
                                <button type="reset" class="border-btn rounded-2">Reset</button>
                            </div>
                        </div>
                    </div>                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            $("#region-form").validate({
                errorClass: 'text-danger',
                errorElement: 'small',
                rules: {
                    country_id: { required: {{ (($authRoleId ?? null) === 8) ? 'false' : 'true' }} },
                    name: { required: true, minlength: 2, maxlength: 150 },
                    short_name: { maxlength: 150 },
                    region_code: { digits: true },
                    region_type: { maxlength: 50 },
                    status: { required: true, digits: true, range: [0, 1] }
                },
                messages: {
                    country_id: { required: "Please select Country" },
                    name: { required: "Enter Region / State / Province Name", minlength: "Name must be at least 2 characters", maxlength: "Name cannot exceed 150 characters" },
                    region_code: { digits: "Region Code must be a number" },
                    status: { required: "Please choose Status", range: "Invalid status selected" }
                },
                highlight: function(element) { $(element).addClass('is-invalid'); },
                unhighlight: function(element) { $(element).removeClass('is-invalid'); }
            });
        })();
    </script>
@endpush
