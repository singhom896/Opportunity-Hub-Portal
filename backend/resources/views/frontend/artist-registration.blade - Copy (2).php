@extends('frontend.layouts.app')

@section('title', 'Artist Registration')

{{-- Styles live in style.css (artist-registration-* scoped classes) --}}




@section('content')


   <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro/dist/css/coreui.min.css" rel="stylesheet" />
   


<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Artist Registration</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Artist Registration</span>
        </h1>
        <small class="brx-page-header__description">Register your profile for consideration by the designated Nodal Agency of your country.</small>
    </div>
</section>

   
{{-- =====================================================
     REGISTRATION DISCLAIMER / ACKNOWLEDGEMENT
     ===================================================== --}}
<div id="registrationDisclaimer" class="artist-reg-disclaimer-overlay" role="dialog" aria-modal="true" aria-labelledby="disclaimerTitle" >
    <div class="artist-reg-disclaimer-modal">
        <div class="artist-reg-disclaimer-header">
            <div class="artist-reg-disclaimer-kicker">BRICS ARTIST REGISTRY</div>
            <h2 id="disclaimerTitle">Important Disclaimer &amp; Registration Acknowledgement</h2>
        </div>

        <div class="artist-reg-disclaimer-body">
            <p>
                The BRICS Artist Registry is intended to provide a platform for registration and listing of eligible artists, performers, practitioners and recognised arts and cultural organisations/institutions.
            </p>
            <p>
                Registration and listing are subject to the applicable eligibility requirements and verification/authentication by the designated Nodal Agency of the respective country.
            </p>
            <p><strong>Please note that registration or listing on the Registry does not:</strong></p>
            <ul class="artist-reg-disclaimer-list">
                <li>Guarantee employment or any professional opportunity.</li>
                <li>Guarantee selection for any festival, event, programme or activity.</li>
                <li>Guarantee funding or financial assistance.</li>
                <li>Constitute government accreditation unless separately specified.</li>
                <li>Constitute a professional licence.</li>
                <li>Certify or guarantee the quality of an artist's work or performance.</li>
                <li>Constitute an endorsement of an artist or organisation by all BRICS countries.</li>
                <li>Transfer ownership of copyright or artistic works to the Registry or any participating agency.</li>
            </ul>
            <div class="artist-reg-disclaimer-note">
                <strong>Important:</strong> Submission of an application does not by itself constitute acceptance or listing on the Registry. The application will be reviewed by the designated Nodal Agency of the selected country.
            </div>
            <p>
                Applicants are responsible for ensuring that the information and supporting documents submitted by them are true, accurate and authentic.
            </p>
        </div>

        <div class="artist-reg-disclaimer-consent">
            <label class="artist-reg-disclaimer-check" for="disclaimerAcknowledgement">
                <input class="form-check-input" type="checkbox" id="disclaimerAcknowledgement">
                <span>
                    I have read and understood the above disclaimer and wish to proceed
                    with registration.
                </span>
            </label>
            <div id="disclaimerError" class="artist-reg-disclaimer-error">
                Please acknowledge the disclaimer before continuing.
            </div>
        </div>

        <div class="artist-reg-disclaimer-actions">
            <button type="button" id="disagreeBtn" class="artist-reg-disclaimer-btn">
                I Don't Agree
            </button>
            <button type="button" id="agreeBtn" class="artist-reg-disclaimer-btn artist-reg-disclaimer-btn-agree" disabled>
                I Agree &amp; Continue
            </button>
        </div>
    </div>
</div>



<main class="evt-main artist-registration-main">
<div class="container artist-registration-wrap">


<div class="d-flex justify-content-end artist-registration-terms">
    <button type="button"
            class="btn btn-primary rounded-3 px-4 py-2 artist-registration-terms-btn"
            data-bs-toggle="modal"
            data-bs-target="#artistDisclaimerModal">

        <i class="bi bi-file-earmark-text me-2"></i>
        Read Important Terms
        <i class="bi bi-arrow-up-right ms-2"></i>

    </button>
