<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ArtistRegistration;
use App\Models\Banner;
use App\Models\Country;
use App\Models\Minister;
use App\Models\User;
use App\Models\Document;
use App\Models\MenuItem;
use App\Models\LoginHistory;
use App\Models\Content;
use App\Notifications\WelcomeUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;



class ArtistController extends Controller
{
    
	
public function artistslist(Request $request, ?string $id = null)
{
    $country = null;

    if ($id !== null) {
        try {
            $country = Country::query()->findOrFail(decrypt($id));
        } catch (\Throwable $e) {
            abort(404);
        }
    }

    $filters = $request->validate([
        'search'   => ['nullable', 'string', 'max:255'],
        'country'  => ['nullable', 'integer', 'exists:countries,id'],
        'art_form' => ['nullable', 'string', 'max:255'],
    ]);

    if ($country) {
        $filters['country'] = (string) $country->id;
    }

    // Active and verified artists, groups and institutions.
    $baseQuery = DB::table('users')
        ->leftJoin('countries as c', 'c.id', '=', 'users.country_id')
        ->whereIn('users.role_id', [5, 6, 7])
        //->where('users.active', 1)
        ->where('users.verified_status', 1)
        ->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('users.role_id', 5)
                    ->whereExists(function ($exists) {
                        $exists->selectRaw('1')
                            ->from('artist_registrations as ar')
                            ->whereColumn('ar.user_id', 'users.id');
                    });
            })->orWhere(function ($sub) {
                $sub->where('users.role_id', 6)
                    ->whereExists(function ($exists) {
                        $exists->selectRaw('1')
                            ->from('artist_group_details as ag')
                            ->whereColumn('ag.user_id', 'users.id');
                    });
            })->orWhere(function ($sub) {
                $sub->where('users.role_id', 7)
                    ->whereExists(function ($exists) {
                        $exists->selectRaw('1')
                            ->from('artist_institution_details as ai')
                            ->whereColumn('ai.user_id', 'users.id');
                    });
            });
        });

    $artistTotal = (clone $baseQuery)->count('users.id');

    $query = clone $baseQuery;

    // Search by name, country or art form.
    if (!empty($filters['search'])) {
        $search = trim($filters['search']);

        $query->where(function ($q) use ($search) {
            $q->where('users.full_name', 'like', "%{$search}%")
                ->orWhere('c.name', 'like', "%{$search}%")

                // Search individual artist art forms.
                ->orWhereExists(function ($sub) use ($search) {
                    $sub->selectRaw('1')
                        ->from('artist_registrations as ar')
                        ->leftJoin(
                            'art_forms as af',
                            'af.id',
                            '=',
                            'ar.art_form_id'
                        )
                        ->whereColumn('ar.user_id', 'users.id')
                        ->where(function ($form) use ($search) {
                            $form->where('af.name', 'like', "%{$search}%")
                                ->orWhere(
                                    'ar.other_art_form',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'ar.other_subgenre',
                                    'like',
                                    "%{$search}%"
                                );
                        });
                })

                // Search group name and group art form.
                ->orWhereExists(function ($sub) use ($search) {
                    $sub->selectRaw('1')
                        ->from('artist_group_details as ag')
                        ->whereColumn('ag.user_id', 'users.id')
                        ->where(function ($group) use ($search) {
                            $group->where(
                                'ag.group_name',
                                'like',
                                "%{$search}%"
                            )->orWhere(
                                'ag.other_art_form',
                                'like',
                                "%{$search}%"
                            );
                        });
                })

                // Search institution name and institution art form.
                ->orWhereExists(function ($sub) use ($search) {
                    $sub->selectRaw('1')
                        ->from('artist_institution_details as ai')
                        ->whereColumn('ai.user_id', 'users.id')
                        ->where(function ($institution) use ($search) {
                            $institution->where(
                                'ai.institution_name',
                                'like',
                                "%{$search}%"
                            )->orWhere(
                                'ai.other_art_form',
                                'like',
                                "%{$search}%"
                            );
                        });
                });
        });
    }

    // Country filter.
    if (!empty($filters['country'])) {
        $query->where('users.country_id', $filters['country']);
    }

    // Art Form filter.
    if (!empty($filters['art_form'])) {
        $artForm = trim($filters['art_form']);

        $query->where(function ($q) use ($artForm) {
            foreach ([
                ['artist_registrations', 'ar'],
                ['artist_group_details', 'ag'],
                ['artist_institution_details', 'ai'],
            ] as [$table, $alias]) {
                $q->orWhereExists(function ($sub) use (
                    $table,
                    $alias,
                    $artForm
                ) {
                    $sub->selectRaw('1')
                        ->from("$table as $alias")
                        ->whereColumn("$alias.user_id", 'users.id')
                        ->where(function ($form) use ($alias, $artForm) {
                            $form->where(
                                "$alias.other_art_form",
                                'like',
                                "%{$artForm}%"
                            )->orWhereExists(function ($exists) use (
                                $alias,
                                $artForm
                            ) {
                                $exists->selectRaw('1')
                                    ->from('art_forms as af')
                                    ->whereColumn(
                                        'af.id',
                                        "$alias.art_form_id"
                                    )
                                    ->where(
                                        'af.name',
                                        'like',
                                        "%{$artForm}%"
                                    );
                            });
                        });
                });
            }
        });
    }

    // Fetch users with pagination.
    $users = $query
        ->select([
            'users.id',
            'users.full_name',
            'users.role_id',
            'users.country_id',
            'c.name as country',
        ])
        ->orderByDesc('users.id')
        ->paginate(10)
        ->withQueryString();

    // Attach role-specific details and display name.
    $artists = $users->through(function ($artist) {
        $details = null;
        $photo = null;

        if ((int) $artist->role_id === 5) {
            // Individual Artist.
            $details = DB::table('artist_registrations')
                ->where('user_id', $artist->id)
                ->first();

            $photo = $details->profile_photo ?? null;

            $artist->display_name = $artist->full_name;

        } elseif ((int) $artist->role_id === 6) {
            // Group Artist.
            $details = DB::table('artist_group_details')
                ->where('user_id', $artist->id)
                ->first();

            $photo = $details->profile_photo ?? null;
            $artist->groupDetails = $details;

            $artist->display_name = !empty($details->group_name)
                ? $details->group_name
                : $artist->full_name;

        } elseif ((int) $artist->role_id === 7) {
            //  Institute  Organization.
            $details = DB::table('artist_institution_details')
                ->where('user_id', $artist->id)
                ->first();

            $photo = $details->institution_logo ?? null;
            $artist->institutionDetails = $details;

            $artist->display_name = !empty($details->institution_name)
                ? $details->institution_name
                : $artist->full_name;
        }

        $artist->registration_type = match ((int) $artist->role_id) {
            5 => 'Artist',
            6 => 'Group Artist',
            7 => 'Institute / Organization',
            default => 'Artist',
        };

        $artist->art_form = '—';
        $artist->subgenre = null;
        $artist->years_of_practice = null;

        $artist->profile_photo_url = $photo
            ? asset('uploads/' . ltrim($photo, '/'))
            : null;

        if ($details) {
            $artist->years_of_practice =
                $details->years_of_practice ?? null;

            $formIds = array_filter(array_map(
                'intval',
                explode(',', (string) ($details->art_form_id ?? ''))
            ));

            $formNames = $formIds
                ? DB::table('art_forms')
                    ->whereIn('id', $formIds)
                    ->pluck('name')
                    ->implode(', ')
                : '';

            $artist->art_form =
                $formNames ?: ($details->other_art_form ?? '—');

            // Sub-genre applies to Individual Artists.
            if (
                (int) $artist->role_id === 5
                && !empty($details->subgenre_id)
            ) {
                $artist->subgenre = DB::table('art_forms')
                    ->where('id', $details->subgenre_id)
                    ->value('name')
                    ?? ($details->other_subgenre ?? null);
            }
        }

        return $artist;
    });

    // Active countries.
    $countries = Country::query()
        ->where('status', 1)
        ->orderBy('name')
        ->get(['id', 'name', 'flag_image']);

    // Country-specific main art forms.
    $artForms = collect();

    if (!empty($filters['country'])) {
        $artForms = DB::table('art_forms')
            ->where('status', 1)
            ->where('parent_id', 0)
            ->where('country_id', $filters['country'])
            ->orderBy('name')
            ->pluck('name');
    }

    return view('frontend.artistslist', compact(
        'artists',
        'countries',
        'artForms',
        'artistTotal',
        'filters'
    ));
}

