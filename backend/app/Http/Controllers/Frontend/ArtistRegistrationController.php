<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ArtistRegistrationConfirmation;
use App\Mail\NodalOfficerNewRegistration;
use App\Mail\SetArtistPasswordMail;
use App\Models\ArtForm;
use App\Models\ArtGroupType;
use App\Models\ArtistRegistration;
use App\Models\ArtLanguage;
use App\Models\ArtRegion;
use App\Models\Country;
use App\Models\InstitutionType;
use App\Models\Role;
use App\Models\User;
use App\Services\EmailOtpService;
use App\Services\EmailService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ArtistRegistrationController extends Controller
{
    private const ROLE_IDS = [
        'individual' => 5,
        'group' => 6,
        'institutional' => 7,
    ];

    private const REGISTRATION_TYPE_LABELS = [
        'individual' => 'Artist (Individual)',
        'group' => 'Artist Group',
        'institutional' => 'Institute / Organization',
    ];

    /**
     * users.role_id of the Country Nodal Officer / Nodal Agency accounts.
     */
    private const NODAL_OFFICER_ROLE_ID = 8;

    public function __construct(
        private readonly EmailOtpService $otpService,
        private readonly EmailService $emailService
    ) {
    }

    public function artistregistration(): View
    {
        $returnedRegistrations = Auth::check()
            ? ArtistRegistration::query()
                ->where('user_id', Auth::id())
                ->where('status', ArtistRegistration::STATUS_RETURNED)
                ->latest()
                ->get()
            : collect();

       $countries = Country::query()
    ->where('status', 1)
    ->orderBy('name')
    ->get(['id', 'name', 'phone_code']);
        $groupTypes = ArtGroupType::query()->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $institutionTypes = InstitutionType::query()->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $languages = ArtLanguage::query()->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $awardMasters = DB::table('art_award_masters')
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedCountry = Country::query()
            ->where('status', 1)
            ->where('type', 1)
            ->find(old('country_id'));
        $mainArtForms = $selectedCountry
            ? $this->mainArtFormsQuery((int) $selectedCountry->id)->get(['id', 'name'])
            : collect();
        $selectedArtForm = old('art_form_id');
        $subgenres = $selectedArtForm
            ? $this->subgenresQuery((int) ($selectedCountry?->id ?? 0), (int) $selectedArtForm)->get(['id', 'name'])
            : collect();

        return view('frontend.artist-registration', compact(
            'returnedRegistrations',
            'countries',
            'groupTypes',
            'institutionTypes',
            'languages',
            'awardMasters',
            'mainArtForms',
            'subgenres'
        ));
    }

    public function regions(Country $country): JsonResponse
    {
        abort_unless((int) $country->status === 1 && (int) $country->type === 1, 404);

        return response()->json(
            ArtRegion::query()
                ->where('country_id', $country->id)
                ->where('status', 1)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function artForms(Country $country): JsonResponse
    {
        abort_unless((int) $country->status === 1 && (int) $country->type === 1, 404);

        return response()->json($this->mainArtFormsQuery((int) $country->id)->get(['id', 'name']));
    }

    public function subgenres(Country $country, ArtForm $artForm): JsonResponse
    {
        abort_unless((int) $country->status === 1 && (int) $country->type === 1, 404);
        abort_unless(
            $this->mainArtFormsQuery((int) $country->id)->whereKey($artForm->id)->exists(),
            404
        );

        return response()->json(
            $this->subgenresQuery((int) $country->id, (int) $artForm->id)->get(['id', 'name'])
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        // The email address must be verified via OTP before the registration
        // can be submitted. This server-side check cannot be bypassed by
        // posting the form directly.
        // Skipped when email OTP is switched off (config/otp.php, OTP_EMAIL_ENABLED).
        if (config('otp.email_enabled') && ! $this->otpService->isVerified((string) $request->input('email'), EmailOtpService::PURPOSE_REGISTRATION)) {
            return back()->withErrors([
                'email' => 'Please verify your email address with the OTP sent to it before submitting the registration.',
            ])->withInput();
        }

        $type = (string) $request->input('registration_type');
        $rules = [
            'registration_type' => ['required', Rule::in(array_keys(self::ROLE_IDS))],
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')->where('status', 1)->where('type', 1)],
            'region_id' => [
                'required',
                'integer',
                Rule::exists('art_regions', 'id')->where(
                    fn ($query) => $query
                        ->where('status', 1)
                        ->where('country_id', $request->input('country_id'))
                ),
            ],
            'email' => ['required', 'email', 'max:255'],
             'phone' => ['nullable', 'string', 'regex:/\A[0-9]{6,15}\z/', 'max:15'],
             'mailing_address' => ['nullable', 'string', 'max:2000', 'regex:/\A[\pL\pM\pN\s.,#\/’()&:-\x27]+\z/u'],
             'postal' => ['nullable', 'string', 'max:12', 'regex:/\A[\pL\pN][\pL\pN -]{0,11}\z/u'],
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'language_ids' => ['nullable', 'array', 'max:50'],
            'language_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('art_languages', 'id')->where('status', 1),
            ],
            'portfolio_links' => ['nullable', 'array', 'max:10'],
            'portfolio_links.*' => ['nullable', 'url', 'max:2048'],
            'awards' => ['nullable', 'array', 'max:10'],
            'awards.*.award_id' => ['nullable', 'integer', Rule::exists('art_award_masters', 'id')->where('status', 1)],
            'awards.*.award_year' => ['nullable', 'integer', 'between:1800,'.now()->year],
            'awards.*.certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'awards.*.description' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
	/* 	dd(
		$request->input('language_ids'),
		$request->all()
	); */

        if ($type === 'individual') {
            $rules += [
                'full_name' => ['required', 'string', 'min:2', 'max:255', 'regex:/\A[\pL\pM]+(?:[ .’\x27-][\pL\pM]+)*\z/u'],
                'city' => ['required', 'string', 'min:2', 'max:100', 'regex:/\A[\pL\pM]+(?:[ .’\x27-][\pL\pM]+)*\z/u'],
                'date_of_birth' => [
                    'required',
                    'date',
                    'before_or_equal:'.now()->subYears(18)->toDateString(),
                ],
                'gender' => ['nullable', Rule::in(['1', '2', '3'])],
                'art_form_id' => [
                    'required',
                    'integer',
                    Rule::exists('art_forms', 'id')->where(
                        fn ($query) => $query
                            ->where('status', 1)
                            ->where('parent_id', 0)
                            ->where('country_id', $request->input('country_id'))
                    ),
                ],
                'subgenre_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('art_forms', 'id')->where(
                        fn ($query) => $query
                            ->where('status', 1)
                            ->where('parent_id', $request->input('art_form_id'))
                            ->where('country_id', $request->input('country_id'))
                    ),
                ],
                'custom_art_form' => ['nullable', 'string', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'custom_subgenre' => ['nullable', 'string', 'max:255'],
                'years_of_practice' => ['nullable', 'integer', 'between:0,100'],
                'teacher_guru' => ['nullable', 'string', 'max:255', 'regex:/\A[\pL\pM]+(?:[ .’\x27-][\pL\pM]+)*\z/u'],
                'biography' => [
                    'nullable',
                    'string',
                    'max:2000',
                    'not_regex:/[<>]/u',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        if ($value !== null && preg_match_all("/[\\p{L}\\p{N}]+(?:['-][\\p{L}\\p{N}]+)*/u", $value) > 300) {
                            $fail('The biography may not exceed 300 words.');
                        }
                    },
                ],
                'institutional_affiliation' => ['nullable', 'string', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'public_profile' => ['nullable', 'accepted'],
            ];
        } elseif ($type === 'group') {
            $rules += [
                'data_processing_consent' => ['accepted'],
                'full_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'group_type_id' => [
                    'required',
                    'integer',
                    Rule::exists('art_group_types', 'id')->where('status', 1),
                ],
                'custom_group_type' => ['nullable', 'string', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'district_city' => ['required', 'string', 'min:2', 'max:100', 'regex:/\A[\pL\pM]+(?:[ .’\x27-][\pL\pM]+)*\z/u'],
                'establishment_year' => ['nullable', 'integer', 'between:1800,'.now()->year],
                'registration_number' => ['nullable', 'string', 'max:60', 'regex:/\A[\pL\pN][\pL\pN ._\/-]*\z/u'],
                'art_forms' => ['required', 'array', 'min:1', 'max:25'],
                'art_forms.*' => [
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists('art_forms', 'id')->where(
                        fn ($query) => $query
                            ->where('status', 1)
                            ->where('parent_id', 0)
                            ->where('country_id', $request->input('country_id'))
                    ),
                ],
                'custom_art_form' => ['nullable', 'string', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'biography' => ['required', 'string', 'max:5000', 'not_regex:/[<>]/u'],
                'representative_name' => ['required', 'string', 'min:2', 'max:255', 'regex:/\A[\pL\pM]+(?:[ .’\x27-][\pL\pM]+)*\z/u'],
                'representative_designation' => ['required', 'string', 'min:2', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'mailing_address' => ['required', 'string', 'max:2000'],
                'total_members' => ['required', 'integer', 'between:1,65535'],
                'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'supporting_documents' => ['nullable', 'array', 'max:5'],
                'supporting_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            ];
        } elseif ($type === 'institutional') {
            $rules += [
                'data_processing_consent' => ['accepted'],
                'full_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'institution_type_id' => [
                    'required',
                    'integer',
                    Rule::exists('art_institution_types', 'id')->where('status', 1),
                ],
                'custom_institution_type' => ['nullable', 'string', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'district_city' => ['required', 'string', 'min:2', 'max:100', 'regex:/\A[\pL\pM]+(?:[ .’\x27-][\pL\pM]+)*\z/u'],
                'establishment_year' => ['nullable', 'integer', 'between:1800,'.now()->year],
                'institution_registration_type' => ['nullable', 'string', 'max:100', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'registration_number' => ['nullable', 'string', 'max:60', 'regex:/\A[\pL\pN][\pL\pN ._\/-]*\z/u'],
                'art_forms' => ['required', 'array', 'min:1', 'max:25'],
                'art_forms.*' => [
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists('art_forms', 'id')->where(
                        fn ($query) => $query
                            ->where('status', 1)
                            ->where('parent_id', 0)
                            ->where('country_id', $request->input('country_id'))
                    ),
                ],
                'custom_art_form' => ['nullable', 'string', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'website' => ['nullable', 'url', 'max:2048'],
                'biography' => ['required', 'string', 'max:5000', 'not_regex:/[<>]/u'],
                'representative_name' => ['required', 'string', 'min:2', 'max:255', 'regex:/\A[\pL\pM]+(?:[ .’\x27-][\pL\pM]+)*\z/u'],
                'representative_designation' => ['required', 'string', 'min:2', 'max:255', 'regex:/\A[\pL\pM\pN][\pL\pM\pN\s&.,’\x27()\/-]*\z/u'],
                'mailing_address' => ['required', 'string', 'max:2000'],
                'authorization_letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
                'cultural_activities' => ['nullable', 'string', 'max:5000', 'not_regex:/[<>]/u'],
                'training_research_activities' => ['nullable', 'string', 'max:5000', 'not_regex:/[<>]/u'],
                'associated_artists' => ['nullable', 'integer', 'between:0,100000'],
                'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'brochure' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],
                'major_events' => ['nullable', 'string', 'max:5000', 'not_regex:/[<>]/u'],
                'collaborations' => ['nullable', 'string', 'max:5000', 'not_regex:/[<>]/u'],
                'registration_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
                'supporting_documents' => ['nullable', 'array', 'max:5'],
                'supporting_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            ];
        }

        if (in_array($type, ['group', 'institutional'], true)) {
            $rules['mailing_address'] = [
                'required',
                'string',
                'min:5',
                'max:2000',
                'regex:/\\A[\\pL\\pM\\pN\\s.,#\\/’()&:-\\x27]+\\z/u',
            ];
        }

        $validator = Validator::make($request->all(), $rules, [
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to register.',
            'full_name.regex' => 'Enter a valid name or organization name using the permitted characters.',
            'representative_name.regex' => 'Representative name may contain letters, spaces, dots, apostrophes and hyphens only.',
            'teacher_guru.regex' => 'Teacher / Guru name may contain letters, spaces, dots, apostrophes and hyphens only.',
            'city.regex' => 'Enter a valid city name.',
            'district_city.regex' => 'Enter a valid city name.',
            'phone.regex' => 'Enter a mobile number containing 6 to 15 digits, without the country code.',
            'mailing_address.regex' => 'Enter a valid address using commonly accepted punctuation.',
            'postal.regex' => 'Enter a valid postal / ZIP code.',
            'registration_number.regex' => 'Registration number contains unsupported characters.',
            'biography.not_regex' => 'HTML tags and script markup are not allowed in descriptions.',
            'cultural_activities.not_regex' => 'HTML tags and script markup are not allowed.',
            'training_research_activities.not_regex' => 'HTML tags and script markup are not allowed.',
            'major_events.not_regex' => 'HTML tags and script markup are not allowed.',
            'collaborations.not_regex' => 'HTML tags and script markup are not allowed.',
            'profile_photo.mimes' => 'Profile photo must be JPG, JPEG, PNG or WebP.',
            'id_document.mimes' => 'Identity document must be PDF, JPG, JPEG or PNG.',
            'supporting_documents.max' => 'You can upload a maximum of five supporting documents.',
            'consent.accepted' => 'Please accept the declaration to submit your registration.',
            'data_processing_consent.accepted' => 'Please consent to the processing of your information.',
        ]);
        $validator->after(function ($validator) use ($request, $type): void {
            if (! in_array($type, ['individual', 'group', 'institutional'], true)) {
                return;
            }

            foreach ((array) $request->input('awards', []) as $index => $award) {
                $hasAwardData = filled($award['award_id'] ?? null)
                    || filled($award['award_year'] ?? null)
                    || filled($award['description'] ?? null)
                    || $request->hasFile("awards.$index.certificate");

                if ($hasAwardData && blank($award['award_id'] ?? null)) {
                    $validator->errors()->add("awards.$index.award_id", 'Please select an award.');
                }
            }

            $countryId = (int) $request->input('country_id');
            $formIds = $type === 'individual'
                ? [(int) $request->input('art_form_id')]
                : array_map('intval', (array) $request->input('art_forms', []));
            $otherFormSelected = ArtForm::query()
                ->whereIn('id', $formIds)
                ->where('name', 'Other')
                ->exists();
            if ($otherFormSelected && blank($request->input('custom_art_form'))) {
                $validator->errors()->add('custom_art_form', 'Please describe the selected Other art form.');
            }

            if ($type === 'individual' && $request->filled('subgenre_id')) {
                $parentId = ArtForm::query()
                    ->whereKey($request->input('art_form_id'))
                    ->where('country_id', $countryId)
                    ->where('parent_id', 0)
                    ->where('status', 1)
                    ->value('id');
                if (! $parentId || ! ArtForm::query()
                    ->whereKey($request->input('subgenre_id'))
                    ->where('parent_id', $parentId)
                    ->where('country_id', $countryId)
                    ->where('status', 1)
                    ->exists()) {
                    $validator->errors()->add('subgenre_id', 'Select a sub-genre belonging to the selected art form.');
                } elseif (
                    ArtForm::query()->whereKey($request->input('subgenre_id'))->where('name', 'Other')->exists()
                    && blank($request->input('custom_subgenre'))
                ) {
                    $validator->errors()->add('custom_subgenre', 'Please enter the custom sub-genre.');
                }
            }

            if ($type === 'institutional') {
                $institutionTypeName = InstitutionType::query()
                    ->whereKey($request->input('institution_type_id'))
                    ->where('status', 1)
                    ->value('name');
                if ($institutionTypeName === 'Other' && blank($request->input('custom_institution_type'))) {
                    $validator->errors()->add('custom_institution_type', 'Please enter the custom institution type.');
                }
            }

            if ($type === 'group') {
                $groupTypeName = ArtGroupType::query()
                    ->whereKey($request->input('group_type_id'))
                    ->where('status', 1)
                    ->value('name');
                if ($groupTypeName === 'Other' && blank($request->input('custom_group_type'))) {
                    $validator->errors()->add('custom_group_type', 'Please enter the custom group type.');
                }
            }
        });
        $validated = $validator->validate();

        $roleId = self::ROLE_IDS[$type];
        if (! Role::query()->where('role_id', $roleId)->where('active', 1)->exists()) {
            return back()->withErrors([
                'registration_type' => 'This registration type is not currently available. Please contact the registry administrator.',
            ])->withInput();
        }

        $country = Country::query()->where('status', 1)->where('type', 1)->findOrFail($validated['country_id']);
        $user = User::query()->where('email', $validated['email'])->first();
        if ($user && (int) Auth::id() !== (int) $user->id) {
            return back()->withErrors([
                'email' => 'This email already has an account. Please log in with that account before submitting a registration.',
            ])->withInput();
        }
        if ($user && (int) $user->role_id !== $roleId) {
            return back()->withErrors([
                'email' => 'This email is already associated with a different account type.',
            ])->withInput();
        }
        if ($user && $user->country_id && (int) $user->country_id !== (int) $country->id) {
            return back()->withErrors([
                'country_id' => 'This account is already associated with a different country.',
            ])->withInput();
        }
        if ($user && $this->hasRegistration((int) $user->id, $type)) {
            return back()->withErrors([
                'email' => 'This account already has a registration of this type.',
            ])->withInput();
        }
        $newUser = $user === null;
        $region = ArtRegion::query()
            ->where('country_id', $country->id)
            ->where('status', 1)
            ->findOrFail($validated['region_id']);
        $artForms = $type === 'individual'
            ? ArtForm::query()->whereKey($validated['art_form_id'])->get()
            : ArtForm::query()->whereIn('id', $validated['art_forms'])->get();
        $groupType = $type === 'group'
            ? ArtGroupType::query()->where('status', 1)->findOrFail($validated['group_type_id'])
            : null;
        $institutionType = $type === 'institutional'
            ? InstitutionType::query()->where('status', 1)->findOrFail($validated['institution_type_id'])
            : null;

        $storedPaths = [];
        try {
            foreach ([
                'profile_photo' => 'profile_photo_path',
                'id_document' => 'id_document_path',
                'authorization_letter' => 'authorization_letter_path',
                'brochure' => 'brochure_path',
                'registration_certificate' => 'registration_certificate_path',
            ] as $input => $column) {
                if ($request->hasFile($input)) {
                     $storedPaths[$column] = $request->file($input)->store('artists', 'public_uploads');
					
                }
            }
            foreach ($request->file('supporting_documents', []) as $document) {
                $storedPaths['supporting_document_paths'][] = $document->store('artists', 'public_uploads');
            }
            foreach ($request->file('awards', []) as $index => $awardFiles) {
                if (isset($awardFiles['certificate']) && $awardFiles['certificate']) {
                    $storedPaths['award_certificates'][$index] = $awardFiles['certificate']->store('artist-awards', 'public_uploads');
                }
            }

            $activationToken = Str::random(64);
            $this->acquireRegistrationLock();
            $artistUser = DB::transaction(function () use (
                $validated,
                $type,
                $roleId,
                $country,
                $region,
                $artForms,
                $groupType,
                $institutionType,
                $storedPaths,
                $activationToken,
                $user,
                $newUser
            ): User {
                $artistUser = $user;
                if ($newUser) {
                    $name = $validated['representative_name'] ?? $validated['full_name'];
                    $nameParts = preg_split('/\s+/', trim($name), 2);
                    $artistUser = User::create([
                        'role_id' => $roleId,
                        'country_id' => $country->id,
                        'region_id' => $region->id,
                        'city' => $validated['city'] ?? $validated['district_city'] ?? null,
                        'pincode' => $validated['postal'] ?? null,
                        'full_name' => $name,
                        'firstname' => $nameParts[0],
                        'lastname' => $nameParts[1] ?? null,
                        'designation' => $validated['representative_designation'] ?? null,
                        'dob' => $validated['date_of_birth'] ?? null,
                        'gender' => $validated['gender'] ?? null,
                        'email' => $validated['email'],
                        'password' => Hash::make(Str::random(64)),
                        'mobile' => $validated['phone'],
                        'address' => $validated['mailing_address'] ?? null,
                        'active' => 0,
                        'verified_status' => 0,
                    ]);
                    $artistUser->regno = $this->nextRegistrationNumber(
                        (string) $country->alpha_3,
                        $this->registrationTypeCode($type)
                    );
                    $artistUser->save();
                } else {
                    if (! $artistUser->country_id) {
                        $artistUser->country_id = $country->id;
                    }
                    if (! $artistUser->regno) {
                        $artistUser->regno = $this->nextRegistrationNumber(
                            (string) $country->alpha_3,
                            $this->registrationTypeCode($type)
                        );
                    }
                    if ($artistUser->isDirty()) {
                        $artistUser->save();
                    }
                }

                $selectedArtForms = $artForms->pluck('name');
                $otherArtForm = $selectedArtForms->contains('Other') ? ($validated['custom_art_form'] ?? null) : null;
                $subgenre = isset($validated['subgenre_id'])
                    ? ArtForm::query()->findOrFail($validated['subgenre_id'])
                    : null;
                $otherSubgenre = $subgenre?->name === 'Other' ? ($validated['custom_subgenre'] ?? null) : null;
                $languageIds = $this->commaSeparatedIds($validated['language_ids'] ?? []);
                $artFormIds = $this->commaSeparatedIds($artForms->pluck('id')->all());
                $supportingDocuments = $storedPaths['supporting_document_paths'] ?? [];
                $portfolioLinks = array_values(array_filter($validated['portfolio_links'] ?? [], fn ($url) => filled($url)));

                if ($type === 'individual') {
                    $registrationId = DB::table('artist_registrations')->insertGetId([
                        'user_id' => $artistUser->id,
                        'art_form_id' => $artForms->first()->id,
                        'other_art_form' => $otherArtForm,
                        'subgenre_id' => $subgenre?->id,
                        'other_subgenre' => $otherSubgenre,
                        'years_of_practice' => $validated['years_of_practice'] ?? null,
                        'language_id' => $languageIds,
                        'biography' => $validated['biography'] ?? null,
                        'teacher_guru' => $validated['teacher_guru'] ?? null,
                        'institutional_affiliation' => $validated['institutional_affiliation'] ?? null,
                        'profile_photo' => $storedPaths['profile_photo_path'] ?? null,
                        'identity_document' => $storedPaths['id_document_path'] ?? null,
                        'consent' => true,
                        'public_profile' => (bool) ($validated['public_profile'] ?? false),
                        'status' => ArtistRegistration::STATUS_PENDING,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } elseif ($type === 'group') {
                    $registrationId = DB::table('artist_group_details')->insertGetId([
                        'registration_type' => 'group',
                        'user_id' => $artistUser->id,
                        'group_name' => $validated['full_name'],
                        'group_type' => $groupType->name,
                        'group_type_id' => $groupType->id,
                        'other_group_type' => $groupType->name === 'Other' ? ($validated['custom_group_type'] ?? null) : null,
                        'establishment_year' => $validated['establishment_year'] ?? null,
                        'registration_number' => $validated['registration_number'] ?? null,
                        'total_members' => $validated['total_members'],
                        'art_form_id' => $artFormIds,
                        'other_art_form' => $otherArtForm,
                        'language_id' => $languageIds,
                        'group_description' => $validated['biography'] ?? null,
                        'identity_document' => $storedPaths['id_document_path'] ?? null,
                        'profile_photo' => $storedPaths['profile_photo_path'] ?? null,
                        'supporting_document' => $supportingDocuments ? implode(',', $supportingDocuments) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $institutionId = DB::table('artist_institution_details')->insertGetId([
                        'user_id' => $artistUser->id,
                        'institution_name' => $validated['full_name'],
                        'institution_type' => $institutionType->name,
                        'other_institution_type' => $institutionType->name === 'Other' ? ($validated['custom_institution_type'] ?? null) : null,
                        'establishment_year' => $validated['establishment_year'] ?? null,
                        'institution_registration_type' => $validated['institution_registration_type'] ?? null,
                        'registration_number' => $validated['registration_number'] ?? null,
                        'art_form_id' => $artFormIds,
                        'other_art_form' => $otherArtForm,
                        'website' => $validated['website'] ?? null,
                        'institution_description' => $validated['biography'] ?? null,
                        'identity_document' => $storedPaths['id_document_path'] ?? null,
                        'authorization_letter' => $storedPaths['authorization_letter_path'] ?? null,
                        'cultural_activities' => $validated['cultural_activities'] ?? null,
                        'training_research_activities' => $validated['training_research_activities'] ?? null,
                        'language_id' => $languageIds,
                        'associated_artists' => $validated['associated_artists'] ?? null,
                        'institution_logo' => $storedPaths['profile_photo_path'] ?? null,
                        'brochure_path' => $storedPaths['brochure_path'] ?? null,
                        'major_events' => $validated['major_events'] ?? null,
                        'collaborations' => $validated['collaborations'] ?? null,
                        'registration_certificate' => $storedPaths['registration_certificate_path'] ?? null,
                        'supporting_documents' => $supportingDocuments ? implode(',', $supportingDocuments) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $registrationId = $institutionId;
                }

                // Store links for all registration types in the common portfolio table.
                $registrationTypeId = match ($type) {
                    'individual' => 5,
                    'group' => 6,
                    'institutional' => 7,
                };

                foreach ($portfolioLinks as $url) {
                    DB::table('artist_portfolio_links')->insert([
                        'registration_type' => $registrationTypeId,
                        'registration_id' => $registrationId,
                        'portfolio_link' => trim($url),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach (($validated['awards'] ?? []) as $awardIndex => $award) {
                    if (blank($award['award_id'] ?? null)) {
                        continue;
                    }

                    DB::table('artist_awards')->insert([
                        'registration_type' => $registrationTypeId,
                        'registration_id' => $registrationId,
                        'award_id' => (int) $award['award_id'],
                        'award_year' => $award['award_year'] ?? null,
                        'certificate_path' => $storedPaths['award_certificates'][$awardIndex] ?? null,
                        'description' => $award['description'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if ($newUser) {
                    DB::table('artist_account_activations')->updateOrInsert(
                        ['user_id' => $artistUser->id],
                        [
                            'token_hash' => hash('sha256', $activationToken),
                            'expires_at' => now()->addHours(24),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                return $artistUser;
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $pathOrPaths) {
                foreach ((array) $pathOrPaths as $path) {
                    Storage::disk('public_uploads')->delete($path);
                }
            }

            throw $exception;
        } finally {
            $this->releaseRegistrationLock();
        }

        if ($newUser) {
            $this->emailService->send(
                $artistUser->email,
                SetArtistPasswordMail::class,
                [$activationToken],
                config('mail_notifications.password_setup', true)
            );
        }

        $this->sendRegistrationEmails($artistUser, $type, $country, $validated);

        // OTP verification is single-use: clear it once the registration is stored
        // so the same verification cannot be reused for another submission.
        if (config('otp.email_enabled')) {
            $this->otpService->consume($artistUser->email, EmailOtpService::PURPOSE_REGISTRATION);
        }

        $successMessages = [
            'individual' => 'Your individual artist application has been submitted. Check your email to set a password and activate your account.',
            'group' => 'Your group artist application has been submitted. Check your email to set a password and activate your account.',
            'institutional' => 'Your institInstitute / Organization application has been submitted. Check your email to set a password and activate your account.',
        ];

        return redirect()
            ->route('artist.registration')
            ->with(
                'registration_success',
                $newUser
                    ? $successMessages[$type]
                    : 'Your registration has been submitted and linked to your logged-in account.'
            );
    }

    public function showPasswordSetup(string $token): View|RedirectResponse
    {
        if (! $this->activationForToken($token)) {
            return redirect()->route('login')->withErrors([
                'activation' => 'This password setup link is invalid or has expired. Please contact the registry administrator.',
            ]);
        }

        return view('frontend.artist-password-setup', compact('token'));
    }

    public function setPassword(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $activation = $this->activationForToken($token);
        if (! $activation) {
            return back()->withErrors([
                'activation' => 'This password setup link is invalid or has expired. Please contact the registry administrator.',
            ]);
        }

        DB::transaction(function () use ($activation, $token, $validated): void {
            $lockedActivation = DB::table('artist_account_activations')
                ->where('user_id', $activation->user_id)
                ->lockForUpdate()
                ->first();
            abort_unless(
                $lockedActivation
                    && hash_equals($lockedActivation->token_hash, hash('sha256', $token))
                    && now()->lt($lockedActivation->expires_at),
                410
            );

            User::query()->whereKey($activation->user_id)->update([
                'password' => Hash::make($validated['password']),
                'active' => 1,
                'email_verified_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('artist_account_activations')->where('user_id', $activation->user_id)->delete();
        });

        return redirect()->route('login')->with('success', 'Your password has been set and your account is active. You can now log in.');
    }

    /**
     * Send the applicant confirmation email and notify the Country Nodal
     * Officer(s) of the selected country. Mail failures are logged but never
     * roll back or block a completed registration.
     */
    private function sendRegistrationEmails(User $artistUser, string $type, Country $country, array $validated): void
    {
        $registrationId = $artistUser->regno ?: 'Pending assignment';
        $applicantName = (string) ($validated['full_name'] ?? $artistUser->full_name);
        $typeLabel = self::REGISTRATION_TYPE_LABELS[$type] ?? ucfirst($type);
        $submissionDate = now()->format('d M Y, h:i A');

        $this->emailService->send(
            $artistUser->email,
            ArtistRegistrationConfirmation::class,
            [
                $registrationId,
                $applicantName,
                $typeLabel,
                $submissionDate,
                'Pending Review',
                (string) $country->name,
            ],
            config('mail_notifications.registration_confirmation', true)
        );

        $officers = User::query()
            ->where('role_id', self::NODAL_OFFICER_ROLE_ID)
            ->where('country_id', $country->id)
            ->whereNotNull('email')
            ->get(['id', 'full_name', 'firstname', 'lastname', 'email'])
            ->filter(fn (User $officer): bool => filter_var($officer->email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique('email')
            ->values();

        if ($officers->isEmpty()) {
            Log::warning('No Country Nodal Officer email found for a new artist registration.', [
                'country_id' => $country->id,
                'country' => $country->name,
                'registration_id' => $registrationId,
                'registration_type' => $type,
            ]);

            return;
        }

        $reviewUrl = url(config('app.backend_prefix', 'cp'));

        foreach ($officers as $officer) {
            $officerName = trim((string) ($officer->full_name ?: $officer->firstname.' '.$officer->lastname)) ?: 'Nodal Officer';

            $this->emailService->send(
                $officer->email,
                NodalOfficerNewRegistration::class,
                [
                    $officerName,
                    $registrationId,
                    $applicantName,
                    $typeLabel,
                    (string) $country->name,
                    (string) $artistUser->email,
                    $validated['phone'] ?? null,
                    $submissionDate,
                    $reviewUrl,
                ],
                config('mail_notifications.nodal_registration', true)
            );
        }
    }

    private function mainArtFormsQuery(int $countryId): Builder
    {
        return ArtForm::query()
            ->where('country_id', $countryId)
            ->where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name');
    }

    private function subgenresQuery(int $countryId, int $parentId): Builder
    {
        return ArtForm::query()
            ->where('country_id', $countryId)
            ->where('parent_id', $parentId)
            ->where('status', 1)
            ->orderBy('name');
    }

    private function activationForToken(string $token): ?object
    {
        return DB::table('artist_account_activations')
            ->where('token_hash', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->first();
    }

    private function nextRegistrationNumber(string $alpha3, string $type): string
    {
        // Global OH-AI / OH-AG / OH-AO sequence (App\Support\RegistrationNumber); $alpha3 is no longer part of the ID.
        return \App\Support\RegistrationNumber::next(\App\Support\RegistrationNumber::roleForType($type));
    }

    private function acquireRegistrationLock(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $acquired = DB::selectOne('SELECT GET_LOCK(?, 15) AS acquired', ['brics_registration_number']);
            if (! $acquired || (int) $acquired->acquired !== 1) {
                throw new \RuntimeException('Could not obtain the registration number lock. Please try again.');
            }
        }
    }

    private function releaseRegistrationLock(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::select('SELECT RELEASE_LOCK(?)', ['brics_registration_number']);
        }
    }

    private function commaSeparatedIds(array $ids): ?string
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        return $ids === [] ? null : implode(',', $ids);
    }

    private function hasRegistration(int $userId, string $type): bool
    {
        $table = match ($type) {
            'individual' => 'artist_registrations',
            'group' => 'artist_group_details',
            default => 'artist_institution_details',
        };

        return DB::table($table)->where('user_id', $userId)->exists();
    }

    private function registrationTypeCode(string $type): string
    {
        return match ($type) {
            'individual' => 'A',
            'group' => 'G',
            'institutional' => 'I',
            default => throw new \InvalidArgumentException('Unsupported artist registration type.'),
        };
    }
}
