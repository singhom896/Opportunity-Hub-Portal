@extends('frontend.layouts.app')

@section('title', 'Explore Countries — BRICS Artist Registry')
@section('body-class', 'page-countries')

@section('content')

	
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Explore Countries</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Explore Member &amp; Partner Countries</span>
        </h1>
        <small class="brx-page-header__description"> Discover participating nations and explore artists, institutions, and cultural practitioners registered through the BRICS Artist Registry network.</small>
    </div>
</section>

<main class="evt-main cty-main py-4 py-lg-5">
    <div class="container">

        <div class="cty-intro mb-4">
            <span class="evt-kicker">PARTICIPATING NATIONS</span>
            <h2 class="evt-title h3 mb-2">Member &amp; Partner Country Profiles</h2>
            <p class="text-secondary mb-0">
                Select any country to explore its dedicated artist directory and approved creative practitioners.
            </p>
        </div>

        <section class="row g-4" aria-label="Countries Directory">
            @forelse ($countries as $country)
                <div class="col-12 col-sm-6 col-lg-4">
                    <article class="cty-card">
                        <div class="cty-card__flag">
                            <img src="{{ asset('assets/frontend/v1/images/flags/' . $country->flag_image) }}"
                                alt="{{ $country->name }} Flag" loading="lazy" onerror="this.style.display='none';">
                        </div>

                        <div class="cty-card__body">
                            <h3 class="cty-card__name">{{ $country->name }}</h3>
                            <p class="cty-card__text">Artists and cultural practitioners representing {{ $country->name }}.</p>

                            <div class="cty-card__actions">
                                <a class="cty-card__cta"
                                    href="{{ route('artist.list.country', ['id' => encrypt($country->id)]) }}">
                                    View Artists →
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="evt-empty cty-empty text-center py-5 px-3">
                        <h3 class="h5">No Countries Available</h3>
                        <p class="text-secondary mb-0">No active countries are currently available. Please check back later.</p>
                    </div>
                </div>
            @endforelse
        </section>

        <aside class="evt-info cty-note mt-5 p-4 d-flex gap-3">
            <i class="bi bi-info-circle-fill fs-5 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <h3 class="h6 fw-bold mb-1">Country-Specific Directory &amp; Nodal Verification:</h3>
                <p class="mb-0 text-secondary small">
                    Selecting a country filters the artist directory. Artist profiles are displayed according to their registration status and the verification procedures of the respective national Nodal Agency.
                </p>
            </div>
        </aside>

    </div>
</main>

@endsection