public function artistsdetail(?string $id = null)
{
    try {
        if ($id === null) abort(404);
        $userId = decrypt($id);
        if (!is_numeric($userId)) abort(404);
    } catch (\Throwable $e) {
        abort(404);
    }

    $user = DB::table('users')
        ->where('id', (int) $userId)
        ->whereIn('role_id', [5, 6, 7])
        ->where('verified_status', 1)
        ->first();

    abort_if(!$user, 404);

    $country = $user->country_id ? DB::table('countries')->find($user->country_id) : null;
    $region = $user->region_id ? DB::table('art_regions')->find($user->region_id) : null;

    $roleId = (int) $user->role_id;
    [$profileType, $table] = match ($roleId) {
        5 => ['individual', 'artist_registrations'],
        6 => ['group', 'artist_group_details'],
        7 => ['institution', 'artist_institution_details'],
        default => abort(404),
    };

    $detail = DB::table($table)->where('user_id', $user->id)->first();
    abort_if(!$detail, 404);

    $artFormNames = $this->artFormNames($detail->art_form_id ?? null, $detail->other_art_form ?? null);
    $subgenreName = $profileType === 'individual'
        ? $this->subgenreName($detail->subgenre_id ?? null, $detail->other_subgenre ?? null)
        : null;

    $typeName = match ($profileType) {
        'group' => $this->groupTypeName($detail),
        'institution' => $this->institutionTypeName($detail),
        default => null,
    };

    $awards = DB::table('artist_awards as aa')
        ->leftJoin('art_award_masters as am', 'am.id', '=', 'aa.award_id')
        ->where('aa.registration_type', $roleId)
        ->where('aa.registration_id', $detail->id)
        ->orderByDesc('aa.award_year')
        ->orderByDesc('aa.id')
        ->get([
            'aa.id', 'aa.award_year', 'aa.certificate_path',
            'aa.description', 'aa.award_id', 'am.name as award_name',
        ]);

    $portfolioUrls = DB::table('artist_portfolio_links')
        ->where('registration_type', $roleId)
        ->where('registration_id', $detail->id)
        ->orderBy('id')
        ->pluck('portfolio_link')
        ->map(fn ($url) => $this->safeExternalUrl($url))
        ->filter()
        ->values()
        ->all();

    // Backward compatibility for registrations that still have the legacy URL column populated.
    $legacyUrl = $this->safeExternalUrl($detail->portfolio_link ?? null);
    if ($legacyUrl && !in_array($legacyUrl, $portfolioUrls, true)) {
        $portfolioUrls[] = $legacyUrl;
    }

    $languageNames = $this->lookupNames('art_languages', $detail->language_id ?? null);
    $photoPath = $profileType === 'institution'
        ? ($detail->institution_logo ?? null)
        : ($detail->profile_photo ?? null);

    $displayName = match ($profileType) {
        'individual' => $user->full_name,
        'group' => $detail->group_name ?: $user->full_name,
        'institution' => $detail->institution_name ?: $user->full_name,
    };

    $data = [
        'user' => $user,
        'country' => $country,
        'region' => $region,
        'profileType' => $profileType,
        'detail' => $detail,
        'displayName' => $displayName,
        'initials' => $this->nameInitials((string) ($displayName ?: $user->full_name)),
        'flagUrl' => $country && !empty($country->flag_image)
            ? asset('assets/frontend/v1/images/flags/' . $country->flag_image)
            : null,
        'moreArtistsUrl' => $country
            ? route('artist.list.country', ['id' => encrypt($country->id)])
            : route('artist.list'),
        'artFormNames' => $artFormNames,
        'subgenreName' => $subgenreName,
        'typeName' => $typeName,
        'languageNames' => $languageNames,
        'photoUrl' => $this->uploadUrl($photoPath),
        'portfolioUrls' => $portfolioUrls,
        'websiteUrl' => $profileType === 'institution'
            ? $this->safeExternalUrl($detail->website ?? null)
            : null,
        'awards' => $awards,
        'awardLines' => $this->textLines($detail->awards ?? null),
        'aboutText' => match ($profileType) {
            'individual' => $detail->biography ?? null,
            'group' => $detail->group_description ?? null,
            'institution' => $detail->institution_description ?? null,
        },
        'roleLine' => $this->roleLine(array_merge(array_filter([$typeName]), $artFormNames, array_filter([$subgenreName]))),
        'culturalLines' => $profileType === 'institution' ? $this->textLines($detail->cultural_activities ?? null) : [],
        'trainingLines' => $profileType === 'institution' ? $this->textLines($detail->training_research_activities ?? null) : [],
        'eventLines' => $profileType === 'institution' ? $this->textLines($detail->major_events ?? null) : [],
        'collaborationLines' => $profileType === 'institution' ? $this->textLines($detail->collaborations ?? null) : [],
    ];

    return view('frontend.artist_detail', $data);
}




