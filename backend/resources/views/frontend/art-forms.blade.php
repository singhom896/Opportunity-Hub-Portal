@extends('frontend.layouts.app')

@section('title', 'Art Forms & Cultural Disciplines')
@section('body-class', 'custom-page artforms-page')

@section('content')

<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">

        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Art Forms</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Art Forms </span>
        </h1>
        <small class="brx-page-header__description">Explore the broad categories of arts and culture covered by the BRICS Artist Registry. Categories are indicative and represent diverse traditional and contemporary expressions across BRICS nations.</small> 
    </div>
</section>


{{-- Main Content --}}
<main class="evt-main arf-main py-4 py-lg-5">
    <div class="container">

        {{-- Introduction --}}
        <div class="col-lg-10 mx-auto text-center mb-4">
            <!--span class="section-kicker d-inline-block text-uppercase fw-bold text-terracotta mb-2">CULTURAL HERITAGE TRADITIONS</span-->
            <h2 class="evt-title arf-heading h2 mb-3">Art Forms Covered</h2>
            <!--p class="text-secondary lead fs-6 mb-0">
                The Registry covers, but is not limited to, the following categories of arts and culture.
                The examples below illustrate the types of artists, performers, and cultural practitioners represented across participating BRICS nations.
            </p-->
        </div>

        {{-- Country Search --}}
        <div class="row mb-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <form action="{{ route('artforms') }}" method="POST" class="arf-search">
                    @csrf

                    <label for="country_id" class="form-label fw-semibold">
                        Select BRICS Country
                    </label>

                    <div class="input-group">
                        <select name="country_id" id="country_id" class="form-select">
                            <option value="">All </option>

                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ (string) $selectedCountryId === (string) $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-search me-1"></i> Search
                        </button>

                        <a href="{{ route('artforms') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Art Forms Grid --}}
        <section class="row g-4 arf-grid" aria-label="Art forms covered">
            @forelse ($artForms as $form)
                <div class="col-12 col-lg-4">
                    <article class="arf-tile h-100 d-flex flex-column">
                        <div class="arf-tile__head d-flex align-items-center gap-3 mb-3">
                            <div class="arf-tile__initial flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                 aria-hidden="true">
                                {{ str($form->name)->substr(0, 1) }}
                            </div>

                            <h3 class="arf-tile__title h5 mb-0">{{ $form->name }}</h3>
                        </div>

                        <div class="arf-tile__body d-flex flex-column flex-grow-1">
                            <p class="arf-tile__text text-secondary mb-4 flex-grow-1">
                                {{ $form->description ?? 'Explore this art form and its cultural traditions.' }}
                            </p>

                            @if ($form->examples->isNotEmpty())
                                <div class="arf-tile__label small text-uppercase fw-semibold mb-2">
                                    Specialisations
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($form->examples as $example)
                                        <span class="badge rounded-pill arf-tile__chip px-3 py-2 fw-normal">
                                            {{ $example }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="evt-empty text-center py-5 px-3">
                        <p class="text-secondary mb-0">No art forms available.</p>
                    </div>
                </div>
            @endforelse
        </section>

        {{-- Information Note --}}
        <aside class="evt-info arf-note mt-5 p-4 d-flex gap-3" role="note">
            <i class="bi bi-info-circle-fill fs-4 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <h4 class="h6 fw-bold mb-1">Indicative, Not Exhaustive Framework</h4>
                <p class="mb-0 text-secondary">
                    The list of art forms is indicative and dynamic. Participating BRICS countries may propose additional disciplines, traditional folklore, and indigenous crafts during the operational phase.
                </p>
            </div>
        </aside>

        {{-- Artist Directory Button --}}
        <div class="text-center mt-4">
            <a href="{{ route('artist.list') }}" class="arf-cta">Explore Artist Directory <i class="bi bi-arrow-right ms-2"></i></a>
        </div>

    </div>
</main>
@endsection
