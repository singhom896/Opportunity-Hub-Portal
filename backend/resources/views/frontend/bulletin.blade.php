@extends('frontend.layouts.app')

@section('title', 'Bulletin & Opportunities')
@section('body-class', 'page-bulletin')

@section('content')

<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">

        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Bulletin</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Cultural Bulletin &amp; Opportunities</span>
        </h1>
        <small class="brx-page-header__description">Opportunities, cultural events, residencies, and official updates from across BRICS Member and Partner Countries.</small> 
    </div>
</section>

<main class="evt-main bul-main py-4 py-lg-5">
    <div class="container">

        {{-- Opportunities --}}
        <section class="bul-section mb-5" aria-labelledby="opportunitiesTitle">
            <div class="bul-heading mb-4">
                <span class="evt-kicker">DISCOVER &amp; PARTICIPATE</span>
                <h2 id="opportunitiesTitle" class="evt-title h3 mb-2">Opportunities &amp; Cultural Events</h2>
                <p class="text-secondary mb-0">Explore cultural programmes, exhibitions, and collaborative initiatives for artists.</p>
            </div>

            <div class="row g-4 bul-grid">
                @forelse ($documents as $rsdata)

                    @php
                        $documentLink = null;

                        if ((int) $rsdata->document_type === 2) {
                            $documentLink = $rsdata->document_url;
                        } elseif ((int) $rsdata->document_type === 1 && optional($rsdata->asset)->url) {
                            $documentLink = asset($rsdata->asset->url);
                        }

                        $documentSize = '—';

                        if ((int) $rsdata->document_type === 1 && optional($rsdata->asset)->size) {
                            $documentSize = function_exists('formatBytes')
                                ? formatBytes($rsdata->asset->size)
                                : (($rsdata->asset->size_mb ?? '—') . ' MB');
                        } elseif ((int) $rsdata->document_type === 2) {
                            $documentSize = 'External Link';
                        }

                        $publishedDate = $rsdata->publish_start_date
                            ? \Carbon\Carbon::parse($rsdata->publish_start_date)->format('d-m-Y')
                            : '—';
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="bul-card h-100 d-flex flex-column">

                            <!--span class="bulletin-tag align-self-start mb-3">
                                {{ (int) $rsdata->document_type === 1 ? 'PDF Document' : 'External Link' }}
                            </span-->

                            <div class="bul-card__top d-flex align-items-center justify-content-between gap-3">
                                <span class="bul-card__icon" aria-hidden="true">
                                    <i class="bi {{ (int) $rsdata->document_type === 2 ? 'bi-link-45deg' : 'bi-file-earmark-text' }}"></i>
                                </span>

                                <div class="bul-card__meta small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Published: {{ $publishedDate }}
                                </div>

                                <!--div class="mt-1">
                                    <i class="bi bi-file-earmark me-1"></i>
                                    {{ $documentSize }}
                                </div-->
                            </div>

                            <div class="bul-card__body d-flex flex-column flex-grow-1">
                                <h3 class="bul-card__title h5 mb-2">
                                    {{ $rsdata->title ?? 'Untitled Document' }}
                                </h3>

                                <p class="bul-card__text text-secondary flex-grow-1 mb-0">
                                    {{ $rsdata->description ?? 'Access the official document published by the BRICS Artist Registry.' }}
                                </p>
                            </div>

                            <div class="bul-card__foot">
                                @if ($documentLink)
                                    <a class="bul-card__link fw-semibold"
                                       href="{{ $documentLink }}"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        View Details <span aria-hidden="true">→</span>
                                    </a>
                                @else
                                    <span class="bul-card__na fw-semibold">
                                        Document Not Available
                                    </span>
                                @endif
                            </div>

                        </article>
                    </div>

                @empty
                    <div class="col-12">
                        <div class="evt-empty text-center py-5 px-3 text-secondary">
                            No documents are currently available.
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- News --}}
        <!--section class="bulletin-section mb-5" aria-labelledby="newsTitle">
            <div class="section-heading mb-4">
                <div>
                    <span class="bulletin-kicker">LATEST DEVELOPMENTS</span>
                    <h2 id="newsTitle" class="h3 mb-1">News &amp; Media Highlights</h2>
                    <p class="text-secondary mb-0">Recent milestones and announcements across BRICS cultural and creative cooperation.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <article class="news-card h-100 p-4 bg-white border-0 shadow-sm">
                        <div class="news-date small text-muted mb-2">11 September 2026 · TV BRICS</div>
                        <h3 class="h5 mb-3">Republic of India to promote creative industries abroad through TV BRICS</h3>
                        <p class="text-secondary mb-3">
                            The National Institute of Design (NID), Ahmedabad, has entered into cooperation
                            with TV BRICS to develop creative exports and strengthen cultural and educational
                            cooperation among BRICS countries.
                        </p>

                        <details class="news-details mb-3">
                            <summary class="fw-semibold text-primary">Read full coverage</summary>
                            <div class="news-details-content pt-2 text-secondary small">
                                <p>
                                    The partnership focuses on cultural and humanitarian ties, national identity,
                                    professional contacts and expert communities in education, design and film.
                                    TV BRICS said cooperation with NID can help promote India's creative potential,
                                    including animation and film production, and provide an international platform
                                    for works by young Indian filmmakers.
                                </p>
                                <p class="mb-0">
                                    NID was founded in 1961 in Ahmedabad and today has campuses in Ahmedabad,
                                    Gandhinagar and Bengaluru, offering programmes across industrial,
                                    communication, textile, digital and other design disciplines.
                                </p>
                            </div>
                        </details>

                        <div class="news-source small">
                            Source:
                            <a href="https://tvbrics.com/en/news/republic-of-india-to-promote-creative-industries-abroad-through-tv-brics/"
                               target="_blank" rel="noopener" class="text-decoration-none fw-semibold">TV BRICS ↗</a>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-lg-6">
                    <article class="news-card h-100 p-4 bg-white border-0 shadow-sm">
                        <div class="news-date small text-muted mb-2">BRICS Culture Ministers’ Meeting · Bhopal</div>
                        <h3 class="h5 mb-3">BRICS countries adopt Bhopal Declaration on Cultural Cooperation</h3>
                        <p class="text-secondary mb-3">
                            The 11th BRICS Culture Ministers’ Meeting concluded in Bhopal with the adoption
                            of the Bhopal Declaration on cooperation in culture and creative industries.
                        </p>

                        <details class="news-details mb-3">
                            <summary class="fw-semibold text-primary">Read full coverage</summary>
                            <div class="news-details-content pt-2 text-secondary small">
                                <p>
                                    The declaration covers exchange of experience and best practices in cultural
                                    and creative industries, support for cultural practitioners, digital tools
                                    for market access, cooperation through UNESCO, research into provenance and
                                    return of cultural property, and protection of traditional knowledge and
                                    cultural expression.
                                </p>
                                <p>
                                    Participants also discussed digitisation and preservation of documentary
                                    heritage through cooperation among museums, libraries and archives, as well
                                    as copyright protection, transparency and fair remuneration when creative
                                    works are used in AI systems.
                                </p>
                                <p>
                                    India announced a pilot project for a voluntary register of artists and other
                                    creative professionals, intended to promote cultural exchange and expand
                                    professional opportunities.
                                </p>
                                <p class="mb-0">
                                    The BRICS Culture Festival was held in Bhopal on 6–7 August and included
                                    participation from BRICS countries and partner states.
                                </p>
                            </div>
                        </details>

                        <div class="news-source small text-muted">
                            Source: Information supplied for the BRICS Artist Registry bulletin.
                        </div>
                    </article>
                </div>
            </div>
        </section-->

        {{-- Information Note --}}
        <aside class="evt-info bul-info p-4 d-flex align-items-start gap-3" aria-label="Important information for artists">
            <div class="fs-4 flex-shrink-0" aria-hidden="true">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div>
                <h3 class="h6 fw-bold mb-1">Notice for Artists &amp; Cultural Practitioners</h3>
                <p class="mb-0 text-secondary">
                    Please check individual announcements and the issuing organisation’s official website
                    for eligibility, submission deadlines, requirements, and any amendments before applying.
                </p>
            </div>
        </aside>

    </div>
</main>
@endsection
