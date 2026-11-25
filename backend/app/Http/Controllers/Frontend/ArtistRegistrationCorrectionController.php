<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ArtistRegistration;
use App\Models\ArtistRegistrationHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ArtistRegistrationCorrectionController extends Controller
{
    public function edit(int $registration): View
    {
        $registration = $this->ownedReturnedRegistration($registration);
        $registration->load(['groupDetails.members', 'institutionDetails', 'histories']);

        return view('frontend.artist-registration-correction', compact('registration'));
    }

    public function resubmit(Request $request, int $registration): RedirectResponse
    {
        $registration = $this->ownedReturnedRegistration($registration);
        $type = $registration->registration_type;
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'art_form' => ['required', 'string', 'max:1000'],
            'subgenre' => ['nullable', 'string', 'max:255'],
            'years_of_practice' => ['nullable', 'integer', 'between:0,100'],
            'teacher_guru' => ['nullable', 'string', 'max:255'],
            'languages' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'gender' => ['nullable', Rule::in(['Female', 'Male', 'Other'])],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'phone' => ['required', 'string', 'max:55'],
            'mailing_address' => ['nullable', 'string', 'max:2000'],
            'institutional_affiliation' => ['nullable', 'string', 'max:255'],
            'portfolio_link' => ['nullable', 'string', 'max:5000'],
            'awards' => ['nullable', 'string', 'max:5000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'id_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];

        if ($type === 'group') {
            $rules += [
                'group_type' => ['required', 'string', 'max:100'],
                'district_city' => ['required', 'string', 'max:255'],
                'establishment_year' => ['nullable', 'integer', 'between:1800,'.now()->year],
                'registration_number' => ['nullable', 'string', 'max:255'],
                'representative_name' => ['required', 'string', 'max:255'],
                'representative_designation' => ['required', 'string', 'max:255'],
                'members' => ['required', 'array', 'min:1', 'max:500'],
                'members.*.name' => ['required', 'string', 'max:255'],
                'members.*.role' => ['required', 'string', 'max:255'],
                'members.*.art_form' => ['required', 'string', 'max:255'],
                'supporting_documents' => ['nullable', 'array', 'max:5'],
                'supporting_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
                'institution_type' => ['prohibited'],
                'authorization_letter' => ['prohibited'],
                'brochure' => ['prohibited'],
                'registration_certificate' => ['prohibited'],
            ];
        } elseif ($type === 'institutional') {
            $rules += [
                'institution_type' => ['required', 'string', 'max:100'],
                'district_city' => ['required', 'string', 'max:255'],
                'establishment_year' => ['nullable', 'integer', 'between:1800,'.now()->year],
                'institution_registration_type' => ['nullable', 'string', 'max:100'],
                'registration_number' => ['nullable', 'string', 'max:255'],
                'representative_name' => ['required', 'string', 'max:255'],
                'representative_designation' => ['required', 'string', 'max:255'],
                'cultural_activities' => ['nullable', 'string', 'max:5000'],
                'training_research_activities' => ['nullable', 'string', 'max:5000'],
                'associated_artists' => ['nullable', 'integer', 'min:0'],
                'major_events' => ['nullable', 'string', 'max:5000'],
                'collaborations' => ['nullable', 'string', 'max:5000'],
                'authorization_letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
                'brochure' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
                'registration_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
                'supporting_documents' => ['nullable', 'array', 'max:5'],
                'supporting_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
                'members' => ['prohibited'],
                'group_type' => ['prohibited'],
            ];
        } else {
            $rules += [
                'supporting_documents' => ['prohibited'],
                'authorization_letter' => ['prohibited'],
                'brochure' => ['prohibited'],
                'registration_certificate' => ['prohibited'],
                'members' => ['prohibited'],
                'group_type' => ['prohibited'],
                'institution_type' => ['prohibited'],
            ];
        }

        $validated = $request->validate($rules);
        if ($request->hasFile('supporting_documents')) {
            $existingDocuments = $type === 'group'
                ? ($registration->groupDetails?->supporting_document_paths ?? [])
                : ($registration->institutionDetails?->supporting_document_paths ?? []);
            if (count($existingDocuments) + count($request->file('supporting_documents')) > 5) {
                throw ValidationException::withMessages([
                    'supporting_documents' => 'A registration may have no more than five supporting documents.',
                ]);
            }
        }

        $stored = [];
        $uploadColumns = [
            'profile_photo' => 'profile_photo_path',
            'id_document' => 'id_document_path',
        ];
        if ($type === 'institutional') {
            $uploadColumns += [
                'authorization_letter' => 'authorization_letter_path',
                'brochure' => 'brochure_path',
                'registration_certificate' => 'registration_certificate_path',
            ];
        }
        foreach ($uploadColumns as $upload => $column) {
            if ($request->hasFile($upload)) {
                $stored[$column] = $request->file($upload)->store('artist-registrations', 'local');
            }
        }
        if ($request->hasFile('supporting_documents')) {
            $stored['supporting_document_paths'] = array_map(
                fn ($file) => $file->store('artist-registrations', 'local'),
                $request->file('supporting_documents')
            );
        }

        try {
            DB::transaction(function () use ($registration, $type, $validated, $stored): void {
                $registration = ArtistRegistration::query()
                    ->where('user_id', auth()->id())
                    ->where('status', ArtistRegistration::STATUS_RETURNED)
                    ->lockForUpdate()
                    ->findOrFail($registration->id);
                $registration->fill(array_intersect_key($validated, array_flip([
                    'full_name', 'region', 'date_of_birth', 'gender', 'art_form', 'subgenre',
                    'years_of_practice', 'teacher_guru', 'languages', 'biography', 'phone',
                    'mailing_address', 'institutional_affiliation', 'portfolio_link', 'awards',
                ])));
                foreach (['profile_photo_path', 'id_document_path'] as $column) {
                    if (isset($stored[$column])) {
                        $registration->{$column} = $stored[$column];
                    }
                }
                $registration->status = ArtistRegistration::STATUS_PENDING;
                $registration->save();

                if ($type === 'group' && $registration->groupDetails) {
                    $group = $registration->groupDetails;
                    $group->fill(array_intersect_key($validated, array_flip([
                        'group_type', 'district_city', 'establishment_year', 'registration_number',
                        'representative_name', 'representative_designation',
                    ])));
                    if (isset($stored['supporting_document_paths'])) {
                        $group->supporting_document_paths = array_merge(
                            $group->supporting_document_paths ?? [],
                            $stored['supporting_document_paths']
                        );
                    }
                    $group->total_members = count($validated['members']);
                    $group->save();
                    $group->members()->delete();
                    $group->members()->createMany($validated['members']);
                } elseif ($type === 'institutional' && $registration->institutionDetails) {
                    $institution = $registration->institutionDetails;
                    $institution->fill(array_intersect_key($validated, array_flip([
                        'institution_type', 'district_city', 'establishment_year', 'institution_registration_type',
                        'registration_number', 'representative_name', 'representative_designation',
                        'cultural_activities', 'training_research_activities', 'associated_artists',
                        'major_events', 'collaborations',
                    ])));
                    foreach ([
                        'authorization_letter_path', 'brochure_path', 'registration_certificate_path',
                        'supporting_document_paths',
                    ] as $column) {
                        if (isset($stored[$column])) {
                            $institution->{$column} = $column === 'supporting_document_paths'
                                ? array_merge($institution->supporting_document_paths ?? [], $stored[$column])
                                : $stored[$column];
                        }
                    }
                    $institution->save();
                }

                ArtistRegistrationHistory::create([
                    'artist_registration_id' => $registration->id,
                    'actor_id' => auth()->id(),
                    'old_status' => ArtistRegistration::STATUS_RETURNED,
                    'new_status' => ArtistRegistration::STATUS_PENDING,
                    'remarks' => 'Applicant corrected and resubmitted the registration.',
                ]);
            });
        } catch (Throwable $exception) {
            foreach ($stored as $pathOrPaths) {
                foreach ((array) $pathOrPaths as $path) {
                    Storage::disk('local')->delete($path);
                }
            }

            throw $exception;
        }

        return redirect()->route('artist.registration')
            ->with('registration_success', 'Your corrected registration has been resubmitted for review.');
    }

    private function ownedReturnedRegistration(int $id): ArtistRegistration
    {
        return ArtistRegistration::query()
            ->where('user_id', auth()->id())
            ->where('status', ArtistRegistration::STATUS_RETURNED)
            ->findOrFail($id);
    }
}