private function artFormNames($artFormIds, ?string $otherArtForm): array
{
    $names = $this->lookupNames('art_forms', (string) $artFormIds);

    $other = trim((string) $otherArtForm);

    if ($other !== '') {
        $names[] = $other;
    }

    return $names;
}

private function subgenreName($subgenreId, ?string $otherSubgenre): ?string
{
    $name = $subgenreId
        ? DB::table('art_forms')->where('id', $subgenreId)->value('name')
        : null;

    if ($name) {
        return $name;
    }

    $other = trim((string) $otherSubgenre);

    return $other !== '' ? $other : null;
}

private function groupTypeName(object $detail): ?string
{
    $name = $detail->group_type_id
        ? DB::table('art_group_types')->where('id', $detail->group_type_id)->value('name')
        : null;

    $name = $name ?: ($detail->group_type ?? null);

    if ($name === 'Other' && !empty($detail->other_group_type)) {
        return $detail->other_group_type;
    }

    return $name;
}

private function institutionTypeName(object $detail): ?string
{
    $name = $detail->institution_type ?? null;

    if ($name === 'Other' && !empty($detail->other_institution_type)) {
        return $detail->other_institution_type;
    }

    return $name;
}

private function lookupNames(string $table, ?string $csvIds): array
{
    $ids = array_values(array_filter(array_map(
        'intval',
        explode(',', (string) $csvIds)
    )));

    if ($ids === []) {
        return [];
    }

    return DB::table($table)->whereIn('id', $ids)->pluck('name')->all();
}

