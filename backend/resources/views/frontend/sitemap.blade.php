@extends('frontend.layouts.app')
@section('title', 'Sitemap')

@section('content')
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Sitemap</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Sitemap &amp; Directory</span>
        </h1>
        <small class="brx-page-header__description">Explore the structure and navigate across all sections of the BRICS Artist Registry.</small>
    </div>
</section>

<section class="py-4 py-lg-5">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <h2 class="h5 mb-3 text-primary border-bottom pb-2">Main Navigation</h2>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        @foreach (getMenu()->where('is_main', 1) as $menu)
                            <li>
                                <a href="{{ url($menu->url) }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                    <span class="text-warning">◆</span> {{ $menu->title }}
                                </a>
                                @if ($menu->childrenRecursive->isNotEmpty())
                                    <ul class="list-unstyled ms-4 mt-2 d-flex flex-column gap-1">
                                        @foreach ($menu->childrenRecursive as $child)
                                            <li>
                                                <a href="{{ url($child->url) }}" class="text-decoration-none text-secondary small d-inline-flex align-items-center gap-1">
                                                    <span>›</span> {{ $child->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <h2 class="h5 mb-3 text-primary border-bottom pb-2">Registry &amp; Artists</h2>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li>
                            <a href="{{ route('artist.list') }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                <span class="text-warning">◆</span> Artist Directory
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('countries') }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                <span class="text-warning">◆</span> Explore Countries
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('artforms') }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                <span class="text-warning">◆</span> Art Forms &amp; Traditions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('artist.registration') }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                <span class="text-warning">◆</span> Artist Registration
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                <span class="text-warning">◆</span> Artist Account Login
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <h2 class="h5 mb-3 text-primary border-bottom pb-2">Information &amp; Policies</h2>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        @foreach (getMenu()->where('is_footer', 1) as $menu)
                            <li>
                                <a href="{{ url($menu->url) }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                    <span class="text-warning">◆</span> {{ $menu->title }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ url('contact-us') }}" class="text-decoration-none fw-semibold text-dark d-inline-flex align-items-center gap-2">
                                <span class="text-warning">◆</span> Contact &amp; Country Nodal Officers
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
