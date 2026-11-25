@extends('frontend.layouts.app')

@section('title', $eventdata->title . ' | Event Details')
@section('body-class', 'page-event-detail')

@section('content')
@php
    $eventDate = $eventdata->event_date;
    $eventEndDate = $eventdata->event_end_date;
    $location = collect([
        $eventdata->venue,
        $eventdata->city,
        optional($eventdata->region)->name,
        optional($eventdata->country)->name,
        $eventdata->address,
        $eventdata->pincode,
    ])->filter()->implode(', ');
@endphp

<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <a class="brx-page-header__crumb-link" href="{{ route('events') }}">Events</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Event Details</span>
        </nav>
        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>
        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Event Details</span>
        </h1>
        <small class="brx-page-header__description">{{ $eventdata->title }}</small>
    </div>
</section>

<main class="evt-main py-4 py-lg-5">
    <div class="container">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('events') }}" class="evt-back btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Events
            </a>
        </div>

        <article class="evt-detail p-3 p-lg-5 mb-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <span class="evt-badge badge text-bg-light mb-2">Cultural Event</span>
                    <h2 class="evt-detail__title h3 mb-2">{{ $eventdata->title }}</h2>
                    @if($eventdata->organised_by)
                        <p class="evt-detail__org text-secondary mb-0">Organised by {{ $eventdata->organised_by }}</p>
                    @endif
                </div>
                <div class="evt-when text-md-end">
                    <div class="evt-when__date fw-semibold"><i class="bi bi-calendar-event me-1"></i>{{ $eventDate?->format('d-m-Y') }}</div>
                    @if($eventdata->event_time)
                        <div class="text-secondary small">{{ $eventdata->event_time }}</div>
                    @endif
                    @if($eventEndDate)
                        <div class="text-secondary small">To {{ $eventEndDate->format('d-m-Y') }}
                            @if($eventdata->event_end_time) {{ $eventdata->event_end_time }} @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($location)
                <p class="evt-detail__place text-secondary mb-4"><i class="bi bi-geo-alt me-2"></i>{{ $location }}</p>
            @endif

            @if($eventdata->event_description)
                <section class="evt-about mb-4">
                    <h3 class="evt-about__title h5">About the Event</h3>
                    <div class="event-description">
                        {!! nl2br(e(strip_tags($eventdata->event_description))) !!}
                    </div>
                </section>
            @endif

            <div class="evt-contact row g-3 pt-4">
                @if($eventdata->mobile)
                    <div class="col-12 col-md-6"><span class="evt-contact__label text-secondary small d-block">Contact Number</span>{{ $eventdata->mobile }}</div>
                @endif
                @if($eventdata->other_mobile)
                    <div class="col-12 col-md-6"><span class="evt-contact__label text-secondary small d-block">Alternate Number</span>{{ $eventdata->other_mobile }}</div>
                @endif
                @if($eventdata->email)
                    <div class="col-12 col-md-6"><span class="evt-contact__label text-secondary small d-block">Email</span><a href="mailto:{{ $eventdata->email }}">{{ $eventdata->email }}</a></div>
                @endif
            </div>
        </article>

        <section class="evt-section mb-5">
            <div class="evt-section__head mb-3">
                <span class="evt-kicker">Event Media</span>
                <h2 class="evt-title h4 mb-0">Photo Gallery</h2>
            </div>
            @if($photoData->isNotEmpty())
                <div class="evt-gallery row g-3">
                    @foreach($photoData as $photo)
                        @php
                            $photoPath = optional($photo->asset)->url;
                            $photoUrl = $photoPath
                                ? (\Illuminate\Support\Str::startsWith($photoPath, ['http://', 'https://']) ? $photoPath : asset($photoPath))
                                : null;
                        @endphp
                        @if($photoUrl)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <a href="{{ $photoUrl }}" target="_blank" rel="noopener" class="evt-photo d-block h-100 text-decoration-none">
                                    <span class="evt-photo__frame">
                                        <img src="{{ $photoUrl }}" alt="{{ $photo->caption ?: 'Event photo' }}"
                                            class="evt-photo__img img-fluid w-100">
                                    </span>
                                    @if($photo->caption)
                                        <span class="evt-photo__caption d-block small text-dark">{{ $photo->caption }}</span>
                                    @endif
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="evt-empty-note text-secondary p-3 mb-0">No photos are available for this event.</p>
            @endif
        </section>

        <section class="evt-section">
            <div class="evt-section__head mb-3">
                <span class="evt-kicker">Watch & Explore</span>
                <h2 class="evt-title h4 mb-0">Video Gallery</h2>
            </div>
            @if($videoData->isNotEmpty())
                <div class="evt-gallery row g-3">
                    @foreach($videoData as $video)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="evt-video h-100">
                                @if($video->media_type === 'youtube' && $video->video_url)
                                    @php
                                        $youtubeId = $video->video_url;
                                        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{11})~', $video->video_url, $matches)) {
                                            $youtubeId = $matches[1];
                                        }
                                    @endphp
                                    <div class="evt-video__frame ratio ratio-16x9">
                                        <iframe src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}"
                                            title="{{ $video->caption ?: 'Event video' }}"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen loading="lazy"></iframe>
                                    </div>
                                @else
                                    @php
                                        $videoPath = optional($video->asset)->url;
                                        $videoUrl = $videoPath
                                            ? (\Illuminate\Support\Str::startsWith($videoPath, ['http://', 'https://']) ? $videoPath : asset($videoPath))
                                            : null;
                                    @endphp
                                    @if($videoUrl)
                                        <video class="evt-video__player w-100" controls preload="metadata">
                                            <source src="{{ $videoUrl }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <div class="evt-video__frame evt-video__frame--empty ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                            <span class="text-secondary">Video unavailable</span>
                                        </div>
                                    @endif
                                @endif
                                @if($video->caption)
                                    <p class="evt-video__caption small mb-0">{{ $video->caption }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="evt-empty-note text-secondary p-3 mb-0">No videos are available for this event.</p>
            @endif
        </section>
    </div>
</main>
@endsection
