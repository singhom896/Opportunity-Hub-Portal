@extends('frontend.layouts.app')

@section('title', 'Contact Us & Country Nodal Officers')
@section('body-class', 'page-contact-us')

@section('content')



<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Contact Us</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Contact &amp; Administrative Support</span>
        </h1>
        <small class="brx-page-header__description">Official publication of the BRICS Artist Registry pilot initiative.</small>
    </div>
</section>

<main class="evt-main ctc-main py-4 py-lg-5">
    <div class="container">

        {{-- Introduction Notice --}}
        <div class="evt-info ctc-intro p-4 d-flex align-items-start gap-3 mb-5">
            <i class="bi bi-info-circle-fill fs-4 flex-shrink-0" aria-hidden="true"></i>
            <p class="mb-0 text-secondary">
                For assistance related to registration, artist profiles, opportunities, technical issues and other matters concerning the BRICS Artist Registry, please contact the dedicated teams below.
            </p>
        </div>

        {{-- Technical Support & Administrative Team --}}
        <div class="row g-4 mb-5">
            {{-- Technical Support Team --}}
            <div class="col-12 col-lg-6">
                <section class="ctc-section h-100 d-flex flex-column" aria-labelledby="technicalSupportTitle">
                    <div class="ctc-heading mb-3">
                        <span class="evt-kicker">HELP &amp; ASSISTANCE</span>
                        <h2 id="technicalSupportTitle" class="evt-title h4 mb-0">Technical Support Team</h2>
                    </div>

                    <article class="ctc-card flex-grow-1">
                        <div class="ctc-card__head d-flex align-items-center gap-3 mb-4 pb-3">
                            <span class="ctc-card__icon d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle" aria-hidden="true">
                                <i class="bi bi-gear-wide-connected"></i>
                            </span>
                            <h3 class="ctc-card__title h5 mb-0">Portal Helpdesk</h3>
                        </div>

                        <div class="ctc-details d-flex flex-column gap-3">
                            <div class="ctc-row row g-2">
                                <div class="col-12 col-sm-3"><span class="ctc-label fw-bold small text-uppercase">Email</span></div>
                                <div class="col-12 col-sm-9"><a class="ctc-value text-break text-decoration-none fw-semibold" href="mailto:techsupport-culture@gov.in">techsupport-culture@gov.in</a></div>
                            </div>

                            <div class="ctc-row row g-2">
                                <div class="col-12 col-sm-3"><span class="ctc-label fw-bold small text-uppercase">Phone</span></div>
                                <div class="col-12 col-sm-9"><span class="ctc-value">+91-11-2307 8000</span></div>
                            </div>

                            <div class="ctc-row row g-2">
                                <div class="col-12 col-sm-3"><span class="ctc-label fw-bold small text-uppercase">Timings</span></div>
                                <div class="col-12 col-sm-9"><span class="ctc-value">Monday – Friday, 9:30 AM – 6:00 PM (IST)</span></div>
                            </div>
                        </div>
                    </article>
                </section>
            </div>

            {{-- Administrative Team --}}
            <div class="col-12 col-lg-6">
                <section class="ctc-section h-100 d-flex flex-column" aria-labelledby="administrativeTeamTitle">
                    <div class="ctc-heading mb-3">
                        <span class="evt-kicker">REGISTRY MANAGEMENT</span>
                        <h2 id="administrativeTeamTitle" class="evt-title h4 mb-0">Administrative Secretariat</h2>
                    </div>

                    <article class="ctc-card flex-grow-1">
                        <div class="ctc-card__head d-flex align-items-center gap-3 mb-4 pb-3">
                            <span class="ctc-card__icon d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle" aria-hidden="true">
                                <i class="bi bi-building"></i>
                            </span>
                            <h3 class="ctc-card__title h5 mb-0">Central Administration</h3>
                        </div>

                        <div class="ctc-details d-flex flex-column gap-3">
                            <div class="ctc-row row g-2">
                                <div class="col-12 col-sm-3"><span class="ctc-label fw-bold small text-uppercase">Officer</span></div>
                                <div class="col-12 col-sm-9"><span class="ctc-value ctc-value--strong fw-semibold">Central Nodal Officer</span></div>
                            </div>

                            <div class="ctc-row row g-2">
                                <div class="col-12 col-sm-3"><span class="ctc-label fw-bold small text-uppercase">Ministry</span></div>
                                <div class="col-12 col-sm-9"><span class="ctc-value">Ministry of Culture, Government of India</span></div>
                            </div>

                            <div class="ctc-row row g-2">
                                <div class="col-12 col-sm-3"><span class="ctc-label fw-bold small text-uppercase">Email</span></div>
                                <div class="col-12 col-sm-9"><a class="ctc-value text-break text-decoration-none fw-semibold" href="mailto:brics-admin@gov.in">brics-admin@gov.in</a></div>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
        </div>

        {{-- BRICS Country Nodal Officers --}}
        <section class="ctc-section mt-5" aria-labelledby="nodalOfficersTitle">
            <div class="ctc-heading mb-3">
                <span class="evt-kicker">COUNTRY CONTACTS</span>
                <h2 id="nodalOfficersTitle" class="evt-title h4 mb-0">BRICS Country Nodal Officers</h2>
            </div>

            <div class="evt-info ctc-intro ctc-intro--compact p-3 d-flex align-items-start gap-3 mb-4">
                <i class="bi bi-info-circle-fill flex-shrink-0" aria-hidden="true"></i>
                <p class="mb-0 text-secondary small">For country-specific queries, artists and organisations may contact the designated nodal officer of the concerned BRICS member country.</p>
            </div>

            <div class="row g-4">
                @php
                    $countryOfficers = [
                        ['name' => 'India', 'email' => 'india@brics-artist-registry.org', 'phone' => '+91-11-2307 8000'],
                        ['name' => 'Russia', 'email' => 'russia@brics-artist-registry.org', 'phone' => '+7-495-XXX-XXXX'],                     
                        ['name' => 'South Africa', 'email' => 'southafrica@brics-artist-registry.org', 'phone' => '+27-12-XXX-XXXX'],              
                        ['name' => 'United Arab Emirates', 'email' => 'uae@brics-artist-registry.org', 'phone' => '+971-2-XXX-XXXX'],                      
                    ];
                @endphp

                @foreach ($countryOfficers as $officer)
                    <div class="col-12 col-sm-6 col-xl-3">
                        <article class="ctc-officer h-100">
                            <div class="ctc-officer__head d-flex align-items-center gap-3 mb-2">
                                <span class="ctc-officer__mark d-inline-flex align-items-center justify-content-center flex-shrink-0 rounded-circle" aria-hidden="true">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                <h3 class="ctc-officer__name h5 mb-0">{{ $officer['name'] }}</h3>
                            </div>

                            <p class="ctc-officer__role small mb-3">Designated Nodal Agency</p>

                            <div class="ctc-officer__details small d-flex flex-column gap-2">
                                <p class="mb-0">
                                    <strong>Designation:</strong> Nodal Officer
                                </p>
                                <p class="mb-0">
                                    <strong>Email:</strong>
                                    <a href="mailto:{{ $officer['email'] }}" class="text-break text-decoration-none">
                                        {{ $officer['email'] }}
                                    </a>
                                </p>
                                <p class="mb-0">
                                    <strong>Phone:</strong> {{ $officer['phone'] }}
                                </p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

    </div>
</main>
@endsection
