@extends('frontend.layouts.app')

@section('title', ($content->title ?? (request()->route('slug') === 'privacy-policy' ? 'Privacy Policy' : Route::currentRouteName())))

@section('body-class', request()->route('slug') === 'privacy-policy' ? 'policy-page' : 'content-page')

@section('content')

<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">{{ $content->title ?? 'Information' }}</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">{{ $content->title ?? 'Information' }}</span>
        </h1>
        <small class="brx-page-header__description">Official publication of the BRICS Artist Registry pilot initiative.</small>
    </div>
</section>

<section class="evt-main cnt-main content-page-section py-4 py-lg-5">
    <div class="container">
        <div class="col-lg-10 mx-auto text-center mb-4">
            <h2 class="evt-title cnt-title custome-title custome-heading h2 mb-3">{{ $content->title ?? 'Information' }}</h2>
        </div>
        <div class="row justify-content-center g-4 cnt-layout">
            <div class="col-12 col-xl-12 cnt-col">
                @if ($content && $content->content)
                    <div class="content-page-card cnt-card">
                        <div class="content-page-body cnt-body">
                            {!! $content->content !!}
                        </div>
                    </div>
                @else
                    <div class="evt-empty content-page-empty cnt-empty text-center py-5 px-3">
                        <i class="bi bi-file-earmark-text display-4"></i>
                        <h2 class="h5 mt-3">Content Not Available</h2>
                        <p class="text-secondary">The requested content is currently unavailable or being updated.</p>
                        <a href="{{ url('/') }}" class="btn btn-outline-primary cnt-empty__btn mt-2">Return to Home</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
