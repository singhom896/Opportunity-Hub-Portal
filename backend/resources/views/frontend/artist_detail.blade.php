@extends('frontend.layouts.app')

@php
    $isIndividual = $profileType === 'individual';
    $isGroup = $profileType === 'group';
    $isInstitution = $profileType === 'institution';
    $pageName = $displayName ?: ($isIndividual ? 'Artist' : ($isGroup ? 'Artist Group' : 'Institution'));

    $profileLabels = [
        'individual' => ['title' => 'Artist Profile', 'role' => 'Individual Artist', 'about' => 'About the Artist'],
        'group' => ['title' => 'Artist Group Profile', 'role' => 'Artist Group', 'about' => 'About the Group'],
        'institution' => ['title' => 'Cultural Institution Profile', 'role' => 'Cultural Institution', 'about' => 'About the Institution'],
    ];
    $label = $profileLabels[$profileType] ?? $profileLabels['individual'];

    $details = [
        ['Registry ID', $user->regno ?: 'Not provided'],
        ['Country', $country?->name ?? 'Not provided'],
        ['Region / State', $region?->name ?? 'Not provided'],
        ['City', $user->city ?: 'Not provided'],
        ['Art Form(s)', !empty($artFormNames) ? implode(', ', $artFormNames) : 'Not provided'],
        ['Languages', !empty($languageNames) ? implode(', ', $languageNames) : 'Not provided'],
    ];

    if ($isIndividual) {
        $details = array_merge($details, [
            ['Specialisation / Subgenre', $subgenreName ?: 'Not provided'],
            ['Years of Practice', !empty($detail->years_of_practice) ? $detail->years_of_practice . ' years' : 'Not provided'],
            ['Teacher / Guru', $detail->teacher_guru ?: 'Not provided'],
            ['Institutional Affiliation', $detail->institutional_affiliation ?: 'Not provided'],
        ]);
    } elseif ($isGroup) {
        $details = array_merge($details, [
            ['Group Type', $typeName ?: 'Not provided'],
            ['Established Year', $detail->establishment_year ?: 'Not provided'],
            ['Total Members', $detail->total_members ?? 'Not provided'],
            ['Registration Number', $detail->registration_number ?: 'Not provided'],
            ['Representative', $user->full_name ?: 'Not provided'],
            ['Representative Designation', $user->designation ?: 'Not provided'],
        ]);
    } else {
        $details = array_merge($details, [
            ['Institution Type', $typeName ?: 'Not provided'],
            ['Established Year', $detail->establishment_year ?: 'Not provided'],
            ['Registration Type', $detail->institution_registration_type ?: 'Not provided'],
            ['Registration Number', $detail->registration_number ?: 'Not provided'],
            ['Associated Artists', $detail->associated_artists ?? 'Not provided'],
            ['Representative', $user->full_name ?: 'Not provided'],
            ['Representative Designation', $user->designation ?: 'Not provided'],
        ]);
    }

    $listSections = [];
    if ($isInstitution) {
        $listSections[] = ['bi bi-brush', 'Cultural Activities',  $culturalLines ?? [], 'No cultural activities provided.'];
        $listSections[] = ['bi bi-mortarboard', 'Training & Research Activities',  $trainingLines ?? [], 'No training or research activities provided.'];
        $listSections[] = ['bi bi-calendar-event', 'Major Events',  $eventLines ?? [], 'No major events provided.'];
        $listSections[] = ['bi bi-people', 'Collaborations', $collaborationLines ?? [], 'No collaborations provided.'];
    }
@endphp

@section('title', $pageName . ' – ' . $label['title'])
@section('body-class', 'page-artist-profile')

@section('content')
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <a class="brx-page-header__crumb-link" href="{{ $moreArtistsUrl }}">Artist Directory</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Profile</span>
        </nav>
        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>
        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">{{ $label['title'] }}</span>
        </h1>
        <small class="brx-page-header__description">Discover verified artists, groups and cultural institutions across BRICS countries.</small>
        @if ($flagUrl)
            <div class="adt-headflag">
                <img src="{{ $flagUrl }}" alt="{{ $country?->name ?? 'Country' }} flag" class="img-thumbnail">
            </div>
        @endif
    </div>
</section>

