@extends('backend.layouts.app')

@section('title', $artist ? 'Edit Artist Registration' : 'Create Artist Registration')

@section('content')
    <style>
        .form-box .form-label { color: #263449; font-weight: 600; margin-bottom: .45rem; }
        .form-box .artist-input-group { display: flex; width: 100%; }
        .form-box .artist-input-group > .input-group-text { min-width: 44px; justify-content: center; color: #245a91; background: #fff; border-color: #d7dfeb; border-right: 0; border-radius: .375rem 0 0 .375rem; }
        .form-box .artist-input-group > .form-control, .form-box .artist-input-group > .form-select { min-width: 0; border-color: #d7dfeb; border-left: 0; border-radius: 0 .375rem .375rem 0; box-shadow: none; }
        .form-box .artist-input-group > .form-control:not(textarea), .form-box .artist-input-group > .form-select:not([multiple]) { min-height: 44px; }
        .form-box .artist-input-group > textarea.form-control { min-height: 100px; padding-top: .65rem; }
        .form-box .artist-input-group:focus-within > .input-group-text, .form-box .artist-input-group:focus-within > .form-control, .form-box .artist-input-group:focus-within > .form-select { border-color: #4b91d1; }
        .form-box .form-control:focus, .form-box .form-select:focus { box-shadow: 0 0 0 .15rem rgba(36, 90, 145, .10); }
        .form-box input[type="file"].form-control { min-height: 44px; padding: .55rem .75rem; border-color: #d7dfeb; border-radius: .375rem; background-color: #fff; }
        .form-box .card { border: 1px solid #e0e7f0; border-radius: .65rem; overflow: hidden; box-shadow: 0 2px 10px rgba(24, 48, 80, .035); }
        .form-box .card-header { padding: .9rem 1.1rem; color: #203d60; background: #f4f8fc; border-bottom: 1px solid #e0e7f0; }
        .form-box .card-body { padding: 1.25rem; }
        .form-box .form-text, .form-box small { line-height: 1.5; }
    </style>
	
	<style>
.form-multi-select-options {
    max-height: 250px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}
</style>
 <link href="{{ asset('assets/vendor/v1/coreui/coreui.min.css') }}" rel="stylesheet" />



 
    @php
        $isEdit = (bool) $artist;
        $currentCountryId = $authCountryId;
        $selectedType = old('registration_type', $type);
        $typeLabel = ['individual' => 'Individual Artist', 'group' => 'Artist Group', 'institutional' => 'Institute / Organization'];
        $selectedLanguages = array_map('strval', old('language_ids', explode(',', $individual->language_id ?? $group->language_id ?? $institution->language_id ?? '')));
        $selectedArtForms = array_map('strval', old('art_forms', explode(',', $group->art_form_id ?? $institution->art_form_id ?? '')));
        $supportingPaths = explode(',', $group->supporting_document ?? $institution->supporting_documents ?? '');
    @endphp
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ $isEdit ? 'Edit Artist Registration' : 'Create Artist Registration' }}</h1>
                    <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
                </div>
                <a href="{{ route('cp.artist.list') }}" class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-caret-left-fill"></i> Back
                </a>
            </div>
        </div>

        <form action="{{ route('cp.artist.saveUpdate') }}" method="post" enctype="multipart/form-data" class="form-box">
            @csrf
            @if ($isEdit)
                <input type="hidden" name="id" value="{{ $artist->id }}">
                <input type="hidden" name="registration_type" value="{{ $selectedType }}">
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <strong>Please review the following:</strong>
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header fw-bold">Registration and Contact Information</div>
                <div class="card-body">
                    <div class="row g-3">
					
					  <div class="col-md-4">
                            <label class="form-label fw-bold" for="country_id">Country <span class="text-danger">*</span></label>
                            <select class="form-select" id="country_id_display" disabled aria-label="Assigned country">
                                @foreach ($countries as $countryOption)
                                    <option value="{{ $countryOption->id }}" selected>{{ $countryOption->name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="country_id" name="country_id" value="{{ $currentCountryId }}">
                            @error('country_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
						
						
                        @unless ($isEdit)
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="registration_type">Registration Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="registration_type" name="registration_type" required>
                                    @foreach ($typeLabel as $value => $label)
                                        <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="col-md-4"><label class="form-label fw-bold">Registration Type</label><div class="form-control-plaintext">{{ $typeLabel[$selectedType] }}</div></div>
                        @endunless
                      
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="region_id">Region / State <span class="text-danger">*</span></label>
                            <select class="form-select" id="region_id" name="region_id" required>
                                <option value="">Select Region</option>
                                @foreach ($regions as $regionOption)
                                    <option value="{{ $regionOption->id }}" @selected((string) old('region_id', $artist->region_id ?? '') === (string) $regionOption->id)>{{ $regionOption->name }}</option>
                                @endforeach
                            </select>
                            @error('region_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
						
						
							
						<div class="col-md-4"><label class="form-label fw-bold">City / Town <span class="text-danger">*</span></label><input class="form-control" name="district_city" value="{{ old('district_city', $artist->city ?? '') }}"></div>
							 
						<div class="col-md-8">
                            <label class="form-label fw-bold" for="mailing_address">Mailing Address</label>
                            <input class="form-control" id="mailing_address" name="mailing_address" maxlength="2000" value="{{ old('mailing_address', $artist->address ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="postal">Postal / ZIP Code</label>
                            <input class="form-control" id="postal" name="postal" maxlength="30" value="{{ old('postal', $artist->pincode ?? '') }}">
                        </div>
						
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="email">Email <span class="text-danger">*</span></label>
                            <input class="form-control" id="email" name="email" type="email" maxlength="255" required value="{{ old('email', $artist->email ?? '') }}">
                            @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="phone">Mobile Number</label>
                            <input class="form-control" id="phone" name="phone" type="tel" maxlength="55" value="{{ old('phone', $artist->mobile ?? '') }}">
                        </div>
                        
                    </div>
                </div>
            </div>

            <section class="card mb-4 artist-type-fields" data-registration-type="individual">
                <div class="card-header fw-bold">Individual Artist Details</div>
                <div class="card-body"><div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label><input class="form-control" name="full_name" maxlength="255" value="{{ old('full_name', $artist->full_name ?? '') }}"></div>
               
                    <div class="col-md-4"><label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label><input class="form-control" name="date_of_birth" type="date" max="{{ now()->subYears(18)->toDateString() }}" value="{{ old('date_of_birth', isset($artist->dob) ? \Illuminate\Support\Carbon::parse($artist->dob)->format('Y-m-d') : '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Gender</label><select class="form-select" name="gender"><option value="">Select Gender</option><option value="1" @selected((string) old('gender', $artist->gender ?? '') === '1')>Male</option><option value="2" @selected((string) old('gender', $artist->gender ?? '') === '2')>Female</option><option value="3" @selected((string) old('gender', $artist->gender ?? '') === '3')>Other</option></select></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Art Form <span class="text-danger">*</span></label><select class="form-select" id="art_form_id" name="art_form_id" data-other-select="individual-art-form"><option value="">Select Art Form</option>@foreach ($artForms as $form)<option value="{{ $form->id }}" @selected((string) old('art_form_id', $individual->art_form_id ?? '') === (string) $form->id)>{{ $form->name }}</option>@endforeach</select></div>
					
						
                    <div class="col-md-4 other-field-wrapper d-none" data-other-field="individual-art-form"><label class="form-label fw-bold">Custom Art Form <span class="text-danger">*</span></label><input class="form-control" name="custom_art_form" maxlength="255" value="{{ old('custom_art_form', $individual->other_art_form ?? '') }}"></div>
					
                    <div class="col-md-4"><label class="form-label fw-bold">Subgenre</label><select class="form-select" id="subgenre_id" name="subgenre_id" data-other-select="individual-subgenre"><option value="">Select Subgenre</option>@foreach ($subgenres as $form)<option value="{{ $form->id }}" @selected((string) old('subgenre_id', $individual->subgenre_id ?? '') === (string) $form->id)>{{ $form->name }}</option>@endforeach</select></div>
				
					
                    <div class="col-md-4 other-field-wrapper d-none" data-other-field="individual-subgenre"><label class="form-label fw-bold">Custom Subgenre <span class="text-danger">*</span></label><input class="form-control" name="custom_subgenre" maxlength="255" value="{{ old('custom_subgenre', $individual->other_subgenre ?? '') }}"></div>
					
                    <div class="col-md-4"><label class="form-label fw-bold">Years of Practice</label><input class="form-control" name="years_of_practice" type="number" min="0" max="100" value="{{ old('years_of_practice', $individual->years_of_practice ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Teacher / Guru</label><input class="form-control" name="teacher_guru" maxlength="255" value="{{ old('teacher_guru', $individual->teacher_guru ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Institutional Affiliation</label><input class="form-control" name="institutional_affiliation" maxlength="255" value="{{ old('institutional_affiliation', $individual->institutional_affiliation ?? '') }}"></div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="languageIdsIndividual">Languages Spoken</label>
                        <select class="form-select artist-registration-control" id="languageIdsIndividual" name="language_ids[]" multiple data-coreui-multi-select data-coreui-search="global" data-coreui-selection-type="tags" data-coreui-select-all="false" data-coreui-aria-search-label="Search languages" aria-label="Languages spoken">
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}" @selected(in_array((string) $language->id, $selectedLanguages, true))>{{ $language->name }}</option>
                            @endforeach
                        </select>
                        <!--span class="artist-registration-help">Search and select one or more languages spoken.</span-->
                    </div>
					
                    <div class="col-md-8">
					<label class="form-label fw-bold">Biography </label>
						<textarea class="form-control" name="biography" rows="4" maxlength="2000">{{ old('biography', $individual->biography ?? '') }}</textarea>
					</div>
					
                    <div class="col-md-4"><label class="form-label fw-bold">Profile Photograph</label><input class="form-control" type="file" name="profile_photo" accept=".jpg,.jpeg,.png">@if ($individual?->profile_photo)<small class="d-block mt-1"><a href="{{ asset('uploads/'.$individual->profile_photo) }}" target="_blank" rel="noopener">View current photograph</a></small>@endif</div>
                    <div class="col-md-4"><label class="form-label fw-bold">Identity Document</label><input class="form-control" type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png">@if ($individual?->identity_document)<small class="d-block mt-1"><a href="{{ asset('uploads/'.$individual->identity_document) }}" target="_blank" rel="noopener">View current document</a></small>@endif</div>
                    <div class="col-12"><input type="hidden" name="public_profile" value="0"><label class="form-check"><input class="form-check-input" type="checkbox" name="public_profile" value="1" @checked((bool) old('public_profile', $individual->public_profile ?? false))> <span class="form-check-label">Public profile consent</span></label></div>
                </div></div>
            </section>

            <section class="card mb-4 artist-type-fields" data-registration-type="group">
                <div class="card-header fw-bold">Artist Group Details</div>
                <div class="card-body"><div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-bold">Group Name <span class="text-danger">*</span></label><input class="form-control" name="full_name" maxlength="100" value="{{ old('full_name', $group->group_name ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Group Type <span class="text-danger">*</span></label><select class="form-select" name="group_type_id" data-other-select="group-type"><option value="">Select Group Type</option>@foreach ($groupTypes as $option)<option value="{{ $option->id }}" @selected((string) old('group_type_id', $group->group_type_id ?? '') === (string) $option->id)>{{ $option->name }}</option>@endforeach</select></div>
                    <div class="col-md-4 other-field-wrapper d-none" data-other-field="group-type"><label class="form-label fw-bold">Other Group Type <span class="text-danger">*</span></label><input class="form-control" name="custom_group_type" value="{{ old('custom_group_type', $group->other_group_type ?? '') }}"></div>
					
					
                    <div class="col-md-4"><label class="form-label fw-bold">Establishment Year</label><input class="form-control" name="establishment_year" type="number" min="1800" max="{{ now()->year }}" value="{{ old('establishment_year', $group->establishment_year ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Registration Number</label><input class="form-control" name="registration_number" value="{{ old('registration_number', $group->registration_number ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Total Members <span class="text-danger">*</span></label><input class="form-control" name="total_members" type="number" min="1" max="65535" value="{{ old('total_members', $group->total_members ?? '') }}"></div>
					
                    <div class="col-md-4"><label class="form-label fw-bold">Art Forms <span class="text-danger">*</span></label><select class="form-select artist-registration-control" id="artFormsGroup" name="art_forms[]" multiple data-coreui-multi-select data-coreui-search="global" data-coreui-selection-type="tags" data-coreui-select-all="false" data-coreui-aria-search-label="Search art forms" aria-label="Group art forms" data-other-select="group-art-forms">@foreach ($artForms as $form)<option value="{{ $form->id }}" @selected(in_array((string) $form->id, $selectedArtForms, true))>{{ $form->name }}</option>@endforeach</select></div>
					
                    <div class="col-md-4 other-field-wrapper d-none" data-other-field="group-art-forms"><label class="form-label fw-bold">Other Art Form <span class="text-danger">*</span></label><input class="form-control" name="custom_art_form" value="{{ old('custom_art_form', $group->other_art_form ?? '') }}"></div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="languageIdsGroup">Languages Spoken</label>
                        <select class="form-select artist-registration-control" id="languageIdsGroup" name="language_ids[]" multiple data-coreui-multi-select data-coreui-search="global" data-coreui-selection-type="tags" data-coreui-select-all="false" data-coreui-aria-search-label="Search languages" aria-label="Languages spoken">
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}" @selected(in_array((string) $language->id, $selectedLanguages, true))>{{ $language->name }}</option>
                            @endforeach
                        </select>
                        <!--span class="artist-registration-help">Search and select one or more languages spoken.</span-->
                    </div>
					
					   <div class="col-md-4"><label class="form-label fw-bold">Representative Name <span class="text-danger">*</span></label><input class="form-control" name="representative_name" value="{{ old('representative_name', $artist->full_name ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Representative Designation <span class="text-danger">*</span></label><input class="form-control" name="representative_designation" value="{{ old('representative_designation', $artist->designation ?? '') }}"></div>
					
                    <div class="col-md-8"><label class="form-label fw-bold">Group Description <span class="text-danger">*</span></label><textarea class="form-control" name="biography" rows="2" maxlength="5000">{{ old('biography', $group->group_description ?? '') }}</textarea></div>
					                
					
                    <div class="col-md-4"><label class="form-label fw-bold">Profile Photograph</label><input class="form-control" type="file" name="profile_photo" accept=".jpg,.jpeg,.png">@if ($group?->profile_photo)<small class="d-block mt-1"><a href="{{ asset('uploads/'.$group->profile_photo) }}" target="_blank" rel="noopener">View current photograph</a></small>@endif</div>
					
                    <div class="col-md-4"><label class="form-label fw-bold">Identity Document</label><input class="form-control" type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png">@if ($group?->identity_document)<small class="d-block mt-1"><a href="{{ asset('uploads/'.$group->identity_document) }}" target="_blank" rel="noopener">View current document</a></small>@endif</div>
					
                    <div class="col-md-4"><label class="form-label fw-bold">Additional Supporting Documents</label><input class="form-control" type="file" name="supporting_documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple>@foreach (array_filter($supportingPaths) as $path)<small class="d-inline-block me-3"><a href="{{ asset('uploads/'.$path) }}" target="_blank" rel="noopener">Current supporting document</a></small>@endforeach</div>
					
                </div></div>
            </section>

            <section class="card mb-4 artist-type-fields" data-registration-type="institutional">
                <div class="card-header fw-bold">Cultural Institution Details</div>
                <div class="card-body"><div class="row g-3">
                    <div class="col-md-4"><label class="form-label fw-bold">Institution Name <span class="text-danger">*</span></label><input class="form-control" name="full_name" maxlength="100" value="{{ old('full_name', $institution->institution_name ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Institution Type <span class="text-danger">*</span></label><select class="form-select" name="institution_type_id" data-other-select="institution-type"><option value="">Select Institution Type</option>@foreach ($institutionTypes as $option)<option value="{{ $option->id }}" @selected((string) old('institution_type_id', $institutionTypeId ?? '') === (string) $option->id)>{{ $option->name }}</option>@endforeach</select></div>
                    <div class="col-md-4 other-field-wrapper d-none" data-other-field="institution-type"><label class="form-label fw-bold">Other Institution Type <span class="text-danger">*</span></label><input class="form-control" name="custom_institution_type" value="{{ old('custom_institution_type', $institution->other_institution_type ?? '') }}"></div>
				
					
                    <div class="col-md-4"><label class="form-label fw-bold">Establishment Year</label><input class="form-control" name="establishment_year" type="number" min="1800" max="{{ now()->year }}" value="{{ old('establishment_year', $institution->establishment_year ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Institution Registration Type</label><input class="form-control" name="institution_registration_type" value="{{ old('institution_registration_type', $institution->institution_registration_type ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Registration Number</label><input class="form-control" name="registration_number" value="{{ old('registration_number', $institution->registration_number ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Art Forms <span class="text-danger">*</span></label><select class="form-select artist-registration-control" id="artFormsInstitution" name="art_forms[]" multiple data-coreui-multi-select data-coreui-search="global" data-coreui-selection-type="tags" data-coreui-select-all="false" data-coreui-aria-search-label="Search art forms" aria-label="Institution art forms" data-other-select="institution-art-forms">@foreach ($artForms as $form)<option value="{{ $form->id }}" @selected(in_array((string) $form->id, $selectedArtForms, true))>{{ $form->name }}</option>@endforeach</select></div>
                    <div class="col-md-4 other-field-wrapper d-none" data-other-field="institution-art-forms"><label class="form-label fw-bold">Other Art Form <span class="text-danger">*</span></label><input class="form-control" name="custom_art_form" value="{{ old('custom_art_form', $institution->other_art_form ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Website</label><input class="form-control" name="website" type="url" value="{{ old('website', $institution->website ?? '') }}"></div>
					
					<div class="col-md-4"><label class="form-label fw-bold">Representative Name <span class="text-danger">*</span></label><input class="form-control" name="representative_name" value="{{ old('representative_name', $artist->full_name ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Representative Designation <span class="text-danger">*</span></label><input class="form-control" name="representative_designation" value="{{ old('representative_designation', $artist->designation ?? '') }}"></div>
					
                    <div class="col-md-4"><label class="form-label fw-bold">Institution Description <span class="text-danger">*</span></label><textarea class="form-control" name="biography" rows="4" maxlength="5000">{{ old('biography', $institution->institution_description ?? '') }}</textarea></div>
					
             
					
                    <div class="col-md-4"><label class="form-label fw-bold">Cultural Activities</label><textarea class="form-control" name="cultural_activities" rows="3">{{ old('cultural_activities', $institution->cultural_activities ?? '') }}</textarea></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Training / Research Activities</label><textarea class="form-control" name="training_research_activities" rows="3">{{ old('training_research_activities', $institution->training_research_activities ?? '') }}</textarea></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Number of Associated Artists</label><input class="form-control" type="number" name="associated_artists" min="0" value="{{ old('associated_artists', $institution->associated_artists ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Major Events</label><textarea class="form-control" name="major_events">{{ old('major_events', $institution->major_events ?? '') }}</textarea></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Collaborations</label><textarea class="form-control" name="collaborations">{{ old('collaborations', $institution->collaborations ?? '') }}</textarea></div>
                    @foreach ([
                        'profile_photo' => ['Institution Logo', $institution->institution_logo ?? null, '.jpg,.jpeg,.png'],
                        'id_document' => ['Identity Document', $institution->identity_document ?? null, '.pdf,.jpg,.jpeg,.png'],
                        'authorization_letter' => ['Authorization Letter', $institution->authorization_letter ?? null, '.pdf,.jpg,.jpeg,.png'],
                        'brochure' => ['Brochure / Profile', $institution->brochure_path ?? null, '.pdf,.jpg,.jpeg,.png'],
                        'registration_certificate' => ['Registration Certificate', $institution->registration_certificate ?? null, '.pdf,.jpg,.jpeg,.png'],
                    ] as $input => [$label, $path, $accept])
                        <div class="col-md-4"><label class="form-label fw-bold">{{ $label }}</label><input class="form-control" type="file" name="{{ $input }}" accept="{{ $accept }}">@if ($path)<small><a href="{{ asset('uploads/'.$path) }}" target="_blank" rel="noopener">View current file</a></small>@endif</div>
                    @endforeach
                    <div class="col-md-4"><label class="form-label fw-bold">Additional Supporting Documents</label><input class="form-control" type="file" name="supporting_documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple>@foreach (array_filter(explode(',', $institution->supporting_documents ?? '')) as $path)<small class="d-inline-block me-3"><a href="{{ asset('uploads/'.$path) }}" target="_blank" rel="noopener">Current supporting document</a></small>@endforeach</div>
                </div></div>
            </section>

            <section class="card mb-4">
                <div class="card-header fw-bold">Portfolio Links and Awards</div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0">Portfolio Links</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addPortfolioLink">+ Add Portfolio Link</button>
                    </div>
                    <div id="portfolioLinksContainer">
                        @php($portfolioRows = old('portfolio_links', $portfolioLinks->isNotEmpty() ? $portfolioLinks->all() : ['']))
                        @foreach ($portfolioRows as $linkIndex => $link)
                            <div class="input-group mb-2 portfolio-link-row">
                                <input class="form-control" type="url" name="portfolio_links[]" value="{{ $link }}" placeholder="https://">
								  @if ($linkIndex > 0)
                                <button type="button" class="btn btn-outline-danger remove-portfolio-link" aria-label="Remove portfolio link">Remove</button>
							  @endif
                            </div>
                        @endforeach
                    </div>
					
                    <div class="form-text mb-4">You can add up to 10 portfolio links. This section is available for Individual Artist, Artist Group, and Cultural Institution registrations.</div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0">Awards</label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addAward">+ Add Award</button>
                    </div>
                    @php($awardRows = old('awards', $relatedAwards->isNotEmpty() ? $relatedAwards->map(fn ($row) => ['id' => $row->id, 'award_id' => $row->award_id, 'award_year' => $row->award_year, 'description' => $row->description, 'certificate_path' => $row->certificate_path])->all() : [[]]))
                    <div id="awardsContainer">
                        @foreach ($awardRows as $index => $awardRow)
                            <div class="row g-2 mb-3 award-row">
                                <input type="hidden" data-name="id" name="awards[{{ $index }}][id]" value="{{ $awardRow['id'] ?? '' }}">
                                <div class="col-md-3"><label class="form-label fw-bold">Award</label><select class="form-select" data-name="award_id" name="awards[{{ $index }}][award_id]"><option value="">Select Award</option>@foreach ($awardMasters as $awardMaster)<option value="{{ $awardMaster->id }}" @selected((string) ($awardRow['award_id'] ?? '') === (string) $awardMaster->id)>{{ $awardMaster->name }}</option>@endforeach</select></div>
                                <div class="col-md-2"><label class="form-label fw-bold">Year</label><input class="form-control" data-name="award_year" name="awards[{{ $index }}][award_year]" type="number" min="1800" max="{{ now()->year }}" value="{{ $awardRow['award_year'] ?? '' }}"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Description</label><input class="form-control" data-name="description" name="awards[{{ $index }}][description]" maxlength="2000" value="{{ $awardRow['description'] ?? '' }}"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Certificate</label><input class="form-control" type="file" data-name="certificate" name="awards[{{ $index }}][certificate]" accept=".pdf,.jpg,.jpeg,.png">
                                    @if (!empty($awardRow['certificate_path']))
                                        <small class="d-block mt-1"><a href="{{ asset('uploads/'.$awardRow['certificate_path']) }}" target="_blank" rel="noopener">View current certificate</a></small>
                                    @endif
                                </div>
                                <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-outline-danger remove-award" aria-label="Remove award">×</button></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">You can add up to 10 awards for any registration type.</div>

                    <template id="portfolioLinkTemplate">
                        <div class="input-group mb-2 portfolio-link-row">
                            <input class="form-control" type="url" name="portfolio_links[]" placeholder="https://">
                            <button type="button" class="btn btn-outline-danger remove-portfolio-link" aria-label="Remove portfolio link">Remove</button>
                        </div>
                    </template>
                    <template id="awardTemplate">
                        <div class="row g-2 mb-3 award-row">
                            <input type="hidden" data-name="id" value="">
                            <div class="col-md-3"><label class="form-label fw-bold">Award</label><select class="form-select" data-name="award_id"><option value="">Select Award</option>@foreach ($awardMasters as $awardMaster)<option value="{{ $awardMaster->id }}">{{ $awardMaster->name }}</option>@endforeach</select></div>
                            <div class="col-md-2"><label class="form-label fw-bold">Year</label><input class="form-control" data-name="award_year" type="number" min="1800" max="{{ now()->year }}"></div>
                            <div class="col-md-3"><label class="form-label fw-bold">Description</label><input class="form-control" data-name="description" maxlength="2000"></div>
                            <div class="col-md-3"><label class="form-label fw-bold">Certificate</label><input class="form-control" type="file" data-name="certificate" accept=".pdf,.jpg,.jpeg,.png"></div>
                            <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-outline-danger remove-award" aria-label="Remove award">×</button></div>
                        </div>
                    </template>
                </div>
            </section>
            <div class="d-flex justify-content-end gap-3 my-4">
                <button type="submit" class="cms-btn rounded-2"><i class="bi bi-check-circle me-1"></i>{{ $isEdit ? 'Update Registration' : 'Create Registration' }}</button>
                <a href="{{ route('cp.artist.list') }}" class="border-btn rounded-2">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
 
   <script src="{{ asset('assets/vendor/v1/coreui/coreui.bundle.min.js') }}"></script>
   
    <script>
        $(function () {
            // Match the Event form's input-group design while preserving field names and behaviour.
            const fieldIcons = {
                registration_type: 'person-lines-fill', country_id: 'globe', region_id: 'map', email: 'envelope', phone: 'telephone',
                mailing_address: 'house-door', postal: 'mailbox', full_name: 'person', city: 'geo-alt', district_city: 'geo-alt',
                date_of_birth: 'calendar-date', gender: 'person-vcard', art_form_id: 'palette', subgenre_id: 'diagram-3',
                custom_art_form: 'palette', custom_subgenre: 'tags', years_of_practice: 'calendar3', teacher_guru: 'person-badge',
                institutional_affiliation: 'building', biography: 'file-text', group_type_id: 'people', custom_group_type: 'people',
                establishment_year: 'calendar-event', registration_number: 'card-text', total_members: 'people', art_forms: 'palette',
                representative_name: 'person-badge', representative_designation: 'briefcase', language_ids: 'translate', website: 'globe',
                institution_type_id: 'building', custom_institution_type: 'building', institution_registration_type: 'card-check',
                cultural_activities: 'music-note-list', training_research_activities: 'book', associated_artists: 'people',
                major_events: 'calendar-event', collaborations: 'people', portfolio_links: 'link-45deg', award_id: 'award',
                award_year: 'calendar', description: 'file-text'
            };
            $('.form-box').find('input.form-control:not([type="hidden"]):not([type="file"]):not([type="checkbox"]):not([type="submit"]):not([type="reset"]), select.form-select:not([multiple]), textarea.form-control').each(function () {
                const field = $(this);
                if (field.closest('.artist-input-group').length) return;
                const rawName = (field.attr('name') || '').replace(/\[.*$/, '');
                const icon = fieldIcons[rawName] || 'pencil-square';
                field.wrap('<div class="input-group rounded-1 artist-input-group"></div>');
                field.before('<span class="input-group-text text-blue bg-white"><i class="bi bi-' + icon + '"></i></span>');
            });

            const country = $('#country_id');
            const region = $('#region_id');
            const artForm = $('#art_form_id');
            const subgenre = $('#subgenre_id');
            const typeSelect = $('#registration_type');

            function updateTypeFields() {
                const selectedType = typeSelect.length ? typeSelect.val() : @json($selectedType);
                $('.artist-type-fields').each(function () {
                    const active = $(this).data('registration-type') === selectedType;
                    $(this).toggle(active).find(':input').prop('disabled', !active);
                });
            }

            function loadOptions(url, select, placeholder, selectedValue) {
                select.empty().append(new Option('Loading...', '')).prop('disabled', true);
                $.getJSON(url).done(function (rows) {
                    select.empty().append(new Option(placeholder, ''));
                    $.each(rows, function (_, row) {
                        select.append(new Option(row.name, row.id, false, String(row.id) === String(selectedValue || '')));
                    });
                    select.prop('disabled', false);
                }).fail(function () {
                    select.empty().append(new Option('Unable to load options', '')).prop('disabled', true);
                });
            }

            function updateOtherFields() {
                $('[data-other-field]').each(function () {
                    const wrapper = $(this);
                    const key = wrapper.data('other-field');
                    const section = wrapper.closest('.artist-type-fields');
                    const selector = section.find('[data-other-select="' + key + '"]');
                    const selectedOptions = selector.find('option:selected');
                    const show = selectedOptions.toArray().some(function (option) {
                        return $.trim($(option).text()).toLowerCase() === 'other';
                    });
                    wrapper.toggleClass('d-none', !show);
                    const active = show && section.is(':visible');
                    wrapper.find('input').prop('required', active).prop('disabled', !active);
                    if (!show && !wrapper.find('input').val()) {
                        wrapper.find('input').val('');
                    }
                });
            }

            typeSelect.on('change', function () {
                updateTypeFields();
                updateOtherFields();
            });
            $('[data-other-select]').on('change', updateOtherFields);
            updateTypeFields();
            updateOtherFields();
            country.on('change', function () {
                const countryId = $(this).val();
                if (!countryId) {
                    region.empty().append(new Option('Select Region', ''));
                    return;
                }
                const base = @json(url('/artist-registration/countries'));
                loadOptions(base + '/' + countryId + '/regions', region, 'Select Region');
                loadOptions(base + '/' + countryId + '/art-forms', artForm, 'Select Art Form');
                subgenre.empty().append(new Option('Select Subgenre', ''));
            });
            artForm.on('change', function () {
                const countryId = country.val();
                const formId = $(this).val();
                if (!countryId || !formId) {
                    subgenre.empty().append(new Option('Select Subgenre', ''));
                    return;
                }
                const base = @json(url('/artist-registration/countries'));
                loadOptions(base + '/' + countryId + '/art-forms/' + formId + '/subgenres', subgenre, 'Select Subgenre');
            });

            const maxRepeatRows = 10;
            function renumberAwards() {
                $('#awardsContainer .award-row').each(function (index) {
                    $(this).find('[data-name]').each(function () {
                        const field = $(this);
                        field.attr('name', 'awards[' + index + '][' + field.data('name') + ']');
                    });
                });
            }
            $('#addPortfolioLink').on('click', function () {
                const count = $('#portfolioLinksContainer .portfolio-link-row').length;
                if (count >= maxRepeatRows) { alert('You can add a maximum of 10 portfolio links.'); return; }
                $('#portfolioLinksContainer').append($('#portfolioLinkTemplate').html());
            });
            $(document).on('click', '.remove-portfolio-link', function () {
                const rows = $('#portfolioLinksContainer .portfolio-link-row');
                if (rows.length > 1) $(this).closest('.portfolio-link-row').remove();
                else rows.first().find('input').val('');
            });
            $('#addAward').on('click', function () {
                const count = $('#awardsContainer .award-row').length;
                if (count >= maxRepeatRows) { alert('You can add a maximum of 10 awards.'); return; }
                const row = $($('#awardTemplate').html());
                $('#awardsContainer').append(row);
                renumberAwards();
            });
            $(document).on('click', '.remove-award', function () {
                const rows = $('#awardsContainer .award-row');
                if (rows.length > 1) $(this).closest('.award-row').remove();
                else {
                    const row = rows.first();
                    row.find('input:not([type="hidden"])').val('');
                    row.find('select').val('');
                    row.find('input[type="hidden"]').val('');
                }
                renumberAwards();
            });
            renumberAwards();
        });
    </script>
@endpush
