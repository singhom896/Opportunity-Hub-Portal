@extends('backend.layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Create User')

@section('content')
    <div class="container-fluid">


        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>

                <a href="{{ route('users.list') }}"
                    class="btn btn-outline-secondary  rounded-2 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-caret-left-fill" aria-hidden="true"></i> Back
                </a>

            </div>
        </div>

        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12 mb-md-0 mb-3">
                <form id="user-form" action="{{ route('users.saveUpdate') }}" method="post" class="form-box">
                    @csrf

                    @if (isset($user))
                        <x-text-input name="id" type="hidden" value="{{ $user->id }}" />
                    @endif

                    <div class="row">

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="role" icon="bi-person-badge" label="Select Role"
                                selected="{{ old('role', $user->role_id ?? '') }}" :options="$roles"
                                placeholder="Select Role" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="firstname" type="text"
                                value="{{ old('firstname', $user->firstname ?? '') }}" placeholder="First Name"
                                label="First Name" icon="bi-person" autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="lastname" type="text"
                                value="{{ old('lastname', $user->lastname ?? '') }}" placeholder="Last Name"
                                label="Last Name" icon="bi-person" autocomplete="off" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="gender" icon="bi-gender-ambiguous" label="Select Gender"
                                selected="{{ old('gender', $user->gender ?? '') }}" :options="genderOptions()"
                                placeholder="Select Gender" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="email" type="email" value="{{ old('email', $user->email ?? '') }}"
                                placeholder="Email" label="Email" icon="bi-envelope" autocomplete="off"
                                :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="mobile" type="text" value="{{ old('mobile', $user->mobile ?? '') }}"
                                placeholder="Mobile No" label="Mobile No" icon="bi-phone" autocomplete="off"
                                :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="designation" type="text"
                                value="{{ old('designation', $user->designation ?? '') }}" placeholder="Enter Designation"
                                label="Designation" icon="bi-briefcase" autocomplete="off" :required="false" />
                        </div>
						
						<div class="col-lg-3 mb-3">
                            <x-select-input name="country_id" icon="bi-globe" label="Country"
                                selected="{{ old('country_id', $user->country_id ?? '') }}" :options="$countries"
                                placeholder="Select Country" :required="true" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="region_id" icon="bi-geo-alt" label="Region / State / Province"
                                selected="{{ old('region_id', $user->region_id ?? '') }}" :options="$regions"
                                placeholder="Select Region / State / Province" :required="false" />
                        </div>
						
						
						 <div class="col-lg-6 mb-3">
                            <x-text-input name="city" type="text" value="{{ old('city', $user->city ?? '') }}"
                                placeholder="City / Town" label="City / Town" icon="bi-geo" autocomplete="off"
                                :required="true" />
                        </div>


                        <div class="col-lg-3 mb-3">
                            <x-text-input name="pincode" type="text" value="{{ old('pincode', $user->pincode ?? '') }}"
                                placeholder="Postal / ZIP Code" label="Postal / ZIP Code" icon="bi-pin-map" autocomplete="off"
                                :required="false" />
                        </div>

                        <div class="col-lg-6 mb-3">
                            <x-text-input name="address" type="text" value="{{ old('address', $user->address ?? '') }}"
                                placeholder="Enter Address" label="Address" icon="bi-house" autocomplete="off"
                                :required="false" />
                        </div>

                        <div class="col-lg-3 mb-3">
                            <x-select-input name="status" icon="bi-check2-circle" label="Select Status"
                                selected="{{ old('status', $user->active ?? '') }}" :options="statusoptions()"
                                placeholder="Select Status" :required="true" />
                        </div>

                        <div class="col-lg-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <button type="submit" class="cms-btn rounded-2">
                                    {{ isset($user) ? 'Update' : 'Submit' }}
                                </button>
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

            $.validator.addMethod("lettersWithSpaces", function(value, element) {
                return this.optional(element) || /^[A-Za-z]+(?:\s[A-Za-z]+)*$/.test(value);
            }, "Only letters and spaces are allowed");

            $("#user-form").validate({
                ignore: [],
                errorClass: 'text-danger',
                errorElement: 'small',

                rules: {
                    role: {
                        required: true
                    },
                    firstname: {
                        required: true,
                        lettersWithSpaces: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    lastname: {
                        required: true,
                        lettersWithSpaces: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    gender: {
                        required: true
                    },
                    country_id: {
                        required: true
                    },
                    region_id: {
                        digits: true
                    },
                    city: {
                        required: true,
                        minlength: 2,
                        maxlength: 255
                    },
                    designation: {
                        minlength: 2,
                        maxlength: 255
                    },
                    address: {
                        minlength: 5,
                        maxlength: 255
                    },
                    pincode: {
                        maxlength: 55
                    },
                    status: {
                        required: true,
                        digits: true,
                        range: [0, 1]
                    }
                },

                messages: {
                    role: {
                        required: "Please select Role"
                    },
                    firstname: {
                        required: "Enter First Name",
                        lettersWithSpaces: "First Name can contain only letters and spaces",
                        minlength: "First Name must be at least 2 characters",
                        maxlength: "First Name cannot exceed 100 characters"
                    },
                    lastname: {
                        required: "Enter Last Name",
                        lettersWithSpaces: "Last Name can contain only letters and spaces",
                        minlength: "Last Name must be at least 2 characters",
                        maxlength: "Last Name cannot exceed 100 characters"
                    },
                    email: {
                        required: "Enter Email",
                        email: "Enter a valid Email",
                        maxlength: "Email cannot exceed 255 characters"
                    },
                    mobile: {
                        required: "Enter Mobile Number",
                        digits: "Mobile Number must contain digits only",
                        minlength: "Mobile Number must be exactly 10 digits",
                        maxlength: "Mobile Number must be exactly 10 digits"
                    },
                    gender: {
                        required: "Please select Gender"
                    },
                    designation: {
                        minlength: "Designation must be at least 2 characters",
                        maxlength: "Designation cannot exceed 255 characters"
                    },
                    address: {
                        minlength: "Address must be at least 5 characters",
                        maxlength: "Address cannot exceed 255 characters"
                    },
                    country_id: {
                        required: "Please select Country"
                    },
                    city: {
                        required: "Please enter City / Town",
                        minlength: "City / Town must be at least 2 characters",
                        maxlength: "City / Town cannot exceed 255 characters"
                    },
                    pincode: {
                        maxlength: "Postal / ZIP Code cannot exceed 55 characters"
                    },
                    status: {
                        required: "Please choose Status",
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

    <script>
        $(document).ready(function() {
            const countrySelect = $('select[name="country_id"]');
            const regionSelect = $('select[name="region_id"]');

            countrySelect.on('change', function() {
                const countryId = $(this).val();

                regionSelect.empty().append(
                    $('<option>', {
                        value: '',
                        text: 'Select Region / State / Province'
                    })
                );

                if (!countryId) {
                    return;
                }

                regionSelect.prop('disabled', true);

                $.ajax({
                    url: @json(route('users.regionsByCountry', ['country' => '__COUNTRY__'])).replace('__COUNTRY__', '') + countryId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(regions) {
                        $.each(regions, function(id, name) {
                            regionSelect.append(
                                $('<option>', {
                                    value: id,
                                    text: name
                                })
                            );
                        });
                    },
                    error: function(xhr) {
                        console.error('Regions loading failed:', xhr.responseText);
                    },
                    complete: function() {
                        regionSelect.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