<main class="evt-main adt-main py-4 py-lg-5">
    <div class="container">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <a class="evt-back btn btn-outline-secondary btn-sm" href="{{ $moreArtistsUrl }}"><i class="bi bi-arrow-left me-1"></i>Back to Artist Directory</a>
            <span class="adt-regid small">Registry ID: <strong>{{ $user->regno ?: 'Not provided' }}</strong></span>
        </div>

        {{-- Profile header --}}
        <article class="adt-hero mb-4 mb-lg-5">
            <div class="row align-items-center g-3 g-md-4">
                <div class="col-12 col-md-auto">
                    <div class="adt-photo">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $pageName }}" class="adt-photo__img" loading="eager">
                        @else
                            <div class="adt-photo__initials d-flex align-items-center justify-content-center fw-bold">{{ $initials }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="adt-role badge"><i class="bi bi-person-badge me-1"></i>{{ $label['role'] }}</span>
                        <span class="adt-verified badge rounded-pill"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                    </div>
                    <h2 class="adt-name mb-2">{{ $pageName }}</h2>
                    @if ($typeName)
                        <p class="adt-type mb-2">{{ $typeName }}</p>
                    @endif
                    <div class="adt-chips d-flex flex-wrap gap-2 mb-2">
                        @forelse ($artFormNames as $artFormName)
                            <span class="adt-chip badge rounded-pill"><i class="bi bi-palette me-1"></i>{{ $artFormName }}</span>
                        @empty
                            <span class="adt-chip adt-chip--muted badge rounded-pill">Art form not provided</span>
                        @endforelse
                        @if ($isIndividual && $subgenreName)
                            <span class="adt-chip badge rounded-pill"><i class="bi bi-stars me-1"></i>{{ $subgenreName }}</span>
                        @endif
                        @if ($isIndividual && !empty($detail->years_of_practice))
                            <span class="adt-chip badge rounded-pill"><i class="bi bi-award me-1"></i>{{ $detail->years_of_practice }} years of practice</span>
                        @elseif (!$isIndividual && !empty($detail->establishment_year))
                            <span class="adt-chip badge rounded-pill"><i class="bi bi-calendar-event me-1"></i>Established {{ $detail->establishment_year }}</span>
                        @endif
                    </div>
                    <div class="adt-where d-flex flex-wrap gap-2">
                        @if ($country)
                            <span class="adt-where__item">
                                @if ($flagUrl)<img src="{{ $flagUrl }}" alt="" class="adt-where__flag">@else<i class="bi bi-globe2"></i>@endif
                                {{ $country->name }}
                            </span>
                        @endif
                        @if ($region)<span class="adt-where__item"><i class="bi bi-geo-alt"></i>{{ $region->name }}</span>@endif
                        @if ($user->city)<span class="adt-where__item"><i class="bi bi-building"></i>{{ $user->city }}</span>@endif
                    </div>
                    @if ($websiteUrl || !empty($portfolioUrls))
                        <div class="adt-actions d-flex flex-wrap gap-2 mt-3">
                            @if ($websiteUrl)
                                <a href="{{ $websiteUrl }}" class="adt-btn adt-btn--solid btn btn-primary btn-sm" target="_blank" rel="noopener noreferrer"><i class="bi bi-globe me-1"></i>Official Website</a>
                            @endif
                            @if (!empty($portfolioUrls))
                                <a href="{{ $portfolioUrls[0] }}" class="adt-btn adt-btn--line btn btn-outline-primary btn-sm" target="_blank" rel="noopener noreferrer"><i class="bi bi-collection me-1"></i>View Portfolio</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </article>

        <div class="row g-4 g-xl-5">

            {{-- Main column --}}
            <div class="col-12 col-lg-8">

                <section class="adt-card mb-4">
                    <div class="adt-card__head d-flex align-items-center gap-3">
                        <span class="adt-card__icon"><i class="bi bi-journal-richtext"></i></span>
                        <h2 class="adt-card__title mb-0">{{ $label['about'] }}</h2>
                    </div>
                    @if (trim((string) $aboutText) !== '')
                        <div class="adt-about text-break">{!! nl2br(e($aboutText)) !!}</div>
                    @else
                        <p class="adt-empty mb-0">About information has not been provided.</p>
                    @endif
                </section>

                @foreach ($listSections as [$sectionIcon, $sectionTitle, $sectionLines, $sectionEmpty])
                    <section class="adt-card mb-4">
                        <div class="adt-card__head d-flex align-items-center gap-3">
                            <span class="adt-card__icon"><i class="{{ $sectionIcon }}"></i></span>
                            <h2 class="adt-card__title mb-0">{{ $sectionTitle }}</h2>
                        </div>
                        @if (!empty($sectionLines))
                            <ul class="adt-list list-unstyled mb-0">
                                @foreach ($sectionLines as $sectionLine)<li class="adt-list__item">{{ $sectionLine }}</li>@endforeach
                            </ul>
                        @else
                            <p class="adt-empty mb-0">{{ $sectionEmpty }}</p>
                        @endif
                    </section>
                @endforeach

                <section class="adt-card adt-card--gold mb-4">
                    <div class="adt-card__head d-flex align-items-center gap-3">
                        <span class="adt-card__icon"><i class="bi bi-trophy"></i></span>
                        <h2 class="adt-card__title mb-0">Awards & Achievements</h2>
                    </div>
                    @if ($awards->isNotEmpty())
                        <div class="row g-3">
                            @foreach ($awards as $award)
                                <div class="col-12 col-md-6">
                                    <div class="adt-award h-100">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <h3 class="adt-award__name mb-1">{{ $award->award_name ?: 'Award / Recognition' }}</h3>
                                            @if ($award->award_year)<span class="adt-award__year badge">{{ $award->award_year }}</span>@endif
                                        </div>
                                        @if ($award->description)<p class="adt-award__desc mb-2">{{ $award->description }}</p>@endif
                                        @if ($award->certificate_path)
                                            <a href="{{ asset('uploads/' . ltrim($award->certificate_path, '/')) }}" target="_blank" rel="noopener noreferrer" class="adt-award__link small"><i class="bi bi-file-earmark-pdf me-1"></i>View certificate</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="adt-empty mb-0">No award records have been provided.</p>
                    @endif
                    @if (!empty($awardLines))
                        <div class="adt-extra mt-4">
                            <h3 class="adt-extra__title">Additional recognitions</h3>
                            <ul class="adt-list list-unstyled mb-0">@foreach ($awardLines as $awardLine)<li class="adt-list__item">{{ $awardLine }}</li>@endforeach</ul>
                        </div>
                    @endif
                </section>

                <section class="adt-card mb-4 mb-lg-0">
                    <div class="adt-card__head d-flex align-items-center gap-3">
                        <span class="adt-card__icon"><i class="bi bi-link-45deg"></i></span>
                        <h2 class="adt-card__title mb-0">Portfolio & Online Presence</h2>
                    </div>
                    @if (!empty($portfolioUrls))
                        <div class="adt-links d-grid gap-2">
                            @foreach ($portfolioUrls as $portfolioUrl)
                                <a class="adt-links__item text-break" href="{{ $portfolioUrl }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right me-2"></i>{{ $portfolioUrl }}</a>
                            @endforeach
                        </div>
                    @else
                        <p class="adt-empty mb-0">No portfolio links have been provided.</p>
                    @endif
                </section>

            </div>

            {{-- Details column --}}
            <div class="col-12 col-lg-4">
                <aside class="adt-card adt-card--aside adt-sticky">
                    <div class="adt-card__head d-flex align-items-center gap-3">
                        <span class="adt-card__icon"><i class="bi bi-card-list"></i></span>
                        <h2 class="adt-card__title mb-0">Professional & Registration Details</h2>
                    </div>
                    <dl class="adt-facts mb-0">
                        @foreach ($details as [$detailLabel, $detailValue])
                            <div class="adt-facts__row">
                                <dt>{{ $detailLabel }}</dt>
                                <dd class="text-break">{{ $detailValue }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </aside>
            </div>

        </div>

        <div class="d-flex justify-content-end mt-4 mt-lg-5">
            <a class="evt-back btn btn-outline-secondary" href="{{ $moreArtistsUrl }}"><i class="bi bi-arrow-left me-1"></i>Back to Artist Directory</a>
        </div>
    </div>
</main>
@endsection
