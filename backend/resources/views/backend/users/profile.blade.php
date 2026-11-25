@extends('backend.layouts.app')

@section('title', 'Update Profile')

@section('content')
    <div class="container-fluid profile-page">
        <header class="profile-heading">
            <div>
                <p class="profile-eyebrow mb-1">ACCOUNT SETTINGS</p>
                <h1 class="profile-title mb-1">Update profile</h1>
                <p class="profile-subtitle mb-0">Keep your personal and contact information up to date.</p>
            </div>
            @if ($user->regno)
                <div class="profile-reference">
                    <span>Registration number</span>
                    <strong>{{ $user->regno }}</strong>
                </div>
            @endif
        </header>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">Please review the highlighted fields and try again.</div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <section class="profile-section" aria-labelledby="personal-details-title">
                <div class="profile-section-heading">
                    <div>
                        <h2 id="personal-details-title">Personal details</h2>
                        <p>Use the name and details associated with your portal account.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="firstname">First name <span class="text-danger">*</span></label>
                        <input class="form-control @error('firstname') is-invalid @enderror" id="firstname" name="firstname" type="text" value="{{ old('firstname', $user->firstname) }}" maxlength="100" autocomplete="given-name" required>
                        @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                 
                    <div class="col-md-4">
                        <label class="form-label" for="lastname">Last name <span class="text-danger">*</span></label>
                        <input class="form-control @error('lastname') is-invalid @enderror" id="lastname" name="lastname" type="text" value="{{ old('lastname', $user->lastname) }}" maxlength="100" autocomplete="family-name" required>
                        @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
              
                   
					
                    <div class="col-md-4">
                        <label class="form-label" for="gender">Gender <span class="text-danger">*</span></label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                            <option value="">Select gender</option>
                            @foreach (genderOptions() as $value => $label)
                                <option value="{{ $value }}" @selected((string) old('gender', $user->gender) === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
					
                    <div class="col-md-4">
                        <label class="form-label" for="designation">Designation</label>
                        <input class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" type="text" value="{{ old('designation', $user->designation) }}" maxlength="255" autocomplete="organization-title">
                        @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
					
               
                </div>
            </section>

            <section class="profile-section" aria-labelledby="contact-details-title">
                <div class="profile-section-heading">
                    <div>
                        <h2 id="contact-details-title">Contact and address</h2>
                        <p>These details help the portal keep your account information current.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="email">Email address <span class="text-danger">*</span></label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="255" autocomplete="email" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
					
                    <div class="col-md-4">
                        <label class="form-label" for="mobile">Mobile number <span class="text-danger">*</span></label>
                        <input class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" type="tel" value="{{ old('mobile', $user->mobile) }}" inputmode="numeric" maxlength="10" autocomplete="tel" required>
                        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
					
					
					<div class="col-md-4">
                        <label class="form-label" for="country_id">Country</label>
                        <select class="form-select" id="country_id" disabled aria-describedby="country-help">
                            <option value="">Select country</option>
                            @foreach ($countries as $countryId => $countryName)
                                <option value="{{ $countryId }}" @selected((string) $user->country_id === (string) $countryId)>
                                    {{ $countryName }}
                                </option>
                            @endforeach
                        </select>
                        <small id="country-help" class="text-muted">Country cannot be changed from profile settings.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="region_id">Region / State / Province</label>
                        <select class="form-select @error('region_id') is-invalid @enderror"
                                id="region_id" name="region_id">
                            <option value="">Select Region / State / Province</option>
                            @foreach ($regions as $regionId => $regionName)
                                <option value="{{ $regionId }}"
                                    @selected((string) old('region_id', $user->region_id) === (string) $regionId)>
                                    {{ $regionName }}
                                </option>
                            @endforeach
                        </select>
                        @error('region_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="city">City / Town <span class="text-danger">*</span></label>
                        <input class="form-control @error('city') is-invalid @enderror" id="city" name="city" type="text" value="{{ old('city', $user->city) }}" inputmode="city"autocomplete="" required>
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
					
                    <div class="col-md-4">
                        <label class="form-label" for="pincode">Postal / ZIP Code</label>
                        <input class="form-control @error('pincode') is-invalid @enderror" id="pincode" name="pincode" type="text" value="{{ old('pincode', $user->pincode) }}" inputmode="numeric" maxlength="6" autocomplete="postal-code">
                        @error('pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" maxlength="255" autocomplete="street-address">{{ old('address', $user->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            <div class="profile-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1" aria-hidden="true"></i>Save changes</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .profile-page {
        --profile-ink: #14233f;
        --profile-muted: #68758b;
        --profile-line: #e3e8ef;
        color: var(--profile-ink);
        padding: 0.5rem 0.25rem 1.5rem;
    }

    .profile-heading,
    .profile-section-heading,
    .profile-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .profile-heading {
        align-items: flex-end;
        margin-bottom: 1.5rem;
    }

    .profile-eyebrow {
        color: #526a91;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .profile-title {
        color: var(--color-secondary);
        font-size: 1.65rem;
        font-weight: 700;
    }

    .profile-subtitle,
    .profile-reference span,
    .profile-section-heading p {
        color: var(--profile-muted);
        font-size: 0.875rem;
    }

    .profile-reference {
        display: grid;
        gap: 0.2rem;
        text-align: right;
    }

    .profile-reference strong {
        color: var(--profile-ink);
        font-size: 0.9rem;
    }

    .profile-form {
        background: #fff;
        border: 1px solid var(--profile-line);
        border-radius: 6px;
    }

    .profile-section {
        padding: 1.25rem;
    }

    .profile-section + .profile-section {
        border-top: 1px solid var(--profile-line);
    }

    .profile-section-heading {
        margin-bottom: 1.1rem;
    }

    .profile-section-heading h2 {
        margin: 0 0 0.2rem;
        color: var(--profile-ink);
        font-size: 1.05rem;
        font-weight: 700;
    }

    .profile-section-heading p {
        margin: 0;
    }

    .profile-form .form-label {
        margin-bottom: 0.35rem;
        color: var(--profile-ink);
        font-size: 0.84rem;
        font-weight: 600;
    }

    .profile-actions {
        justify-content: flex-end;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--profile-line);
    }

    @media (max-width: 575.98px) {
        .profile-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .profile-reference {
            text-align: left;
        }

        .profile-section {
            padding: 1rem;
        }

        .profile-actions {
            padding: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const stateSelect = document.getElementById('state');
        const districtSelect = document.getElementById('district');
        const districtsUrl = @json(route('common.districts-by-state'));

        stateSelect.addEventListener('change', async () => {
            districtSelect.replaceChildren(new Option('Select district', ''));
            districtSelect.disabled = !stateSelect.value;

            if (!stateSelect.value) {
                return;
            }

            try {
                const response = await fetch(`${districtsUrl}?state_code=${encodeURIComponent(stateSelect.value)}`, {
                    headers: { Accept: 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('District lookup failed');
                }

                const districts = await response.json();
                Object.entries(districts).forEach(([code, name]) => {
                    districtSelect.add(new Option(name, code));
                });
                districtSelect.disabled = false;
            } catch (error) {
                districtSelect.add(new Option('Unable to load districts', ''));
            }
        });
    })();
</script>
@endpush