</div>



    @if (!empty($returnedRegistrations) && $returnedRegistrations->isNotEmpty())
        <section class="alert alert-warning" aria-labelledby="returnedRegistrationsTitle">
            <h2 class="h5" id="returnedRegistrationsTitle">Registrations returned for correction</h2>
            <p>Please review the Nodal Agency remarks and resubmit each corrected registration.</p>
            <ul class="mb-0">
                @foreach ($returnedRegistrations as $returnedRegistration)
                    <li>
                        {{ $returnedRegistration->full_name }} — {{ ucfirst($returnedRegistration->registration_type) }}
                        <a href="{{ route('artist-registration.correction.edit', $returnedRegistration->id) }}">Correct and resubmit</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
    {{-- Process steps --}}
    

    @php($activeRegistrationType = old('registration_type', 'individual'))
    <div class="artist-registration-tabs nav nav-tabs" role="tablist" aria-label="Artist registration type">
        <button class="nav-link @if ($activeRegistrationType === 'individual') active @endif"
                id="individual-tab" type="button" role="tab" aria-controls="individual-panel"
                aria-selected="{{ $activeRegistrationType === 'individual' ? 'true' : 'false' }}"
                data-registration-tab="individual"><i class="bi bi-person-badge" aria-hidden="true"></i><span>Artist (Individual)</span></button>
        <button class="nav-link @if ($activeRegistrationType === 'group') active @endif"
                id="group-tab" type="button" role="tab" aria-controls="group-panel"
                aria-selected="{{ $activeRegistrationType === 'group' ? 'true' : 'false' }}"
                data-registration-tab="group"><i class="bi bi-people" aria-hidden="true"></i><span>Artist Group</span></button>
        <button class="nav-link @if ($activeRegistrationType === 'institutional') active @endif"
                id="institutional-tab" type="button" role="tab" aria-controls="institutional-panel"
                aria-selected="{{ $activeRegistrationType === 'institutional' ? 'true' : 'false' }}"
                data-registration-tab="institutional"><i class="bi bi-bank" aria-hidden="true"></i><span>Institute / Organization</span></button>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-3" role="alert" aria-labelledby="registrationErrorsTitle">
            <strong id="registrationErrorsTitle">Please correct the following before submitting:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Registration form (hidden until disclaimer accepted) --}}
    <form id="artistRegistrationForm"
          class="artist-registration-card @if ($activeRegistrationType !== 'individual') artist-registration-form-hidden @endif"
          @if ($activeRegistrationType !== 'individual') hidden @endif
          role="tabpanel" aria-labelledby="individual-tab" data-registration-panel="individual"
          action="{{ route('artistregistration.store') }}"
          method="post"
          enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="registration_type" value="individual">

        <div class="artist-registration-country-strip" id="selectedCountryStrip" hidden>
            <span class="selected-strip-flag" id="selectedStripFlag" aria-hidden="true">🌐</span>
            <span>
                <b id="selectedStripName">Country not selected</b>
                <small>Registration country</small>
            </span>
        </div>

        <div class="artist-registration-header">
            <span class="artist-registration-required-note">Fields marked * are required</span>
            <h2>Individual Artist Details</h2>
            <p>Please provide the information requested below. The form will be reviewed by the Nodal Agency of the selected country.</p>
        </div>

        {{-- Basic Information --}}
        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Basic Information</h3>
            <div class="row g-3">
                <div class="field">
                    <label class="artist-registration-label" for="country">Country <span>*</span></label>
                    <select class="form-select artist-registration-control" id="country" name="country_id" data-country-select required>
                        <option value="">Select BRICS Country</option>
                        @foreach ($countries as $countryOption)
                            <option value="{{ $countryOption->id }}" @selected((string) old('country_id') === (string) $countryOption->id)>{{ $countryOption->name }}</option>
                        @endforeach
                    </select>
                    <span class="artist-registration-help">Your application will be routed according to the selected country.</span>
                </div>

                <div class="artist-registration-country-confirm" id="countryConfirmation" aria-live="polite" hidden>
                    <div class="artist-registration-country-flag" id="selectedCountryFlag" aria-hidden="true">🌐</div>
                    <div class="artist-registration-country-confirm-text">
                        <span class="artist-registration-country-confirm-label">REGISTRATION COUNTRY</span>
                        <strong id="selectedCountryName">Please select a country</strong>
                        <small>Your application will be routed to the Nodal Agency of this country.</small>
                    </div>
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="fullName">Full Name <span class="">*</span></label>
                    <input class="form-control artist-registration-control" id="fullName" name="full_name" type="text" value="{{ old('full_name') }}"
                           placeholder="Enter your full name as per official ID" required>
                    <span class="artist-registration-help">As per official identification document.</span>
                </div>

          

                <div class="field">
                    <label class="artist-registration-label" for="dob">Date of Birth <span>*</span></label>
                    <input class="form-control artist-registration-control" id="dob" name="date_of_birth" type="date" max="{{ now()->subYears(18)->toDateString() }}" value="{{ old('date_of_birth') }}" required>
                    <span class="artist-registration-help">Used for age verification. Minimum age is 18 years at registration.</span>
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="gender">Gender</label>
                    <select class="form-select artist-registration-control" id="gender" name="gender">
                        <option value="">Prefer not to say</option>
						  <option value="1" @selected(old('gender') === '1')>Male</option>
                        <option value="2" @selected(old('gender') === '2')>Female</option>                   
                        <option value="3" @selected(old('gender') === '3')>Other</option>
                    </select>
                    <span class="artist-registration-help">Optional; self-identified.</span>
                </div>
            </div>
        </section>

        {{-- Art & Cultural Practice --}}
        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Art &amp; Cultural Practice</h3>
            <div class="row g-3">
                <div class="field">
                    <label class="artist-registration-label" for="artForm">Art Form / Discipline <span class="">*</span></label>
                    <select class="form-select artist-registration-control" id="artForm" name="art_form_id" data-art-form-select required>
                        <option value="">Select art form</option>
                        @foreach ($mainArtForms as $artFormOption)
                            <option value="{{ $artFormOption->id }}" @selected((string) old('art_form_id') === (string) $artFormOption->id)>{{ $artFormOption->name }}</option>
                        @endforeach
                    </select>
                    <span class="artist-registration-help">Choose the main discipline associated with your practice.</span>
                </div>

                <div class="field" id="customArtFormField" hidden>
                    <label class="artist-registration-label" for="customArtForm">Please specify the art form <span>*</span></label>
                    <input class="form-control artist-registration-control" id="customArtForm" name="custom_art_form" value="{{ old('custom_art_form') }}" maxlength="255">
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="subgenre">Sub-genre / Tradition</label>
                    <select class="form-select artist-registration-control" id="subgenre" name="subgenre_id" data-subgenre-select>
                        <option value="">Select sub-genre / tradition</option>
                        @foreach ($subgenres as $subgenreOption)
                            <option value="{{ $subgenreOption->id }}" @selected((string) old('subgenre_id') === (string) $subgenreOption->id)>{{ $subgenreOption->name }}</option>
                        @endforeach
                    </select>
                    <span class="artist-registration-help">Enter a specific tradition, style or sub-genre, if applicable.</span>
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="years">Years of Practice</label>
                    <input class="form-control artist-registration-control" id="years" name="years_of_practice" type="number" value="{{ old('years_of_practice') }}"
                           min="0" max="100" placeholder="e.g. 15">
                    <span class="artist-registration-help">Approximate duration of professional practice.</span>
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="guru">Teacher / Guru</label>
                    <input class="form-control artist-registration-control" id="guru" name="teacher_guru" type="text" value="{{ old('teacher_guru') }}"
                           placeholder="Name of primary teacher or guru">
                    <span class="artist-registration-help">Complete if applicable.</span>
                </div>

				<div class="field">
					<label class="artist-registration-label" for="languageIds">
						Languages Spoken
					</label>

					<select
						class="form-select artist-registration-control"
						id="languageIds"
						name="language_ids[]"
						multiple
						data-coreui-multi-select
						data-coreui-search="global"
						data-coreui-selection-type="tags"
						data-coreui-select-all="false"
						data-coreui-aria-search-label="Search languages"
						aria-label="Languages spoken"
					>
						@foreach ($languages as $language)
							<option
								value="{{ $language->id }}"
								@selected(
									in_array(
										(string) $language->id,
										array_map('strval', (array) old('language_ids', [])),
										true
									)
								)
							>
								{{ $language->name }}
							</option>
						@endforeach
					</select>

					<span class="artist-registration-help">
						Search and select one or more languages spoken.
					</span>
				</div>

                <!--div class="field full">
                    <label class="artist-registration-label" for="languageSearch">Languages Spoken</label>
                    <input class="form-control artist-registration-control mb-2" id="languageSearch" type="search" placeholder="Search languages">
                    <select class="form-select artist-registration-control" id="languageIds" name="language_ids[]" multiple size="6" aria-label="Languages spoken">
                        @foreach ($languages as $language)
                            <option value="{{ $language->id }}" @selected(in_array((string) $language->id, array_map('strval', (array) old('language_ids', [])), true))>{{ $language->name }}</option>
                        @endforeach
                    </select>
                    <span class="artist-registration-help">Select one or more languages spoken.</span>
                </div-->

                <div class="field full">
                    <label class="artist-registration-label" for="bio">Biography / Artist Statement</label>
                    <textarea class="form-control artist-registration-control" id="bio" name="biography" maxlength="2000"
                              placeholder="Briefly describe your artistic practice, experience and cultural work...">{{ old('biography') }}</textarea>
                    <div class="artist-registration-counter"><span id="bioCount">0</span> / 300 words maximum</div>
                </div>
            </div>
        </section>

        {{-- Contact & Profile --}}
        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Contact &amp; Profile</h3>
            <div class="row g-3">

                @include('frontend.partials.email-otp-field', [
                    'label' => 'Contact Email',
                    'placeholder' => 'artist@example.com',
                    'help' => 'Primary communication address. Click "Send OTP" and enter the one-time password emailed to you before submitting.',
                ])

                <!--div class="field">
                    <label class="artist-registration-label" for="email">Contact Email <span class="">*</span></label>
                    <input class="form-control artist-registration-control" id="email" name="email" type="email" value="{{ old('email') }}"
                           placeholder="artist@example.com" required>
                    <span class="artist-registration-help">Primary communication address.</span>
                </div-->

                <div class="field">
                    <label class="artist-registration-label" for="phone">Mobile Number </label>
                    <input class="form-control artist-registration-control" id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                           placeholder="Enter mobile number" >
                    <span class="artist-registration-help">Used for account contact and registration updates.</span>
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="address">Address</label>
                    <input class="form-control artist-registration-control" id="address" name="mailing_address" value="{{ old('mailing_address') }}"
                           placeholder="Enter full address for official correspondence">
                </div>
				
				<div class="field">
                    <label class="artist-registration-label" for="region">Region / State / Province <span class="">*</span></label>
                    <select class="form-select artist-registration-control" id="region" name="region_id" data-region-select required>
                        <option value="">Select country first</option>
                    </select>
                    <span class="artist-registration-help">Sub-national region of origin or residence.</span>
                </div>
				
				<div class="field">
                    <label class="artist-registration-label" for="city">City / Town <span class="">*</span></label>
                    <input class="form-control artist-registration-control" id="city" name="city" type="text" value="{{ old('city') }}"
                           placeholder="Enter city or town" required>
                    <span class="artist-registration-help">city</span>
                </div>
				
				<div class="field">
                    <label class="artist-registration-label" for="region">Postal / ZIP Code <span class="">*</span></label>
                    <input class="form-control artist-registration-control" id="postal" name="postal" type="text" value="{{ old('postal') }}"
                           placeholder="Enter postal / ZIP code">
                    <span class="artist-registration-help">Postal / ZIP Code</span>
                </div>
				
                <div class="field">
                    <label class="artist-registration-label" for="affiliation">Institutional Affiliation</label>
                    <input class="form-control artist-registration-control" id="affiliation" name="institutional_affiliation" type="text" value="{{ old('institutional_affiliation') }}"
                           placeholder="University, academy or institution, if any">
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="photo">Profile Photograph</label>
                    <input class="form-control artist-registration-control" id="photo" name="profile_photo" type="file"
                           accept="image/jpeg,image/png">
                    <span class="artist-registration-help">Recent, high-resolution photograph facing the camera.</span>
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="individualPortfolioLinks">Performance / Portfolio Links</label>
                    <div id="individualPortfolioLinks">
                        @foreach (old('portfolio_links', ['']) as $index => $portfolioUrl)
                            <div class="input-group mb-2" data-portfolio-row>
                                <input class="form-control artist-registration-control" name="portfolio_links[]" type="url" value="{{ $portfolioUrl }}" placeholder="https://">
                                @if ($index > 0)
                                    <button class="btn btn-outline-secondary" type="button" data-remove-portfolio>Remove</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button class="artist-registration-btn artist-registration-btn-secondary mt-2" type="button" id="addIndividualPortfolioLink">Add portfolio link</button>
                    <span class="artist-registration-help">Add YouTube, Vimeo, website or online portfolio links.</span>
                </div>
				
				 <div class="field">
                    <label class="artist-registration-label" for="idDocument">Identity Document</label>
                    <input class="form-control artist-registration-control" id="idDocument" name="id_document" type="file"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <span class="artist-registration-help">For identity verification only.</span>
                </div>

                <div class="field full">
                    <label class="artist-registration-label">Awards &amp; Recognition</label>
                    <div id="individualAwardsContainer">
                        @foreach (old('awards', [[]]) as $awardIndex => $oldAward)
                            <div class="border rounded p-3 mb-3" data-award-row>
                                <div class="row g-3">
                                    <div class="field">
                                        <label class="artist-registration-label" for="individualAwardId_{{ $awardIndex }}">Award</label>
                                        <select class="form-select artist-registration-control" id="individualAwardId_{{ $awardIndex }}" name="awards[{{ $awardIndex }}][award_id]">
                                            <option value="">Select award</option>
                                            @foreach ($awardMasters as $awardMaster)
                                                <option value="{{ $awardMaster->id }}" @selected((string) ($oldAward['award_id'] ?? '') === (string) $awardMaster->id)>{{ $awardMaster->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="artist-registration-label" for="individualAwardYear_{{ $awardIndex }}">Award Year</label>
                                        <input class="form-control artist-registration-control" id="individualAwardYear_{{ $awardIndex }}" type="number" min="1800" max="{{ now()->year }}" name="awards[{{ $awardIndex }}][award_year]" value="{{ $oldAward['award_year'] ?? '' }}">
                                    </div>
                                    <div class="field">
                                        <label class="artist-registration-label" for="individualAwardCertificate_{{ $awardIndex }}">Certificate</label>
                                        <input class="form-control artist-registration-control" id="individualAwardCertificate_{{ $awardIndex }}" type="file" accept=".pdf,.jpg,.jpeg,.png" name="awards[{{ $awardIndex }}][certificate]">
                                    </div>
                                    <div class="field full">
                                        <label class="artist-registration-label" for="individualAwardDescription_{{ $awardIndex }}">Description</label>
                                        <textarea class="form-control artist-registration-control" id="individualAwardDescription_{{ $awardIndex }}" name="awards[{{ $awardIndex }}][description]" maxlength="2000">{{ $oldAward['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger mt-2" data-remove-award>Remove award</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="artist-registration-btn artist-registration-btn-secondary mt-2" id="addIndividualAward">Add another award</button>
                    <span class="artist-registration-help">You can add multiple awards. Certificate is optional.</span>
                </div>

               
            </div>
        </section>

        {{-- Declaration & Consent --}}
        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Declaration &amp; Consent</h3>
            <div class="artist-registration-checkbox">
                <input class="form-check-input" id="consent" type="checkbox" name="consent" required @checked(old('consent'))>
                <label class="artist-registration-label" for="consent">
                    I confirm that the information provided by me is accurate to the best of my knowledge,
                    and I consent to the processing of my information for registration and assessment by the
                    designated Nodal Agency of my selected country. I understand that the Registry is voluntary
                    and registration does not guarantee employment, selection for a festival or programme,
                    funding, accreditation, or performance opportunities.
                </label>
            </div>
            <div class="artist-registration-checkbox">
                <input class="form-check-input" id="publicProfile" type="checkbox" name="public_profile" @checked(old('public_profile'))>
                <label class="artist-registration-label" for="publicProfile">
                    I understand that, if approved, information designated as public by the relevant Member State
                    may be displayed in the public BRICS Artist Registry.
                </label>
            </div>
        </section>

        <div class="artist-registration-footer">
            <div class="artist-registration-save-note">Your application will be submitted for review. A secure password setup link will be sent to your email.</div>
            <div class="artist-registration-buttons">
                <button type="reset" class="artist-registration-btn artist-registration-btn-secondary">Reset</button>
                <button type="submit" class="artist-registration-btn artist-registration-btn-primary">Register</button>
            </div>
        </div>
    </form>

    <form id="groupRegistrationForm"
          class="artist-registration-card @if ($activeRegistrationType !== 'group') artist-registration-form-hidden @endif"
          @if ($activeRegistrationType !== 'group') hidden @endif
          role="tabpanel" aria-labelledby="group-tab" data-registration-panel="group"
          action="{{ route('artistregistration.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="registration_type" value="group">
        <div class="artist-registration-header">
            <span class="artist-registration-required-note">Fields marked * are required</span>
            <h2>Group Artist Registration</h2>
            <p>Register your cultural group and its representative for review by the Nodal Agency of the selected country.</p>
        </div>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Group Information</h3>
            <div class="row g-3">
				
				<div class="field">
                    <label class="artist-registration-label" for="groupCountry">Country <span>*</span></label>
                    <select class="form-select artist-registration-control" id="groupCountry" name="country_id" data-country-select required>
                        <option value="">Select country</option>
                        @foreach ($countries as $countryOption)
                            <option value="{{ $countryOption->id }}" @selected((string) old('country_id') === (string) $countryOption->id)>{{ $countryOption->name }}</option>
                        @endforeach
                    </select>
                </div>
				

                <div class="artist-registration-country-confirm" id="countryConfirmation" aria-live="polite" hidden>
                    <div class="artist-registration-country-flag" id="selectedCountryFlag" aria-hidden="true">🌐</div>
                    <div class="artist-registration-country-confirm-text">
                        <span class="artist-registration-country-confirm-label">REGISTRATION COUNTRY</span>
                        <strong id="selectedCountryName">Please select a country</strong>
                        <small>Your application will be routed to the Nodal Agency of this country.</small>
                    </div>
                </div>

			
                <div class="field">
                    <label class="artist-registration-label" for="groupName">Group Name <span>*</span></label>
                    <input class="form-control artist-registration-control" id="groupName" name="full_name" value="{{ old('full_name') }}" required>
                </div>
				
                <div class="field">
                    <label class="artist-registration-label" for="groupType">Group Type <span>*</span></label>
                    <select class="form-select artist-registration-control" id="groupType" name="group_type_id" data-group-type-select required>
                        <option value="">Select group type</option>
                        @foreach ($groupTypes as $option)
                            <option value="{{ $option->id }}" @selected((string) old('group_type_id') === (string) $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" id="customGroupTypeField" hidden>
                    <label class="artist-registration-label" for="customGroupType">Please specify group type</label>
                    <input class="form-control artist-registration-control" id="customGroupType" name="custom_group_type" value="{{ old('custom_group_type') }}" maxlength="255">
                </div>

                <div class="field">
                    <label class="artist-registration-label" for="groupYear">Establishment Year</label>
                    <input class="form-control artist-registration-control" id="groupYear" name="establishment_year" type="number" min="1800" max="{{ now()->year }}" value="{{ old('establishment_year') }}">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="groupRegistrationNumber">Registration Number</label>
                    <input class="form-control artist-registration-control" id="groupRegistrationNumber" name="registration_number" value="{{ old('registration_number') }}">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="totalMembers">Total Members <span>*</span></label>
                    <input class="form-control artist-registration-control" id="totalMembers" name="total_members" type="number" min="1" max="65535" value="{{ old('total_members') }}" required>
                    <span class="artist-registration-help">Declared total; member details are managed after the representative logs in.</span>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="groupArtForms">Art Form(s) <span>*</span></label>
                    <select class="form-select artist-registration-control" id="groupArtForms" name="art_forms[]" data-art-forms-select multiple required>
                        @foreach ($mainArtForms as $artFormOption)
                            <option value="{{ $artFormOption->id }}" @selected(in_array((string) $artFormOption->id, array_map('strval', (array) old('art_forms', [])), true))>{{ $artFormOption->name }}</option>
                        @endforeach
                    </select>
                    <span class="artist-registration-help">Select one or more disciplines. Use Ctrl or Command for multiple selections.</span>
                </div>
                <div class="field" id="groupCustomArtFormField" hidden>
                    <label class="artist-registration-label" for="groupCustomArtForm">Please specify the Other art form</label>
                    <input class="form-control artist-registration-control" id="groupCustomArtForm" name="custom_art_form" value="{{ old('custom_art_form') }}" maxlength="255">
                </div>
                <!--div class="field full">
                    <label class="artist-registration-label" for="groupLanguageSearch">Languages</label>
                    <input class="form-control artist-registration-control mb-2" id="groupLanguageSearch" type="search" placeholder="Search languages">
                    <select class="form-select artist-registration-control" id="groupLanguageIds" name="language_ids[]" multiple size="6" aria-label="Group languages">
                        @foreach ($languages as $language)
                            <option value="{{ $language->id }}" @selected(in_array((string) $language->id, array_map('strval', (array) old('language_ids', [])), true))>{{ $language->name }}</option>
                        @endforeach
                    </select>
                    <span class="artist-registration-help">Select one or more languages used by the group.</span>
                </div-->
				
				<div class="field">
					<label class="artist-registration-label" for="groupLanguageIds">
						Languages
					</label>

					<select
						class="form-select artist-registration-control"
						id="groupLanguageIds"
						name="language_ids[]"
						multiple
						data-coreui-multi-select
						data-coreui-search="global"
						data-coreui-selection-type="tags"
						data-coreui-select-all="false"
						data-coreui-aria-search-label="Search languages"
						aria-label="Group languages"
					>
						@foreach ($languages as $language)
							<option
								value="{{ $language->id }}"
								@selected(
									in_array(
										(string) $language->id,
										array_map('strval', (array) old('language_ids', [])),
										true
									)
								)
							>
								{{ $language->name }}
							</option>
						@endforeach
					</select>

					<span class="artist-registration-help">
						Search and select one or more languages used by the group.
					</span>
				</div>
	

                <div class="field full">
                    <label class="artist-registration-label" for="groupDescription">Group Description <span>*</span></label>
                    <textarea class="form-control artist-registration-control" id="groupDescription" name="biography" maxlength="5000" required>{{ old('biography') }}</textarea>
                </div>
            </div>
        </section>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Representative Details</h3>
            <div class="row g-3">
                <div class="field">
                    <label class="artist-registration-label" for="groupRepresentative">Full Name <span>*</span></label>
                    <input class="form-control artist-registration-control" id="groupRepresentative" name="representative_name" value="{{ old('representative_name') }}" required>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="groupDesignation">Designation <span>*</span></label>
                    <input class="form-control artist-registration-control" id="groupDesignation" name="representative_designation" value="{{ old('representative_designation') }}" required>
                </div>
                @include('frontend.partials.email-otp-field', [
                    'label' => 'Email',
                    'placeholder' => 'Enter representative email',
                    'help' => 'A one-time password (OTP) will be sent to this email for verification.',
                ])
                <div class="field">
                    <label class="artist-registration-label" for="groupMobile">Mobile Number </label>
                    <input class="form-control artist-registration-control" id="groupMobile" name="phone" type="tel" value="{{ old('phone') }}" >
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="groupAddress">Address <span>*</span></label>
					 <input class="form-control artist-registration-control" id="groupAddress" name="mailing_address" type="text" value="{{ old('mailing_address') }}" required>
                   
                </div>
				
				   <div class="field">
                    <label class="artist-registration-label" for="groupRegion">Region / State / Province <span>*</span></label>
                    <select class="form-select artist-registration-control" id="groupRegion" name="region_id" data-region-select required>
                        <option value="">Select country first</option>
                    </select>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="groupDistrict">City / Town  <span>*</span></label>
                    <input class="form-control artist-registration-control" id="groupDistrict" name="district_city" value="{{ old('district_city') }}" required>
                </div>
				
				<div class="field">
                    <label class="artist-registration-label" for="region">Postal / ZIP Code <span class="">*</span></label>
                    <input class="form-control artist-registration-control" id="groupPostal" name="postal" type="text" value="{{ old('postal') }}"
                           placeholder="Enter postal / ZIP code">
                    <span class="artist-registration-help">Postal / ZIP Code</span>
                </div>
				
                <div class="field">
                    <label class="artist-registration-label" for="groupIdentityDocument">Identity Document</label>
                    <input class="form-control artist-registration-control" id="groupIdentityDocument" name="id_document" type="file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        </section>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Portfolio</h3>
            <div class="row g-3">
                <div class="field">
                    <label class="artist-registration-label" for="groupPhoto">Group Photo</label>
                    <input class="form-control artist-registration-control" id="groupPhoto" name="profile_photo" type="file" accept="image/jpeg,image/png">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="groupPortfolioLinks">Performance / Portfolio Links</label>
                    <div id="groupPortfolioLinks">
                        @foreach (old('portfolio_links', ['']) as $index => $portfolioUrl)
                            <div class="input-group mb-2" data-portfolio-row>
                                <input class="form-control artist-registration-control" name="portfolio_links[]" type="url" value="{{ $portfolioUrl }}" placeholder="https://">
                                @if ($index > 0)
                                    <button class="btn btn-outline-secondary" type="button" data-remove-portfolio>Remove</button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button class="artist-registration-btn artist-registration-btn-secondary mt-2" type="button" id="addGroupPortfolioLink">Add portfolio link</button>
                    <span class="artist-registration-help">Add one or more links for the group.</span>
                </div>
				 <div class="field ">
                    <label class="artist-registration-label" for="groupDocuments">Supporting Documents</label>
                    <input class="form-control artist-registration-control" id="groupDocuments" name="supporting_documents[]" type="file" accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <span class="artist-registration-help">Up to five PDF, JPG or PNG files, 10 MB each.</span>
                </div>
                <div class="field full">
                    <label class="artist-registration-label">Awards and Recognition</label>
                    <div id="groupAwardsContainer">
                        @foreach (old('awards', [[]]) as $awardIndex => $oldAward)
                            <div class="border rounded p-3 mb-3" data-award-row>
                                <div class="row g-3">
                                    <div class="field">
                                        <label class="artist-registration-label" for="groupAwardId_{{ $awardIndex }}">Award</label>
                                        <select class="form-select artist-registration-control" id="groupAwardId_{{ $awardIndex }}" name="awards[{{ $awardIndex }}][award_id]">
                                            <option value="">Select award</option>
                                            @foreach ($awardMasters as $awardMaster)
                                                <option value="{{ $awardMaster->id }}" @selected((string) ($oldAward['award_id'] ?? '') === (string) $awardMaster->id)>{{ $awardMaster->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="artist-registration-label" for="groupAwardYear_{{ $awardIndex }}">Award Year</label>
                                        <input class="form-control artist-registration-control" id="groupAwardYear_{{ $awardIndex }}" type="number" min="1800" max="{{ now()->year }}" name="awards[{{ $awardIndex }}][award_year]" value="{{ $oldAward['award_year'] ?? '' }}">
                                    </div>
                                    <div class="field">
                                        <label class="artist-registration-label" for="groupAwardCertificate_{{ $awardIndex }}">Certificate</label>
                                        <input class="form-control artist-registration-control" id="groupAwardCertificate_{{ $awardIndex }}" type="file" accept=".pdf,.jpg,.jpeg,.png" name="awards[{{ $awardIndex }}][certificate]">
                                    </div>
                                    <div class="field full">
                                        <label class="artist-registration-label" for="groupAwardDescription_{{ $awardIndex }}">Description</label>
                                        <textarea class="form-control artist-registration-control" id="groupAwardDescription_{{ $awardIndex }}" name="awards[{{ $awardIndex }}][description]" maxlength="2000">{{ $oldAward['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger mt-2" data-remove-award>Remove award</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="artist-registration-btn artist-registration-btn-secondary mt-2" id="addGroupAward">Add another award</button>
                    <span class="artist-registration-help">You can add multiple awards. Certificate is optional.</span>
                </div>
               
            </div>
        </section>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Declaration &amp; Consent</h3>
            <div class="artist-registration-checkbox">
                <input class="form-check-input" id="groupConsent" type="checkbox" name="consent" required @checked(old('consent'))>
                <label class="artist-registration-label" for="groupConsent">I confirm the group information is accurate and authorize this registration. <span>*</span></label>
            </div>
            <div class="artist-registration-checkbox">
                <input class="form-check-input" id="groupDataConsent" type="checkbox" name="data_processing_consent" required @checked(old('data_processing_consent'))>
                <label class="artist-registration-label" for="groupDataConsent">I consent to processing the information and documents for registration and assessment. <span>*</span></label>
            </div>
        </section>

        <div class="artist-registration-footer">
            <div class="artist-registration-save-note">A secure password setup link will be sent to the representative email.</div>
            <div class="artist-registration-buttons">
                <button type="reset" class="artist-registration-btn artist-registration-btn-secondary">Reset</button>
                <button type="submit" class="artist-registration-btn artist-registration-btn-primary">Register Group</button>
            </div>
        </div>
    </form>

    <form id="institutionRegistrationForm"
          class="artist-registration-card @if ($activeRegistrationType !== 'institutional') artist-registration-form-hidden @endif"
          @if ($activeRegistrationType !== 'institutional') hidden @endif
          role="tabpanel" aria-labelledby="institutional-tab" data-registration-panel="institutional"
          action="{{ route('artistregistration.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="registration_type" value="institutional">
        <div class="artist-registration-header">
            <span class="artist-registration-required-note">Fields marked * are required</span>
            <h2>Institute / Organization Registration</h2>
            <p>Register your cultural institution and its authorized representative for review by the Nodal Agency.</p>
        </div>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Institution Information</h3>
            <div class="row g-3">
			
			 <div class="field">
                    <label class="artist-registration-label" for="institutionCountry">Country <span>*</span></label>
                    <select class="form-select artist-registration-control" id="institutionCountry" name="country_id" data-country-select required>
                        <option value="">Select country</option>
                        @foreach ($countries as $countryOption)
                            <option value="{{ $countryOption->id }}" @selected((string) old('country_id') === (string) $countryOption->id)>{{ $countryOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionName">Institution Name <span>*</span></label>
                    <input class="form-control artist-registration-control" id="institutionName" name="full_name" value="{{ old('full_name') }}" required>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionType">Institution Type <span>*</span></label>
                    <select class="form-select artist-registration-control" id="institutionType" name="institution_type_id" required>
                        <option value="">Select institution type</option>
                        @foreach ($institutionTypes as $option)
                            <option value="{{ $option->id }}" @selected((string) old('institution_type_id') === (string) $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
               
                
                <div class="field">
                    <label class="artist-registration-label" for="institutionYear">Establishment Year</label>
                    <input class="form-control artist-registration-control" id="institutionYear" name="establishment_year" type="number" min="1800" max="{{ now()->year }}" value="{{ old('establishment_year') }}">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionRegistrationType">Registration Type</label>
                    <input class="form-control artist-registration-control" id="institutionRegistrationType" name="institution_registration_type" value="{{ old('institution_registration_type') }}">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionRegistrationNumber">Registration Number</label>
                    <input class="form-control artist-registration-control" id="institutionRegistrationNumber" name="registration_number" value="{{ old('registration_number') }}">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionArtForms">Art Forms <span>*</span></label>
                    <select class="form-select artist-registration-control" id="institutionArtForms" name="art_forms[]" data-art-forms-select multiple required>
                        @foreach ($mainArtForms as $artFormOption)
                            <option value="{{ $artFormOption->id }}" @selected(in_array((string) $artFormOption->id, array_map('strval', (array) old('art_forms', [])), true))>{{ $artFormOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" id="institutionCustomArtFormField" hidden>
                    <label class="artist-registration-label" for="institutionCustomArtForm">Please specify the Other art form</label>
                    <input class="form-control artist-registration-control" id="institutionCustomArtForm" name="custom_art_form" value="{{ old('custom_art_form') }}" maxlength="255">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionWebsite">Website</label>
                    <input class="form-control artist-registration-control" id="institutionWebsite" name="website" type="url" value="{{ old('website') }}">
                </div>
                <div class="field full">
                    <label class="artist-registration-label" for="institutionDescription">Institution Description <span>*</span></label>
                    <textarea class="form-control artist-registration-control" id="institutionDescription" name="biography" maxlength="5000" required>{{ old('biography') }}</textarea>
                </div>
            </div>
        </section>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Authorized Representative</h3>
            <div class="row g-3">
                <div class="field">
                    <label class="artist-registration-label" for="institutionRepresentative">Full Name <span>*</span></label>
                    <input class="form-control artist-registration-control" id="institutionRepresentative" name="representative_name" value="{{ old('representative_name') }}" required>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionDesignation">Designation <span>*</span></label>
                    <input class="form-control artist-registration-control" id="institutionDesignation" name="representative_designation" value="{{ old('representative_designation') }}" required>
                </div>
                @include('frontend.partials.email-otp-field', [
                    'label' => 'Official Email',
                    'placeholder' => 'Enter official email',
                    'help' => 'A one-time password (OTP) will be sent to this email for verification.',
                ])
                <div class="field">
                    <label class="artist-registration-label" for="institutionMobile">Mobile Number </label>
                    <input class="form-control artist-registration-control" id="institutionMobile" name="phone" type="tel" value="{{ old('phone') }}" >
                </div>
                <div class="field ">
                    <label class="artist-registration-label" for="institutionAddress">Office Address <span>*</span></label>
					<input class="form-control artist-registration-control" id="institutionAddress" name="mailing_address" value="{{ old('mailing_address') }}" required>
                </div>
				<div class="field">
                    <label class="artist-registration-label" for="institutionRegion">State / Province / Region <span>*</span></label>
                    <select class="form-select artist-registration-control" id="institutionRegion" name="region_id" data-region-select required>
                        <option value="">Select country first</option>
                    </select>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionDistrict">City / Town <span>*</span></label>
                    <input class="form-control artist-registration-control" id="institutionDistrict" name="district_city" value="{{ old('district_city') }}" required>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionIdentityDocument">Identity Document</label>
                    <input class="form-control artist-registration-control" id="institutionIdentityDocument" name="id_document" type="file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="authorizationLetter">Authorization Letter</label>
                    <input class="form-control artist-registration-control" id="authorizationLetter" name="authorization_letter" type="file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>
        </section>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Artistic Activities</h3>
            <div class="row g-3">
                <div class="field">
                    <label class="artist-registration-label" for="culturalActivities">Cultural Activities</label>
                    <textarea class="form-control artist-registration-control" id="culturalActivities" name="cultural_activities">{{ old('cultural_activities') }}</textarea>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="trainingActivities">Training / Research Activities</label>
                    <textarea class="form-control artist-registration-control" id="trainingActivities" name="training_research_activities">{{ old('training_research_activities') }}</textarea>
                </div>
                <!--div class="field full">
                    <label class="artist-registration-label" for="institutionLanguageSearch">Languages</label>
                    <input class="form-control artist-registration-control mb-2" id="institutionLanguageSearch" type="search" placeholder="Search languages">
                    <select class="form-select artist-registration-control" id="institutionLanguageIds" name="language_ids[]" multiple size="6" aria-label="Institutional languages">
                        @foreach ($languages as $language)
                            <option value="{{ $language->id }}" @selected(in_array((string) $language->id, array_map('strval', (array) old('language_ids', [])), true))>{{ $language->name }}</option>
                        @endforeach
                    </select>
                    <span class="artist-registration-help">Select one or more languages used by the institution.</span>
                </div-->
				
				<div class="field">
					<label class="artist-registration-label" for="institutionLanguageIds">
						Languages
					</label>

					<select
						class="form-select artist-registration-control"
						id="institutionLanguageIds"
						name="language_ids[]"
						multiple
						data-coreui-multi-select
						data-coreui-search="global"
						data-coreui-selection-type="tags"
						data-coreui-aria-search-label="Search languages"
						aria-label="Institutional languages"
						data-coreui-select-all="false"
					>
						@foreach ($languages as $language)
							<option
								value="{{ $language->id }}"
								@selected(
									in_array(
										(string) $language->id,
										array_map('strval', (array) old('language_ids', [])),
										true
									)
								)
							>
								{{ $language->name }}
							</option>
						@endforeach
					</select>

					<span class="artist-registration-help">
						Search and select one or more languages used by the institution.
					</span>
				</div>
				
				
                <div class="field">
                    <label class="artist-registration-label" for="associatedArtists">Number of Associated Artists</label>
                    <input class="form-control artist-registration-control" id="associatedArtists" name="associated_artists" type="number" min="0" value="{{ old('associated_artists') }}">
                </div>
            </div>
        </section>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Institutional Portfolio</h3>
            <div class="row g-3">
                <div class="field">
                    <label class="artist-registration-label" for="institutionLogo">Institution Logo</label>
                    <input class="form-control artist-registration-control" id="institutionLogo" name="profile_photo" type="file" accept="image/jpeg,image/png">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionBrochure">Brochure / Profile</label>
                    <input class="form-control artist-registration-control" id="institutionBrochure" name="brochure" type="file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="field full">
                    <label class="artist-registration-label" for="majorEvents">Major Events</label>
                    <textarea class="form-control artist-registration-control" id="majorEvents" name="major_events">{{ old('major_events') }}</textarea>
                </div>
                <div class="field full">
                    <label class="artist-registration-label">Awards</label>
                    <div id="institutionAwardsContainer">
                        @foreach (old('awards', [[]]) as $awardIndex => $oldAward)
                            <div class="border rounded p-3 mb-3" data-award-row>
                                <div class="row g-3">
                                    <div class="field">
                                        <label class="artist-registration-label" for="institutionAwardId_{{ $awardIndex }}">Award</label>
                                        <select class="form-select artist-registration-control" id="institutionAwardId_{{ $awardIndex }}" name="awards[{{ $awardIndex }}][award_id]">
                                            <option value="">Select award</option>
                                            @foreach ($awardMasters as $awardMaster)
                                                <option value="{{ $awardMaster->id }}" @selected((string) ($oldAward['award_id'] ?? '') === (string) $awardMaster->id)>{{ $awardMaster->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="artist-registration-label" for="institutionAwardYear_{{ $awardIndex }}">Award Year</label>
                                        <input class="form-control artist-registration-control" id="institutionAwardYear_{{ $awardIndex }}" type="number" min="1800" max="{{ now()->year }}" name="awards[{{ $awardIndex }}][award_year]" value="{{ $oldAward['award_year'] ?? '' }}">
                                    </div>
                                    <div class="field">
                                        <label class="artist-registration-label" for="institutionAwardCertificate_{{ $awardIndex }}">Certificate</label>
                                        <input class="form-control artist-registration-control" id="institutionAwardCertificate_{{ $awardIndex }}" type="file" accept=".pdf,.jpg,.jpeg,.png" name="awards[{{ $awardIndex }}][certificate]">
                                    </div>
                                    <div class="field full">
                                        <label class="artist-registration-label" for="institutionAwardDescription_{{ $awardIndex }}">Description</label>
                                        <textarea class="form-control artist-registration-control" id="institutionAwardDescription_{{ $awardIndex }}" name="awards[{{ $awardIndex }}][description]" maxlength="2000">{{ $oldAward['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger mt-2" data-remove-award>Remove award</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="artist-registration-btn artist-registration-btn-secondary mt-2" id="addInstitutionAward">Add another award</button>
                    <span class="artist-registration-help">You can add multiple awards. Certificate is optional.</span>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionCollaborations">Collaborations</label>
                    <textarea class="form-control artist-registration-control" id="institutionCollaborations" name="collaborations">{{ old('collaborations') }}</textarea>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionPortfolioLinks">Portfolio Links</label>
                   <div id="institutionPortfolioLinks">
                       @foreach (old('portfolio_links', ['']) as $index => $portfolioUrl)
                           <div class="input-group mb-2" data-portfolio-row>
                               <input class="form-control artist-registration-control" name="portfolio_links[]" type="url" value="{{ $portfolioUrl }}" placeholder="https://">
                               @if ($index > 0)
                                   <button class="btn btn-outline-secondary" type="button" data-remove-portfolio>Remove</button>
                               @endif
                           </div>
                       @endforeach
                   </div>
                   <button class="artist-registration-btn artist-registration-btn-secondary mt-2" type="button" id="addInstitutionPortfolioLink">Add portfolio link</button>
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="registrationCertificate">Registration Certificate</label>
                    <input class="form-control artist-registration-control" id="registrationCertificate" name="registration_certificate" type="file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="field">
                    <label class="artist-registration-label" for="institutionSupportingDocuments">Supporting Documents</label>
                    <input class="form-control artist-registration-control" id="institutionSupportingDocuments" name="supporting_documents[]" type="file" accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <span class="artist-registration-help">Up to five supporting files, 10 MB each.</span>
                </div>
            </div>
        </section>

        <section class="artist-registration-section">
            <h3 class="artist-registration-section-title">Declaration &amp; Consent</h3>
            <div class="artist-registration-checkbox">
                <input class="form-check-input" id="institutionConsent" type="checkbox" name="consent" required @checked(old('consent'))>
                <label class="artist-registration-label" for="institutionConsent">I confirm the institution information is accurate and I am authorized to submit it. <span>*</span></label>
            </div>
            <div class="artist-registration-checkbox">
                <input class="form-check-input" id="institutionDataConsent" type="checkbox" name="data_processing_consent" required @checked(old('data_processing_consent'))>
                <label class="artist-registration-label" for="institutionDataConsent">I consent to processing the information and documents for registration and assessment. <span>*</span></label>
            </div>
        </section>

        <div class="artist-registration-footer">
            <div class="artist-registration-save-note">A secure password setup link will be sent to the representative email.</div>
            <div class="artist-registration-buttons">
                <button type="reset" class="artist-registration-btn artist-registration-btn-secondary">Reset</button>
                <button type="submit" class="artist-registration-btn artist-registration-btn-primary">Register Institution</button>
            </div>
        </div>
    </form>
</div>
</main>

{{-- Registration Success Confirmation --}}
<div id="registrationSuccess" class="artist-reg-success-overlay" role="dialog" aria-modal="true" aria-labelledby="successTitle" @if (!session('registration_success')) hidden @endif>
    <div class="artist-reg-success-modal">
        <div class="artist-reg-success-icon" aria-hidden="true">✓</div>
        <div class="artist-reg-success-kicker">REGISTRATION SUCCESSFUL</div>
        <h2 id="successTitle">Thank you for the registration.</h2>
        <p>{{ session('registration_success') }}</p>
        <button type="button" id="successCloseBtn" class="artist-registration-btn artist-registration-btn-primary">Continue</button>
    </div>
</div>



<!-- Registration Preparation Modal -->
<div class="modal fade"
     id="artistDisclaimerModal"
     tabindex="-1"
     aria-labelledby="artistDisclaimerModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white p-3 p-md-4">
                <div>
                    <h5 class="modal-title fw-bold"
                        id="artistDisclaimerModalLabel">
                        <i class="bi bi-clipboard-check me-2"></i>
                        Registration Preparation Guide
                    </h5>
                    <p class="small text-white-50 mb-0 mt-1">
                        BRICS Artist Registry · Please read before applying
                    </p>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-3 p-md-4">

                <p class="text-secondary">
                    Keep the relevant information and documents ready before
                    starting your registration. Requirements may differ
                    depending on whether you are registering as an individual
                    artist, an artist group or a cultural institution.
                </p>

                <!-- General file requirements -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-file-earmark-check text-primary me-2"></i>
                            File Upload Guidelines
                        </h6>

                        <div class="row g-3">

                            <div class="col-12 col-sm-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-file-earmark-image fs-4 text-primary"></i>
                                    <div>
                                        <div class="fw-semibold">Photo / Logo</div>
                                        <div class="small text-secondary">
                                            JPG or JPEG, PNG
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-file-earmark-pdf fs-4 text-danger"></i>
                                    <div>
                                        <div class="fw-semibold">Documents / Certificates</div>
                                        <div class="small text-secondary">
                                            PDF, JPG, JPEG or PNG where accepted
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-hdd fs-4 text-primary"></i>
                                    <div>
                                        <div class="fw-semibold">File Size</div>
                                        <div class="small text-secondary">
                                            Supporting documents: maximum 10 MB per file
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-files fs-4 text-primary"></i>
                                    <div>
                                        <div class="fw-semibold">Multiple Documents</div>
                                        <div class="small text-secondary">
                                            Up to 5 supporting files, where applicable
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="alert alert-warning small mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            The limits above reflect the current supporting-document
                            guidance. Check the individual upload field for its
                            specific requirements. Do not upload a file type that
                            the field does not accept.
                        </div>
                    </div>
                </div>

                <!-- Individual Artist -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-person-badge me-2"></i>
                        1. Individual Artist
                    </h6>

                    <ul class="list-group list-group-flush border rounded-3">
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Personal details and contact information
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Profile photograph in JPG/JPEG or PNG format
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Identity document in PDF, JPG, JPEG or PNG format
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Art form, relevant practice details and experience
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Award details and certificate, if applicable
                        </li>
                    </ul>
                </div>

                <!-- Artist Group -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-people me-2"></i>
                        2. Artist Group
                    </h6>

                    <ul class="list-group list-group-flush border rounded-3">
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Group details and contact information
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Group profile photograph in JPG/JPEG or PNG format
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Identity document and relevant supporting documents
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Group members' details, as requested by the form
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Award details and certificates, if applicable
                        </li>
                    </ul>
                </div>

                <!-- Cultural Institution -->
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-building me-2"></i>
                        3. Cultural Institution
                    </h6>

                    <ul class="list-group list-group-flush border rounded-3">
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Institution details and contact information
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Institution logo in JPG/JPEG or PNG format
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Identity document and authorisation letter, where required
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Brochure/profile and registration certificate, where applicable
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check2-circle text-success me-2"></i>
                            Supporting documents and award certificates, if applicable
                        </li>
                    </ul>
                </div>

                <!-- Before submission -->
                <div class="alert alert-success border-0 rounded-3 mb-4">
                    <h6 class="fw-bold">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Before You Submit
                    </h6>

                    <ul class="mb-0 ps-3">
                        <li>Check that all mandatory fields are completed.</li>
                        <li>Verify your contact details and other information.</li>
                        <li>Ensure uploaded files are clear, readable and correct.</li>
                        <li>Check the allowed file format and size for each upload.</li>
                        <li>Ensure all submitted information and documents are accurate and authentic.</li>
                    </ul>
                </div>

                <!-- Disclaimer -->
                <div class="border rounded-3 p-3">
                    <h6 class="fw-bold mb-2">
                        <i class="bi bi-shield-exclamation text-warning me-2"></i>
                        Important Registration Notice
                    </h6>

                    <p class="small text-secondary mb-2">
                        Registration is subject to applicable eligibility
                        requirements and verification by the designated
                        Nodal Agency of the selected country.
                    </p>

                    <p class="small text-secondary mb-0">
                        Submission does not guarantee employment, selection
                        for events, funding, accreditation or endorsement.
                        It does not transfer ownership of your copyright.
                        Acceptance and listing are subject to review by the
                        designated Nodal Agency.
                    </p>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer bg-light">
                <button type="button"
                        class="btn btn-primary px-4"
                        data-bs-dismiss="modal">
                    <i class="bi bi-check2 me-1"></i>
                    Got It
                </button>
            </div>

        </div>
    </div>
</div>


@endsection

@push('scripts')

   <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui-pro/dist/js/coreui.bundle.min.js"></script>
 
 
<script>


  // Email OTP verification for all three registration forms.
  (function () {
    'use strict';

    var OTP_PURPOSE = 'registration';
    var RESEND_SECONDS = 60;
    var CSRF_TOKEN = @json(csrf_token());
    var SEND_URL = @json(route('email.otp.send'));
    var RESEND_URL = @json(route('email.otp.resend'));
    var VERIFY_URL = @json(route('email.otp.verify'));
    var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    function formOf(el) {
      return $(el).closest('form');
    }

    function showMsg(form, text, isError) {
      form.find('.otpMsg').text(text || '').css('color', isError ? '#c43d2d' : '#1e7e34');
    }

    function clearTimer(form) {
      var timer = form.data('otpTimer');
      if (timer) {
        clearInterval(timer);
        form.data('otpTimer', null);
      }
    }

    function startTimer(form, seconds) {
      clearTimer(form);
      var remaining = seconds;
      form.find('.timer').removeClass('d-none').text('Resend OTP will be enabled in ' + remaining + ' seconds');
      form.find('.reSendBtn').addClass('d-none');
      var timer = setInterval(function () {
        remaining -= 1;
        if (remaining <= 0) {
          clearTimer(form);
          form.find('.timer').addClass('d-none').text('');
          form.find('.reSendBtn').removeClass('d-none').prop('disabled', false).text('Resend OTP');
        } else {
          form.find('.timer').text('Resend OTP will be enabled in ' + remaining + ' seconds');
        }
      }, 1000);
      form.data('otpTimer', timer);
    }

    function updateSubmitState(form) {
      var verified = form.data('emailVerified') === true || form.find('.emailId').attr('data-verified') === '1';
      form.find('button[type="submit"]').prop('disabled', !verified);
    }

    function setVerified(form) {
      form.data('emailVerified', true);
      clearTimer(form);
      form.find('.otpSendBtn, .otpVerifyBtn, .reSendBtn, .edit_mail').addClass('d-none');
      form.find('.email_otp').addClass('d-none').val('');
      form.find('.timer').addClass('d-none').text('');
      form.find('.emailId').prop('readonly', true).attr('data-verified', '1');
      form.find('.verifyEmail').removeClass('d-none');
      showMsg(form, 'Email verified successfully. You can now submit the registration.', false);
      updateSubmitState(form);
    }

    function resetVerification(form) {
      form.data('emailVerified', false);
      form.find('.emailId').attr('data-verified', '0');
      form.find('.verifyEmail').addClass('d-none');
      updateSubmitState(form);
    }

    function otpPayload(form, otp) {
      var payload = {
        _token: CSRF_TOKEN,
        email: $.trim(form.find('.emailId').val() || ''),
        purpose: OTP_PURPOSE
      };
      if (otp !== undefined) {
        payload.otp = otp;
      }
      return payload;
    }

    function handleOtpError(form, xhr, button, defaultText) {
      var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to process the OTP request. Please try again.';
      if (xhr.responseJSON && xhr.responseJSON.errors) {
        var first = Object.values(xhr.responseJSON.errors)[0];
        if (first && first[0]) {
          message = first[0];
        }
      }
      showMsg(form, message, true);
      if (button) {
        button.prop('disabled', false).text(defaultText);
      }
    }

    // Send OTP
    $(document).on('click', '.otpSendBtn', function () {
      var button = $(this);
      var form = formOf(this);
      var email = $.trim(form.find('.emailId').val() || '');
      showMsg(form, '', false);
      if (!EMAIL_PATTERN.test(email)) {
        showMsg(form, 'Please enter a valid email address.', true);
        return;
      }
      $.ajax({
        type: 'POST',
        url: SEND_URL,
        data: otpPayload(form),
        beforeSend: function () {
          button.prop('disabled', true).text('Sending OTP...');
        },
        success: function (res) {
          if (res && res.success) {
            form.find('.otpSendBtn').addClass('d-none');
            form.find('.otpVerifyBtn').removeClass('d-none').prop('disabled', false).text('Verify OTP');
            form.find('.email_otp').removeClass('d-none').val('').trigger('focus');
            form.find('.edit_mail').removeClass('d-none');
            form.find('.emailId').prop('readonly', true);
            resetVerification(form);
            showMsg(form, res.message || 'OTP has been sent to your email address.', false);
            startTimer(form, RESEND_SECONDS);
          } else {
            showMsg(form, (res && res.message) || 'Unable to send OTP.', true);
            button.prop('disabled', false).text('Send OTP');
          }
        },
        error: function (xhr) {
          handleOtpError(form, xhr, button, 'Send OTP');
        }
      });
    });

    // Resend OTP
    $(document).on('click', '.reSendBtn', function () {
      var button = $(this);
      var form = formOf(this);
      var email = $.trim(form.find('.emailId').val() || '');
      if (!EMAIL_PATTERN.test(email)) {
        showMsg(form, 'Please enter a valid email address.', true);
        return;
      }
      $.ajax({
        type: 'POST',
        url: RESEND_URL,
        data: otpPayload(form),
        beforeSend: function () {
          button.prop('disabled', true).text('Resending OTP...');
        },
        success: function (res) {
          if (res && res.success) {
            form.find('.otpVerifyBtn').removeClass('d-none').prop('disabled', false).text('Verify OTP');
            form.find('.email_otp').removeClass('d-none').val('').trigger('focus');
            resetVerification(form);
            showMsg(form, res.message || 'OTP has been resent to your email address.', false);
            startTimer(form, RESEND_SECONDS);
          } else {
            showMsg(form, (res && res.message) || 'Unable to resend OTP.', true);
            button.prop('disabled', false).text('Resend OTP');
          }
        },
        error: function (xhr) {
          handleOtpError(form, xhr, button, 'Resend OTP');
        }
      });
    });

    // Edit email after OTP was sent (requires a fresh OTP verification)
    $(document).on('click', '.edit_mail', function () {
      var form = formOf(this);
      clearTimer(form);
      form.find('.emailId').prop('readonly', false).trigger('focus');
      form.find('.otpSendBtn').removeClass('d-none').prop('disabled', false).text('Send OTP');
      form.find('.otpVerifyBtn, .reSendBtn, .edit_mail').addClass('d-none');
      form.find('.email_otp').addClass('d-none').val('');
      form.find('.timer').addClass('d-none').text('');
      showMsg(form, '', false);
      resetVerification(form);
    });

    // Verify OTP
    $(document).on('click', '.otpVerifyBtn', function () {
      var button = $(this);
      var form = formOf(this);
      var otp = $.trim(form.find('.email_otp').val() || '');
      if (!/^\d{6}$/.test(otp)) {
        showMsg(form, 'Please enter the 6-digit OTP.', true);
        return;
      }
      $.ajax({
        type: 'POST',
        url: VERIFY_URL,
        data: otpPayload(form, otp),
        beforeSend: function () {
          button.prop('disabled', true).text('Verifying...');
        },
        success: function (res) {
          if (res && res.success) {
            setVerified(form);
          } else {
            form.find('.email_otp').val('');
            showMsg(form, (res && res.message) || 'Invalid OTP.', true);
            button.prop('disabled', false).text('Verify OTP');
          }
        },
        error: function (xhr) {
          form.find('.email_otp').val('');
          handleOtpError(form, xhr, button, 'Verify OTP');
        }
      });
    });

    // Block submission until the email is verified (the server re-checks this too).
    $(document).on('submit', 'form[data-registration-panel]', function (event) {
      var form = $(this);
      var verified = form.data('emailVerified') === true || form.find('.emailId').attr('data-verified') === '1';
      if (!verified) {
        event.preventDefault();
        showMsg(form, 'Please verify your email address with the OTP before submitting the registration.', true);
        form.find('.emailId').trigger('focus');
      }
    });

    // Initial state: submit stays disabled until verification completes.
    $(function () {
      $('form[data-registration-panel]').each(function () {
        updateSubmitState($(this));
      });
    });
  })();

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    var registrationTabs = document.querySelectorAll('[data-registration-tab]');
    var registrationPanels = document.querySelectorAll('[data-registration-panel]');

    function selectRegistrationType(selectedType) {
        registrationTabs.forEach(function (tab) {
            var selected = tab.getAttribute('data-registration-tab') === selectedType;
            tab.classList.toggle('active', selected);
            tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            tab.setAttribute('tabindex', selected ? '0' : '-1');
        });
        registrationPanels.forEach(function (panel) {
            var selected = panel.getAttribute('data-registration-panel') === selectedType;
            panel.hidden = !selected;
            panel.classList.toggle('artist-registration-form-hidden', !selected);
        });
    }

    registrationTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            selectRegistrationType(tab.getAttribute('data-registration-tab'));
        });
    });
    selectRegistrationType(@json($activeRegistrationType));

    var masterUrl = @json(url('/artist-registration/countries'));
    function replaceOptions(select, records, placeholder, selectedValues) {
        select.replaceChildren();
        if (!select.multiple) select.add(new Option(placeholder, ''));
        records.forEach(function (record) {
            var option = new Option(record.name, record.id);
            option.selected = selectedValues.map(String).indexOf(String(record.id)) !== -1;
            select.add(option);
        });
    }
    var pendingSubgenre = @json(old('subgenre_id', ''));
    function loadMasterOptions(select, url, placeholder, selectedValues) {
        select.disabled = true;
        var requestId = (select.dataset.requestId = String(Number(select.dataset.requestId || 0) + 1));
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (response) {
                if (!response.ok) throw new Error('Master data request failed with status ' + response.status);
                return response.json();
            })
            .then(function (records) {
                if (select.dataset.requestId !== requestId) return;
                replaceOptions(select, records, placeholder, selectedValues);
                select.disabled = false;
                select.dispatchEvent(new Event('change'));
            })
            .catch(function (error) {
                if (select.dataset.requestId !== requestId) return;
                select.replaceChildren(new Option('Unable to load options. Change country to retry.', ''));
                select.disabled = true;
                console.error(error);
            });
    }

    document.querySelectorAll('[data-country-select]').forEach(function (country) {
        var form = country.closest('form');
        var region = form.querySelector('[data-region-select]');
        var artForms = form.querySelector('[data-art-forms-select], [data-art-form-select]');
        var subgenre = form.querySelector('[data-subgenre-select]');
        var previousRegion = @json(old('region_id', ''));
        var previousArtForm = @json(old('art_form_id', ''));
        var previousArtForms = @json(old('art_forms', []));
        function refreshCountryData() {
            var countryId = country.value;
            region.replaceChildren(new Option(countryId ? 'Loading regions...' : 'Select country first', ''));
            if (subgenre) {
                subgenre.replaceChildren(new Option('Select main art form first', ''));
                subgenre.disabled = true;
            }
            if (!countryId) {
                if (artForms) artForms.replaceChildren(new Option('Select country first', ''));
                return;
            }
            loadMasterOptions(region, masterUrl + '/' + encodeURIComponent(countryId) + '/regions', 'Select region', [previousRegion]);
            if (artForms) {
                loadMasterOptions(artForms, masterUrl + '/' + encodeURIComponent(countryId) + '/art-forms', 'Select art form(s)', artForms.id === 'artForm' ? [previousArtForm] : previousArtForms);
            }
        }
        country.addEventListener('change', function () {
            previousRegion = '';
            previousArtForm = '';
            previousArtForms = [];
            pendingSubgenre = '';
            refreshCountryData();
        });
        refreshCountryData();
    });

    var mainArtForm = document.getElementById('artForm');
    var subgenreSelect = document.getElementById('subgenre');
    if (mainArtForm && subgenreSelect) {
        function refreshSubgenres(selectedId) {
            var countryId = document.getElementById('country').value;
            if (!countryId || !mainArtForm.value) {
                subgenreSelect.replaceChildren(new Option('Select main art form first', ''));
                subgenreSelect.disabled = true;
                return;
            }
            loadMasterOptions(
                subgenreSelect,
                masterUrl + '/' + encodeURIComponent(countryId) + '/art-forms/' + encodeURIComponent(mainArtForm.value) + '/subgenres',
                'Select sub-genre / tradition',
                [selectedId]
            );
        }
        mainArtForm.addEventListener('change', function () {
            refreshSubgenres(pendingSubgenre);
            pendingSubgenre = '';
            updateOtherArtForm();
        });
    }

    function toggleOtherField(select, wrapper, input, multiple) {
        if (!select || !wrapper || !input) return false;
        var hasOther = Array.from(select.selectedOptions).some(function (option) {
            return option.text.trim().toLowerCase() === 'other';
        });
        wrapper.hidden = !hasOther;
        input.required = hasOther;
        return hasOther;
    }
    var customIndividualArtForm = document.getElementById('customArtForm');
    var customIndividualArtField = document.getElementById('customArtFormField');
    function updateOtherArtForm() {
        toggleOtherField(mainArtForm, customIndividualArtField, customIndividualArtForm, false);
    }
    if (mainArtForm) {
        mainArtForm.addEventListener('change', updateOtherArtForm);
        updateOtherArtForm();
    }
    [
        ['groupArtForms', 'groupCustomArtFormField', 'groupCustomArtForm'],
        ['institutionArtForms', 'institutionCustomArtFormField', 'institutionCustomArtForm']
    ].forEach(function (ids) {
        var select = document.getElementById(ids[0]);
        var wrapper = document.getElementById(ids[1]);
        var input = document.getElementById(ids[2]);
        if (!select) return;
        var update = function () { toggleOtherField(select, wrapper, input, true); };
        select.addEventListener('change', update);
        update();
    });

    var groupTypeSelect = document.getElementById('groupType');
    var customGroupTypeField = document.getElementById('customGroupTypeField');
    var customGroupType = document.getElementById('customGroupType');
    if (groupTypeSelect) {
        var updateGroupType = function () {
            var selected = groupTypeSelect.selectedOptions[0];
            var isOther = selected && selected.text.trim().toLowerCase() === 'other';
            customGroupTypeField.hidden = !isOther;
            customGroupType.required = Boolean(isOther);
        };
        groupTypeSelect.addEventListener('change', updateGroupType);
        updateGroupType();
    }

    function setupLanguageSearch(searchId, selectId) {
        var search = document.getElementById(searchId);
        var select = document.getElementById(selectId);
        if (!search || !select) return;

        search.addEventListener('input', function () {
            var term = search.value.trim().toLowerCase();
            Array.from(select.options).forEach(function (option) {
                option.hidden = !option.selected && !option.text.toLowerCase().includes(term);
            });
        });
    }

    setupLanguageSearch('languageSearch', 'languageIds');
    setupLanguageSearch('groupLanguageSearch', 'groupLanguageIds');
    setupLanguageSearch('institutionLanguageSearch', 'institutionLanguageIds');

    function setupAwards(containerId, buttonId, prefix) {
        var container = document.getElementById(containerId);
        var addButton = document.getElementById(buttonId);
        if (!container || !addButton) return;

        addButton.addEventListener('click', function () {
            var rows = container.querySelectorAll('[data-award-row]');
            if (rows.length >= 10) {
                alert('You can add a maximum of 10 awards.');
                return;
            }

            var index = Date.now();
            var row = document.createElement('div');
            row.className = 'border rounded p-3 mb-3';
            row.setAttribute('data-award-row', '');
            row.innerHTML = `
                <div class="row g-3">
                    <div class="field">
                        <label class="artist-registration-label">Award</label> <select class="form-select artist-registration-control" name="awards[${index}][award_id]">
                            <option value="">Select award</option>
                            ${Array.from(container.querySelector('select[name*="[award_id]"]').options).slice(1).map(option => `<option value="${option.value}">${option.textContent}</option>`).join('')}
                        </select>
                    </div>
                    <div class="field">
                        <label class="artist-registration-label">Award Year</label>
                        <input class="form-control artist-registration-control" type="number" min="1800" max="${new Date().getFullYear()}" name="awards[${index}][award_year]">
                    </div>
                    <div class="field">
                        <label class="artist-registration-label">Certificate</label>
                        <input class="form-control artist-registration-control" type="file" accept=".pdf,.jpg,.jpeg,.png" name="awards[${index}][certificate]">
                    </div>
                    <div class="field full">
                        <label class="artist-registration-label">Description</label>
                        <textarea class="form-control artist-registration-control" maxlength="2000" name="awards[${index}][description]"></textarea>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger mt-2" data-remove-award>Remove award</button>`;
            container.appendChild(row);
        });

        container.addEventListener('click', function (event) {
            var remove = event.target instanceof Element ? event.target.closest('[data-remove-award]') : null;
            if (remove && container.querySelectorAll('[data-award-row]').length > 1) {
                remove.closest('[data-award-row]').remove();
            }
        });
    }

    setupAwards('individualAwardsContainer', 'addIndividualAward', 'individual');
    setupAwards('groupAwardsContainer', 'addGroupAward', 'group');
    setupAwards('institutionAwardsContainer', 'addInstitutionAward', 'institution');

    function setupPortfolioLinks(containerId, buttonId) {
        var container = document.getElementById(containerId);
        var addButton = document.getElementById(buttonId);
        if (!container || !addButton) return;

        addButton.addEventListener('click', function () {
            if (container.querySelectorAll('[data-portfolio-row]').length >= 10) {
                alert('You can add a maximum of 10 portfolio links.');
                return;
            }
            var row = document.createElement('div');
            row.className = 'input-group mb-2';
            row.setAttribute('data-portfolio-row', '');

            var input = document.createElement('input');
            input.className = 'form-control artist-registration-control';
            input.name = 'portfolio_links[]';
            input.type = 'url';
            input.maxLength = 2048;
            input.placeholder = 'https://';

            var remove = document.createElement('button');
            remove.className = 'btn btn-outline-secondary';
            remove.type = 'button';
            remove.textContent = 'Remove';
            remove.setAttribute('data-remove-portfolio', '');

            row.append(input, remove);
            container.appendChild(row);
        });

        container.addEventListener('click', function (event) {
            var target = event.target;
            var remove = target instanceof Element ? target.closest('[data-remove-portfolio]') : null;
            if (remove && container.querySelectorAll('[data-portfolio-row]').length > 1) {
                remove.closest('[data-portfolio-row]').remove();
            }
        });
    }

    setupPortfolioLinks('individualPortfolioLinks', 'addIndividualPortfolioLink');
    setupPortfolioLinks('groupPortfolioLinks', 'addGroupPortfolioLink');
    setupPortfolioLinks('institutionPortfolioLinks', 'addInstitutionPortfolioLink');

    /* ---------- Disclaimer modal ---------- */
    var disclaimer = document.getElementById('registrationDisclaimer');
    var agreeBtn = document.getElementById('agreeBtn');
    var disagreeBtn = document.getElementById('disagreeBtn');
    var ack = document.getElementById('disclaimerAcknowledgement');
    var err = document.getElementById('disclaimerError');
    var disclaimerAccepted = false;

    // Keep the acknowledgement across refreshes and validation/success redirects.
    // Once accepted, do not show this modal again in the same browser.
    try {
        disclaimerAccepted = window.localStorage.getItem('bricsRegistrationDisclaimerAccepted') === '1';
    } catch (e) {
        disclaimerAccepted = false;
    }
		//disclaimerAccepted = false;
		
    if (disclaimerAccepted) {
        if (disclaimer) {
            disclaimer.style.display = 'none';
            disclaimer.setAttribute('aria-hidden', 'true');
        }
        document.body.classList.remove('artist-reg-disclaimer-active');
    } else {
        document.body.classList.add('artist-reg-disclaimer-active');
        if (disclaimer) {
            disclaimer.style.display = '';
            disclaimer.removeAttribute('aria-hidden');
        }
    }

    if (ack && agreeBtn) {
        agreeBtn.disabled = !ack.checked;
        ack.addEventListener('change', function () {
            agreeBtn.disabled = !ack.checked;
            if (ack.checked && err) err.style.display = 'none';
        });
    }

    if (agreeBtn) {
        agreeBtn.addEventListener('click', function () {
            if (!ack || !ack.checked) {
                if (err) err.style.display = 'block';
                return;
            }
            try {
                window.localStorage.setItem('bricsRegistrationDisclaimerAccepted', '1');
            } catch (e) {
                // Continue even if browser storage is unavailable.
            }
            if (disclaimer) {
                disclaimer.style.display = 'none';
                disclaimer.setAttribute('aria-hidden', 'true');
            }
            document.body.classList.remove('artist-reg-disclaimer-active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    if (disagreeBtn) {
        disagreeBtn.addEventListener('click', function () {
            window.location.href = '{{ url("/") }}';
        });
    } 

    /* ---------- Country confirmation ---------- */
    var countryFlags = {
        'Brazil': '🇧🇷', 'China': '🇨🇳', 'Egypt': '🇪🇬', 'Ethiopia': '🇪🇹',
        'India': '🇮🇳', 'Indonesia': '🇮🇩', 'Iran': '🇮🇷', 'Russia': '🇷🇺',
        'Saudi Arabia': '🇸🇦', 'South Africa': '🇿🇦', 'United Arab Emirates': '🇦🇪',
        'Belarus': '🇧🇾', 'Bolivia': '🇧🇴', 'Cuba': '🇨🇺', 'Kazakhstan': '🇰🇿',
        'Malaysia': '🇲🇾', 'Nigeria': '🇳🇬', 'Thailand': '🇹🇭', 'Uganda': '🇺🇬',
        'Uzbekistan': '🇺🇿', 'Vietnam': '🇻🇳'
    };

    var countrySelect = document.getElementById('country');
    var countryConfirmation = document.getElementById('countryConfirmation');
    var selectedCountryName = document.getElementById('selectedCountryName');
    var selectedCountryFlag = document.getElementById('selectedCountryFlag');
    var selectedCountryStrip = document.getElementById('selectedCountryStrip');
    var selectedStripFlag = document.getElementById('selectedStripFlag');
    var selectedStripName = document.getElementById('selectedStripName');

    function updateCountryUI() {
        if (!countrySelect) return;
        var val = countrySelect.value;
        if (val) {
            var countryName = countrySelect.selectedOptions[0].text;
            var flag = countryFlags[countryName] || '🌐';
            if (countryConfirmation) countryConfirmation.hidden = false;
            if (selectedCountryName) selectedCountryName.textContent = countryName;
            if (selectedCountryFlag) selectedCountryFlag.textContent = flag;
            if (selectedCountryStrip) selectedCountryStrip.hidden = false;
            if (selectedStripFlag) selectedStripFlag.textContent = flag;
            if (selectedStripName) selectedStripName.textContent = countryName;
        } else {
            if (countryConfirmation) countryConfirmation.hidden = true;
            if (selectedCountryStrip) selectedCountryStrip.hidden = true;
        }
    }

    if (countrySelect) {
        countrySelect.addEventListener('change', updateCountryUI);
        updateCountryUI();
    }

    /* ---------- Bio word counter ---------- */
    var bio = document.getElementById('bio');
    var counter = document.getElementById('bioCount');
    if (bio && counter) {
        function updateBioCount() {
            var text = bio.value.trim();
            var words = text ? text.split(/\s+/).length : 0;
            counter.textContent = words;
            counter.style.color = words > 300 ? '#c43d2d' : '';
            bio.setCustomValidity(words > 300 ? 'The biography may not exceed 300 words.' : '');
        }
        bio.addEventListener('input', updateBioCount);
        updateBioCount();
    }

    /* ---------- Upload file format and size validation ---------- */
    var maxUploadBytes = 10 * 1024 * 1024; // 10 MB per file
    document.querySelectorAll('#artistRegistrationForm input[type="file"], #groupRegistrationForm input[type="file"], #institutionRegistrationForm input[type="file"]').forEach(function (input) {
        var error = document.createElement('div');
        error.className = 'registration-file-error';
        error.setAttribute('aria-live', 'polite');
        input.insertAdjacentElement('afterend', error);

        input.addEventListener('change', function () {
            error.textContent = '';
            input.setCustomValidity('');

            var allowed = (input.getAttribute('accept') || '')
                .split(',')
                .map(function (item) { return item.trim().toLowerCase(); })
                .filter(Boolean);
            var files = Array.from(input.files || []);

            if (input.name === 'supporting_documents[]' && files.length > 5) {
                error.textContent = 'You can upload a maximum of five supporting documents.';
                input.setCustomValidity(error.textContent);
                input.value = '';
                return;
            }

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var extension = '.' + (file.name.split('.').pop() || '').toLowerCase();
                var mime = (file.type || '').toLowerCase();
                var accepted = !allowed.length || allowed.some(function (type) {
                    if (type.charAt(0) === '.') return extension === type;
                    if (type.endsWith('/*')) return mime.indexOf(type.slice(0, -1)) === 0;
                    return mime === type;
                });

                if (!accepted) {
                    error.textContent = 'Unsupported file format. Allowed: ' + allowed.join(', ') + '.';
                    input.setCustomValidity(error.textContent);
                    input.value = '';
                    return;
                }

                if (file.size > maxUploadBytes) {
                    error.textContent = 'Each file must be 10 MB or smaller.';
                    input.setCustomValidity(error.textContent);
                    input.value = '';
                    return;
                }
            }
        });
    });

    /* ---------- Registration success confirmation ---------- */
    var successOverlay = document.getElementById('registrationSuccess');
    var successCloseBtn = document.getElementById('successCloseBtn');

    if (successOverlay && !successOverlay.hidden) {
        document.body.style.overflow = 'hidden';
        if (successCloseBtn) successCloseBtn.focus();
    }

    if (successCloseBtn) {
        successCloseBtn.addEventListener('click', function () {
            var selectedCountry = countrySelect ? countrySelect.value : '';
            if (selectedCountry) {
                try { sessionStorage.setItem('bricsRegistrationCountry', selectedCountry); } catch (e) {}
            }
            // Change route when nodal pending page exists
            window.location.href = '{{ url("/") }}';
        });
    }

    if (successOverlay) {
        successOverlay.addEventListener('click', function (event) {
            if (event.target === successOverlay) {
                successOverlay.hidden = true;
                document.body.style.overflow = '';
            }
        });
    }
});


</script>
@endpush
