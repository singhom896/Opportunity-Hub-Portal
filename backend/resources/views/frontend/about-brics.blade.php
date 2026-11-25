@extends('frontend.layouts.app')

@section('title', 'About the BRICS Artist Registry Pilot Initiative')
@section('body-class', 'artist-registry-page')

@section('content')

<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">

        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">About the BRICS Artist Registry</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">About the BRICS Artist Registry</span>
            <span class="brx-page-header__title-accent">Pilot Initiative</span>     
        </h1>
        <!--small class="brx-page-header__description">A landmark cultural initiative under India's BRICS Chairship 2026 connecting creative communities across member and partner nations.</small--> 
    </div>
</section>

<main class="evt-main abt-main py-4 py-lg-5">
    <div class="container">

        <section class="abt-intro">
            <div class="abt-intro__ornament" aria-hidden="true"></div>
            <p class="abt-intro__lead">A significant initiative of India’s BRICS Chairship in 2026 was to create and develop an Artist Registry on a pilot basis for voluntarily participating BRICS members. The initiative was adopted by BRICS Member and Partner Countries through the Declaration of the XI BRICS Culture Ministers’ Meeting, adopted in Bhopal on 8 August 2026. It was subsequently reflected in the New Delhi Declaration adopted by BRICS Leaders at the XVIII BRICS Summit on 12 September 2026.</p>

            <p class="abt-intro__text">The BRICS Artist Registry is a pilot initiative designed to connect artists, performers, craftspeople and cultural organisations across participating BRICS countries. It provides a voluntary digital space to discover artistic talent, enhance professional visibility, and encourage cultural exchange and collaboration.</p>

            <p class="abt-intro__text">Recognising culture as an important pillar of BRICS cooperation, the Registry seeks to bring greater visibility to the diverse artistic traditions, cultural expressions and contemporary creative practices of participating countries. Through a searchable directory of artist and organisational profiles, it aims to facilitate connections among creative communities, cultural institutions, festivals, curators and other stakeholders, while strengthening mutual understanding and people-to-people engagement.</p>

            <p class="abt-intro__text mb-0">The initiative builds on the shared commitment of BRICS countries to exchange information and best practices, strengthen cultural cooperation, and explore opportunities for joint projects and professional engagement.</p>
        </section>

        <section class="abt-section" aria-labelledby="registry-why-heading">
            <div class="abt-heading">
                <span class="abt-heading__number">01</span>
                <div>
                    <h2 class="abt-heading__title" id="registry-why-heading">Why The BRICS Artist Registry?</h2>
                    <p class="abt-heading__sub text-secondary mb-0">The Registry seeks to achieve core cultural objectives:</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="abt-card abt-benefit">
                        <span class="abt-benefit__index">01</span>
                        <h3 class="abt-card__title">Enhance visibility</h3>
                        <p class="abt-card__text">Improve the discoverability of artists and cultural organisations across participating BRICS countries.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="abt-card abt-benefit">
                        <span class="abt-benefit__index">02</span>
                        <h3 class="abt-card__title">Encourage cultural exchange</h3>
                        <p class="abt-card__text">Promote awareness and appreciation of the diverse artistic traditions and cultural expressions of participating countries.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="abt-card abt-benefit">
                        <span class="abt-benefit__index">03</span>
                        <h3 class="abt-card__title">Facilitate professional networking</h3>
                        <p class="abt-card__text">Enable cultural institutions, festivals, curators and other stakeholders to discover artists and their work.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="abt-card abt-benefit">
                        <span class="abt-benefit__index">04</span>
                        <h3 class="abt-card__title">Foster artistic collaboration</h3>
                        <p class="abt-card__text">Provide a foundation for connections and potential collaborative initiatives across borders.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="abt-card abt-benefit">
                        <span class="abt-benefit__index">05</span>
                        <h3 class="abt-card__title">Share opportunities</h3>
                        <p class="abt-card__text">Disseminate information about cultural festivals, exhibitions, residencies, fellowships, grants and other relevant opportunities through a dedicated bulletin.</p>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="abt-card abt-benefit">
                        <span class="abt-benefit__index">06</span>
                        <h3 class="abt-card__title">Strengthen people-to-people ties</h3>
                        <p class="abt-card__text">Support closer engagement among the creative communities of participating BRICS countries.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="abt-section" aria-labelledby="registry-who-heading">
            <div class="abt-heading">
                <span class="abt-heading__number">02</span>
                <div>
                    <h2 class="abt-heading__title" id="registry-who-heading">Who Can Register?</h2>
                    <p class="abt-heading__sub text-secondary mb-0">The proposed pilot framework provides for two broad categories of registrants, subject to recognition by the relevant participating country.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <article class="abt-card abt-eligible">
                        <div class="abt-eligible__symbol" aria-hidden="true">01</div>
                        <div>
                            <h3 class="abt-card__title">Individual artists and cultural practitioners</h3>
                            <p class="abt-card__text">Artists, performers and practitioners working across diverse artistic and cultural disciplines, including traditional, folk, classical, contemporary and interdisciplinary practices.</p>
                        </div>
                    </article>
                </div>
                <div class="col-12 col-md-6">
                    <article class="abt-card abt-eligible">
                        <div class="abt-eligible__symbol" aria-hidden="true">02</div>
                        <div>
                            <h3 class="abt-card__title">Cultural organisations and institutions</h3>
                            <p class="abt-card__text">Eligible cultural organisations, societies, trusts, non-governmental organisations, cultural centres, academies, arts collectives, ensembles, crafts cooperatives and artisan groups, in accordance with applicable national requirements.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="abt-section" aria-labelledby="registry-process-heading">
            <div class="abt-heading">
                <span class="abt-heading__number">03</span>
                <div>
                    <h2 class="abt-heading__title" id="registry-process-heading">How Registration Works</h2>
                    <p class="abt-heading__sub text-secondary mb-0">The draft pilot framework envisages a standardised registration and assessment process.</p>
                </div>
            </div>

            <div class="row g-4 abt-steps">
                <div class="col-12 col-md-4">
                    <article class="abt-card abt-step">
                        <span class="abt-step__number">1</span>
                        <h3 class="abt-card__title">Step 1 — Submit an application</h3>
                        <p class="abt-card__text">Eligible artists or cultural organisations complete the registration form and provide the required information and supporting documents.</p>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="abt-card abt-step">
                        <span class="abt-step__number">2</span>
                        <h3 class="abt-card__title">Step 2 — Assessment</h3>
                        <p class="abt-card__text">The relevant participating country's designated nodal agency reviews submissions in accordance with applicable verification procedures.</p>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="abt-card abt-step">
                        <span class="abt-step__number">3</span>
                        <h3 class="abt-card__title">Step 3 — Listing</h3>
                        <p class="abt-card__text">Successful applicants are listed in the Registry, with public visibility determined by authorised information for display.</p>
                    </article>
                </div>
            </div>

            <p class="abt-note mt-4">
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                Registration is voluntary. The availability of registration and listing will depend on the operational status of the pilot and participation arrangements established by the relevant countries.
            </p>
        </section>

        <section class="abt-important" aria-labelledby="registry-important-heading">
            <div class="abt-important__icon" aria-hidden="true">!</div>
            <div>
                <h2 class="abt-important__title" id="registry-important-heading">Important Information</h2>
                <p class="abt-important__lead">The BRICS Artist Registry is a voluntary directory and discovery platform. Registration:</p>
                <ul class="abt-important__list">
                    <li>Does not guarantee employment, funding or selection for festivals, programmes or other opportunities.</li>
                    <li>Does not constitute government accreditation or a professional licence.</li>
                    <li>Does not certify the quality of an artist's work.</li>
                    <li>Does not transfer ownership of copyright or artistic works.</li>
                    <li>Does not constitute an endorsement of an artist by all BRICS countries.</li>
                </ul>
            </div>
        </section>
    </div>
</main>
@endsection
