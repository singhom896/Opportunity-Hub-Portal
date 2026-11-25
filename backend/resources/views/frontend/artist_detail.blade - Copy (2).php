@extends('frontend.layouts.app')

@php
    $isIndividual  = $profileType === 'individual';
    $isGroup       = $profileType === 'group';
    $isInstitution = $profileType === 'institution';

    $pageName = $displayName ?: ($isIndividual ? 'Artist' : ($isGroup ? 'Artist Group' : 'Institution'));

    $profileLabels = [
        'individual'  => ['title' => 'Artist Profile',      'role' => 'Artist',     'about' => 'About the Artist',      'verified' => 'Verified Artist',      'aboutSub' => "Discover the artist's background and creative journey."],
        'group'       => ['title' => 'Group Profile',       'role' => 'Artist Group',          'about' => 'About the Group',       'verified' => 'Verified Group',       'aboutSub' => "Discover the group's background and creative journey."],
        'institution' => ['title' => 'Institution Profile', 'role' => 'Institute / Organization',  'about' => 'About the Institution', 'verified' => 'Verified Institution', 'aboutSub' => "Discover the institution's background and activities."],
    ];
    $label = $profileLabels[$profileType];

    // Professional detail items (role specific).
    $details = [
        ['Registry ID', $user->regno ?: 'Not provided'],
        ['Country', $country?->name ?? 'Not provided'],
        ['Region', $region?->name ?? 'Not provided'],
        ['City', $user->city ?: 'Not provided'],
        [$isIndividual ? 'Art Form' : 'Art Forms', !empty($artFormNames) ? implode(', ', $artFormNames) : 'Not provided'],
    ];

    if ($isIndividual) {
        $details[] = ['Specialisation', $subgenreName ?: 'Not provided'];
        $details[] = ['Experience', !empty($detail->years_of_practice) ? $detail->years_of_practice . ' Years' : 'Not provided'];
        $details[] = ['Teacher / Guru', $detail->teacher_guru ?: 'Not provided'];
        $details[] = ['Institutional Affiliation', $detail->institutional_affiliation ?: 'Not provided'];
    } elseif ($isGroup) {
        $details[] = ['Group Type', $typeName ?: 'Not provided'];
        $details[] = ['Established', $detail->establishment_year ?: 'Not provided'];
        $details[] = ['Total Members', $detail->total_members ?: 'Not provided'];
        $details[] = ['Registration Number', $detail->registration_number ?: 'Not provided'];
    } else {
        $details[] = ['Institution Type', $typeName ?: 'Not provided'];
        $details[] = ['Established', $detail->establishment_year ?: 'Not provided'];
        $details[] = ['Registration Type', $detail->institution_registration_type ?: 'Not provided'];
        $details[] = ['Registration Number', $detail->registration_number ?: 'Not provided'];
        $details[] = ['Associated Artists', $detail->associated_artists ?? 'Not provided'];
    }

    $details[] = ['Profile Updated', $detail->updated_at ? \Carbon\Carbon::parse($detail->updated_at)->format('d-m-Y h:i A') : 'Not provided'];

    // List sections (role specific): [icon, title, subtitle, lines, empty text]
    $listSections = [];

    if ($isInstitution) {
        $listSections[] = ['✺', 'Cultural Activities', 'Cultural programmes and initiatives.', $culturalLines, 'No cultural activities provided.'];
        $listSections[] = ['✎', 'Training & Research Activities', 'Training, research and capacity-building work.', $trainingLines, 'No training or research activities provided.'];
    }

    $listSections[] = ['★', 'Awards & Achievements', $isIndividual ? "Recognitions and milestones in the artist's career." : 'Recognitions and milestones.', $awardLines, 'No awards or achievements provided.'];

    if ($isInstitution) {
        $listSections[] = ['❖', 'Selected Exhibitions', 'Major events and exhibitions.', $eventLines, 'No exhibitions or events provided.'];
        $listSections[] = ['⚭', 'Collaborations', 'Partnerships and collaborations.', $collaborationLines, 'No collaborations provided.'];
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
            <span class="brx-page-header__crumb-current" aria-current="page">Artist Detials</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>
		

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Artist Detials</span> 
        </h1>
		
        <small class="brx-page-header__description">Discover verified artists, performing troupes, and cultural practitioners from across the BRICS countries.</small> 
			
				
		  <div class="directory-flag">
			@if ($flagUrl)
				<img src="{{ $flagUrl }}" alt="{{ $country?->name ?? 'Country' }} flag">
			@else
			 <span class="d-block small text-center text-white-50 mt-1">{{ $country?->name ?? '' }}</span>
			@endif
		</div>
		
    </div>
</section>



{{-- Main Profile Content --}}
<main class="profile-main py-4 py-lg-5">
    <div class="wrap">

        <div class="profile-toolbar d-flex justify-content-between align-items-center mb-4">
            <a class="back-link btn btn-outline-secondary btn-sm" href="{{ $moreArtistsUrl }}">
                <span aria-hidden="true">←</span> Back to Artist Directory
            </a>
            <span class="text-muted small">Registry ID: <strong>{{ $user->regno ?: 'Pending' }}</strong></span>
        </div>
	


        <div class="row g-4 profile-layout">
            <div class="col-12">

				<div class="row g-4 profile-layout">
					<div class="col-12">

						<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
							<div class="card-body p-3 p-md-4">

								<div class="row align-items-center g-4">

									{{-- Artist Photo --}}
									<div class="col-auto">
										<div class="rounded-circle border border-3 border-warning p-1 shadow-sm"
											 style="width: 120px; height: 120px;">

											@if ($photoUrl)
												<img src="{{ $photoUrl }}"
													 alt="{{ $pageName }}"
													 loading="eager"
													 class="rounded-circle w-100 h-100 object-fit-cover">
											@else
												<div class="rounded-circle bg-light text-dark w-100 h-100 d-flex align-items-center justify-content-center fw-bold fs-2">
													{{ $initials }}
												</div>
											@endif

										</div>
									</div>

									{{-- Artist Information --}}
									<div class="col">

										{{-- Name and Verified Badge --}}
										<div class="d-flex align-items-center flex-wrap gap-2 mb-2">

											<h1 class="h3 fw-bold text-dark mb-0"
												id="profileNameTitle">
												{{ $displayName ?: 'Not provided' }}
											</h1>

											<span class="badge rounded-circle bg-success d-inline-flex align-items-center justify-content-center"
												  style="width: 23px; height: 23px;"
												  title="{{ $label['verified'] }}"
												  aria-label="{{ $label['verified'] }}">
												<i class="bi bi-check-lg"></i>
											</span>

										</div>

										{{-- Role --}}
										<p class="text-danger fw-semibold mb-3">
											<i class="bi bi-person-badge me-1"></i>
											{{ $label['role'] }}
										</p>

										{{-- Artist Tags --}}
										<div class="d-flex flex-wrap gap-2">

											@if (!$isIndividual && $typeName)
												<span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-medium">
													<i class="bi bi-building text-danger me-1"></i>
													{{ $typeName }}
												</span>
											@endif

											@forelse ($artFormNames as $artFormName)
												<span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-medium">
													<i class="bi bi-palette text-warning me-1"></i>
													{{ $artFormName }}
												</span>
											@empty
												@if ($isIndividual || !$typeName)
													<span class="badge rounded-pill bg-light text-secondary border px-3 py-2 fw-medium">
														<i class="bi bi-palette me-1"></i>
														Art form not provided
													</span>
												@endif
											@endforelse

											@if ($isIndividual && $subgenreName)
												<span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-medium">
													<i class="bi bi-stars text-warning me-1"></i>
													{{ $subgenreName }}
												</span>
											@endif

											@if ($isIndividual && !empty($detail->years_of_practice))
												<span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-medium">
													<i class="bi bi-award text-success me-1"></i>
													{{ $detail->years_of_practice }} Years Experience
												</span>
											@endif

											@if (!$isIndividual && !empty($detail->establishment_year))
												<span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-medium">
													<i class="bi bi-calendar-event text-primary me-1"></i>
													Established {{ $detail->establishment_year }}
												</span>
											@endif

										</div>

									</div>

								</div>

							</div>
						</div>

					</div>
				</div>
                {{-- About --}}
                <section class="profile-card mb-4">
                    <div class="profile-card-heading">
                        <div>
                            <h2 class="d-flex align-items-center gap-2 mb-1">
                                <span class="profile-section-icon d-inline-flex align-items-center justify-content-center flex-shrink-0" aria-hidden="true">✦</span>
                                {{ $label['about'] }}
                            </h2>
                            <p class="mb-0">{{ $label['aboutSub'] }}</p>
                        </div>
                    </div>

                    <div class="profile-card-body">
                        @if (trim((string) $aboutText) !== '')
                            <p class="profile-about-text">{!! nl2br(e($aboutText)) !!}</p>
                        @else
                            <p class="profile-empty">About information has not been provided.</p>
                        @endif
                    </div>
                </section>

                {{-- Professional Details --}}
                <section class="profile-card mb-4">
                    <div class="profile-card-heading">
                        <div>
                            <h2 class="d-flex align-items-center gap-2 mb-1">
                                <span class="profile-section-icon d-inline-flex align-items-center justify-content-center flex-shrink-0" aria-hidden="true">▤</span>
                                Professional Details
                            </h2>
                            <p class="mb-0">Registration and verified background information.</p>
                        </div>
                    </div>

                    <div class="profile-card-body">
                        <div class="row g-3">
                            @foreach ($details as [$detailLabel, $detailValue])
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <div class="detail-item">
                                        <small>{{ $detailLabel }}</small>
                                        <strong>{{ $detailValue }}</strong>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="detail-item">
                                    <small>Languages</small>
                                    <div class="profile-language-list mt-1">
                                        @forelse ($languageNames as $language)
                                            <span class="profile-tag">{{ $language }}</span>
                                        @empty
                                            <span class="profile-muted small">Not provided</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($websiteUrl || ($isInstitution ? false : !empty($portfolioUrls)))
                            <div class="profile-portfolio d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                                @if ($websiteUrl)
                                    <a href="{{ $websiteUrl }}" class="profile-action" target="_blank" rel="noopener noreferrer">
                                        Visit Official Website
                                        <span aria-hidden="true">↗</span>
                                    </a>
                                @endif

                                @if (!$isInstitution && !empty($portfolioUrls))
                                    <a href="{{ $portfolioUrls[0] }}" class="profile-action" target="_blank" rel="noopener noreferrer">
                                        {{ $isIndividual ? 'View Artist Portfolio' : 'View Portfolio' }}
                                        <span aria-hidden="true">↗</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Portfolio Links (institution may have several) --}}
                @if ($isInstitution && !empty($portfolioUrls))
                    <section class="profile-card mb-4">
                        <div class="profile-card-heading">
                            <div>
                                <h2 class="d-flex align-items-center gap-2 mb-1">
                                    <span class="profile-section-icon d-inline-flex align-items-center justify-content-center flex-shrink-0" aria-hidden="true">↗</span>
                                    Portfolio Links
                                </h2>
                                <p class="mb-0">Online portfolios and showcases.</p>
                            </div>
                        </div>

                        <div class="profile-card-body">
                            <ul class="link-list mb-0">
                                @foreach ($portfolioUrls as $portfolioItemUrl)
                                    <li>
                                        <a href="{{ $portfolioItemUrl }}" target="_blank" rel="noopener noreferrer">
                                            {{ $portfolioItemUrl }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </section>
                @endif

                {{-- Role specific list sections --}}
                @foreach ($listSections as [$sectionIcon, $sectionTitle, $sectionSub, $sectionLines, $sectionEmpty])
                    <section class="profile-card mb-4">
                        <div class="profile-card-heading">
                            <div>
                                <h2 class="d-flex align-items-center gap-2 mb-1">
                                    <span class="profile-section-icon d-inline-flex align-items-center justify-content-center flex-shrink-0" aria-hidden="true">{{ $sectionIcon }}</span>
                                    {{ $sectionTitle }}
                                </h2>
                                <p class="mb-0">{{ $sectionSub }}</p>
                            </div>
                        </div>

                        <div class="profile-card-body">
                            <ul class="link-list mb-0">
                                @forelse ($sectionLines as $sectionLine)
                                    <li>{{ $sectionLine }}</li>
                                @empty
                                    <li class="profile-empty">{{ $sectionEmpty }}</li>
                                @endforelse
                            </ul>
                        </div>
                    </section>
                @endforeach

            </div>
        </div>
    </div>
</main>
@endsection