private function textLines(?string $text): array
{
    $lines = preg_split('/\r\n|\r|\n/', trim((string) $text)) ?: [];

    return array_values(array_filter(
        array_map('trim', $lines),
        fn(string $line) => $line !== ''
    ));
}

private function roleLine(array $parts): string
{
    $parts = array_values(array_filter(array_map(
        fn($part) => trim((string) $part),
        $parts
    ), fn(string $part) => $part !== ''));

    return $parts ? implode(' • ', $parts) : 'Not provided';
}

private function uploadUrl(?string $path): ?string
{
    $path = ltrim(trim((string) $path), '/');

    if ($path === '') {
        return null;
    }

    return Storage::disk('public_uploads')->exists($path)
        ? asset('uploads/' . $path)
        : null;
}

private function safeExternalUrl(?string $url): ?string
{
    $url = trim((string) $url);

    if ($url === '') {
        return null;
    }

    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }

    return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
}

private function nameInitials(?string $name): string
{
    $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
    $initials = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }

    return $initials !== '' ? $initials : 'A';
}

    public function profilePhoto(int $artist): StreamedResponse
    {
        $registration = $this->publicArtists()->findOrFail($artist);
        abort_unless(
            $registration->profile_photo_path && Storage::disk('local')->exists($registration->profile_photo_path),
            404
        );

        return Storage::disk('local')->response($registration->profile_photo_path);
    }

    private function publicArtists(): Builder
    {
        return ArtistRegistration::query()
            ->join('users as u', 'u.id', '=', 'artist_registrations.user_id')
            ->leftJoin('countries as c', 'c.id', '=', 'u.country_id')
            ->leftJoin('art_regions as reg', 'reg.id', '=', 'u.region_id')
            ->leftJoin('art_forms as af', 'af.id', '=', 'artist_registrations.art_form_id')
            ->leftJoin('art_forms as sf', 'sf.id', '=', 'artist_registrations.subgenre_id')
            ->where('artist_registrations.status', ArtistRegistration::STATUS_APPROVED)
            ->where('artist_registrations.public_profile', 1)
            ->where('u.verified_status', 1)
            ->whereIn('u.role_id', [5, 6, 7])
            ->select([
                'artist_registrations.*',
                'u.full_name as full_name',
                'u.country_id as country_id',
                'u.region_id as region_id',
                'c.name as country',
                'reg.name as region',
                'u.role_id as role_id',
                'artist_registrations.profile_photo as profile_photo_path',
                'artist_registrations.language_id as languages',
                'af.name as art_form_name',
                'sf.name as subgenre_name',
            ])
            ->selectRaw("COALESCE(NULLIF(artist_registrations.other_art_form, ''), af.name) as art_form")
            ->selectRaw("COALESCE(NULLIF(artist_registrations.other_subgenre, ''), sf.name) as subgenre")
            ->selectRaw("CASE u.role_id WHEN 5 THEN 'individual' WHEN 6 THEN 'group' WHEN 7 THEN 'institutional' ELSE 'individual' END as registration_type");
    }
	
	public function artFormsByCountry(Request $request)
	{ 
		$request->validate([
			'country' => ['required', 'integer', 'exists:countries,id'],
		]); 

		$artForms = DB::table('art_forms')
			->where('status', 1)
			->where('parent_id', 0)
			->where('country_id', $request->country)
			->orderBy('name')
			->get(['id', 'name']);

		return response()->json($artForms);
	}
}
