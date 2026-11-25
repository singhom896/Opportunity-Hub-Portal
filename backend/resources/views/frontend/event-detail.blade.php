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
                        <p class="evt-detail__org text-secondary mb-0">
                            Organised by {{ $eventdata->organised_by }}
                        </p>
                    @endif
                </div>

                <div class="evt-when text-md-end">
                    <div class="evt-when__date fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i>{{ $eventDate?->format('d-m-Y') }}
                    </div>

                    @if($eventdata->event_time)
                        <div class="text-secondary small">{{ $eventdata->event_time }}</div>
                    @endif

                    @if($eventEndDate)
                        <div class="text-secondary small">
                            To {{ $eventEndDate->format('d-m-Y') }}
                            @if($eventdata->event_end_time)
                                {{ $eventdata->event_end_time }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($location)
                <p class="evt-detail__place text-secondary mb-4">
                    <i class="bi bi-geo-alt me-2"></i>{{ $location }}
                </p>
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
                    <div class="col-12 col-md-6">
                        <span class="evt-contact__label text-secondary small d-block">Contact Number</span>
                        {{ $eventdata->mobile }}
                    </div>
                @endif

                @if($eventdata->other_mobile)
                    <div class="col-12 col-md-6">
                        <span class="evt-contact__label text-secondary small d-block">Alternate Number</span>
                        {{ $eventdata->other_mobile }}
                    </div>
                @endif

                @if($eventdata->email)
                    <div class="col-12 col-md-6">
                        <span class="evt-contact__label text-secondary small d-block">Email</span>
                        <a href="mailto:{{ $eventdata->email }}">{{ $eventdata->email }}</a>
                    </div>
                @endif
            </div>
        </article>

        {{-- EVENT PHOTO GALLERY --}}
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
                                ? (\Illuminate\Support\Str::startsWith($photoPath, ['http://', 'https://'])
                                    ? $photoPath
                                    : asset($photoPath))
                                : null;
                        @endphp

                        @if($photoUrl)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <a href="javascript:void(0)"
                                   class="gallery-preview evt-photo d-block h-100 text-decoration-none"
                                   data-type="image"
                                   data-src="{{ $photoUrl }}"
                                   data-title="{{ $photo->caption ?: 'Event photo' }}"
                                   aria-label="Preview photo: {{ $photo->caption ?: 'Event photo' }}">

                                    <span class="evt-photo__frame">
                                        <img src="{{ $photoUrl }}"
                                             alt="{{ $photo->caption ?: 'Event photo' }}"
                                             class="evt-photo__img img-fluid w-100"
                                             loading="lazy">
                                    </span>

                                    @if($photo->caption)
                                        <span class="evt-photo__caption d-block small text-dark">
                                            {{ $photo->caption }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="evt-empty-note text-secondary p-3 mb-0">
                    No photos are available for this event.
                </p>
            @endif
        </section>

        {{-- EVENT VIDEO GALLERY --}}
        <section class="evt-section">
            <div class="evt-section__head mb-3">
                <span class="evt-kicker">Watch &amp; Explore</span>
                <h2 class="evt-title h4 mb-0">Video Gallery</h2>
            </div>

            @if($videoData->isNotEmpty())
                <div class="evt-gallery row g-3">
                    @foreach($videoData as $video)
                        @php
                            $videoPath = optional($video->asset)->url;
                            $rawVideoUrl = trim((string) ($video->video_url ?? ''));
                            $youtubeId = '';

                            // YouTube URL/ID is stored in event_media.video_url.
                            if ($video->media_type === 'youtube' && $rawVideoUrl !== '') {
                                if (preg_match(
                                    '~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:[^#]*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~i',
                                    $rawVideoUrl,
                                    $youtubeMatch
                                )) {
                                    $youtubeId = $youtubeMatch[1];
                                } elseif (preg_match('~^[A-Za-z0-9_-]{11}$~', $rawVideoUrl)) {
                                    $youtubeId = $rawVideoUrl;
                                }

                                $videoSource = $rawVideoUrl;
                            } elseif ($video->media_type === 'video') {
                                $videoSource = $videoPath
                                    ? (\Illuminate\Support\Str::startsWith($videoPath, ['http://', 'https://'])
                                        ? $videoPath
                                        : asset($videoPath))
                                    : '';
                            } else {
                                $videoSource = '';
                            }
                        @endphp

                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="evt-video h-100">
                                @if($video->media_type === 'youtube' && $youtubeId)
                                    <a href="javascript:void(0)"
                                       class="gallery-preview gal-card__link"
                                       data-type="youtube"
                                       data-src="{{ $videoSource }}"
                                       data-title="{{ $video->caption ?: 'Event video' }}"
                                       aria-label="Play video: {{ $video->caption ?: 'Event video' }}">

                                        <img src="https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg"
                                             alt="{{ $video->caption ?: 'Event video' }}"
                                             class="img-fluid w-100"
                                             loading="lazy">

                                        <span class="gallery-play-icon gal-play" aria-hidden="true">
                                            <i class="bi bi-play-fill"></i>
                                        </span>
                                    </a>

                                @elseif($video->media_type === 'video' && $videoSource)
                                    <a href="javascript:void(0)"
                                       class="gallery-preview gal-card__link"
                                       data-type="video"
                                       data-src="{{ $videoSource }}"
                                       data-title="{{ $video->caption ?: 'Event video' }}"
                                       aria-label="Play video: {{ $video->caption ?: 'Event video' }}">

                                        <video preload="metadata" muted playsinline class="w-100">
                                            <source src="{{ $videoSource }}">
                                        </video>

                                        <span class="gallery-play-icon gal-play" aria-hidden="true">
                                            <i class="bi bi-play-fill"></i>
                                        </span>
                                    </a>

                                @else
                                    <div class="evt-video__frame evt-video__frame--empty ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center">
                                        <span class="text-secondary">
                                            {{ $video->media_type === 'youtube' ? 'Invalid YouTube URL.' : 'Video unavailable.' }}
                                        </span>
                                    </div>
                                @endif

                                @if($video->caption)
                                    <p class="evt-video__caption small mb-0">{{ $video->caption }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="evt-empty-note text-secondary p-3 mb-0">
                    No videos are available for this event.
                </p>
            @endif
        </section>
    </div>
</main>

{{-- GALLERY LIGHTBOX / PREVIEW MODAL --}}
<div class="modal fade gal-lightbox"
     id="galleryPreviewModal"
     tabindex="-1"
     aria-labelledby="galleryPreviewTitle"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header gal-lightbox__head">
                <div class="gal-lightbox__heading">
                    <h5 class="modal-title" id="galleryPreviewTitle">Gallery Preview</h5>
                    <span class="gal-lightbox__counter" id="galleryPreviewCounter" aria-live="polite"></span>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body gal-lightbox__body">
                <div class="gal-lightbox__stage">
                    <button type="button"
                            class="gal-lightbox__nav gal-lightbox__nav--prev"
                            id="galleryPrev"
                            aria-label="Previous">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                             stroke="currentColor" stroke-width="2.4"
                             stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M15 5l-7 7 7 7"/>
                        </svg>
                    </button>

                    <div id="galleryPreviewContent" class="gal-lightbox__content"></div>

                    <button type="button"
                            class="gal-lightbox__nav gal-lightbox__nav--next"
                            id="galleryNext"
                            aria-label="Next">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                             stroke="currentColor" stroke-width="2.4"
                             stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <div class="gal-lightbox__progress" id="galleryProgress" aria-hidden="true">
                    <span></span>
                </div>

                <div class="gal-lightbox__bar">
                    <button type="button"
                            class="gal-lightbox__btn"
                            id="galleryPlay"
                            aria-label="Start slideshow"
                            aria-pressed="false">
                        <svg class="gal-ico-play" viewBox="0 0 24 24" width="18" height="18"
                             fill="currentColor" aria-hidden="true">
                            <path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.5-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z"/>
                        </svg>
                        <svg class="gal-ico-pause" viewBox="0 0 24 24" width="18" height="18"
                             fill="currentColor" aria-hidden="true">
                            <rect x="6" y="5" width="4" height="14" rx="1"/>
                            <rect x="14" y="5" width="4" height="14" rx="1"/>
                        </svg>
                        <span class="gal-lightbox__btn-text">Play</span>
                    </button>

                    <!--span class="gal-lightbox__hint d-none d-lg-inline">
                        &larr; &rarr; navigate &middot; Space play/pause &middot; Esc close
                    </span-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('galleryPreviewModal');
    const previewContent = document.getElementById('galleryPreviewContent');
    const previewTitle = document.getElementById('galleryPreviewTitle');
    const counterEl = document.getElementById('galleryPreviewCounter');
    const prevBtn = document.getElementById('galleryPrev');
    const nextBtn = document.getElementById('galleryNext');
    const playBtn = document.getElementById('galleryPlay');
    const playText = playBtn ? playBtn.querySelector('.gal-lightbox__btn-text') : null;
    const progress = document.getElementById('galleryProgress');
    const progressBar = progress ? progress.firstElementChild : null;

    if (!modalElement || typeof bootstrap === 'undefined') {
        return;
    }

    const SLIDE_MS = 4000;
    const previewModal = new bootstrap.Modal(modalElement);

    let items = [];
    let index = 0;
    let playing = false;
    let timer = null;
    let touchX = null;

    function getYoutubeId(src) {
        if (!src) return '';

        src = String(src).trim();

        // event_media.video_url may contain a full URL or a bare 11-character ID.
        if (/^[a-zA-Z0-9_-]{11}$/.test(src)) {
            return src;
        }

        try {
            const url = new URL(src);
            const host = url.hostname.toLowerCase().replace(/^www\./, '');

            if (host === 'youtu.be') {
                return (url.pathname.split('/').filter(Boolean)[0] || '').substring(0, 11);
            }

            if (
                host === 'youtube.com' ||
                host === 'm.youtube.com' ||
                host === 'youtube-nocookie.com'
            ) {
                const queryId = url.searchParams.get('v');
                if (queryId) return queryId.substring(0, 11);

                const parts = url.pathname.split('/').filter(Boolean);
                if (parts.length >= 2 && ['embed', 'shorts', 'live', 'v'].includes(parts[0])) {
                    return parts[1].substring(0, 11);
                }
            }
        } catch (error) {
            // Invalid URL.
        }

        return '';
    }

    function stopTimer() {
        clearTimeout(timer);
        timer = null;

        if (progressBar) {
            progressBar.classList.remove('is-running');
        }
    }

    function scheduleNext() {
        stopTimer();

        if (!playing) {
            return;
        }

        if (progressBar) {
            progressBar.style.animationDuration = SLIDE_MS + 'ms';
            void progressBar.offsetWidth;
            progressBar.classList.add('is-running');
        }

        timer = setTimeout(function () {
            step(1);
        }, SLIDE_MS);
    }

    function setPlaying(state) {
        playing = !!state;

        if (!playBtn) {
            return;
        }

        playBtn.classList.toggle('is-playing', playing);
        playBtn.setAttribute('aria-pressed', playing ? 'true' : 'false');
        playBtn.setAttribute('aria-label', playing ? 'Pause slideshow' : 'Start slideshow');

        if (playText) {
            playText.textContent = playing ? 'Pause' : 'Play';
        }

        if (playing) {
            scheduleNext();
        } else {
            stopTimer();
        }
    }

    function render() {
        if (!items.length || !items[index]) {
            return;
        }

        const item = items[index];
        const type = item.dataset.type;
        const src = item.dataset.src;
        const title = item.dataset.title || 'Gallery Preview';

        previewTitle.textContent = title;
        previewContent.innerHTML = '';
        previewContent.setAttribute('data-type', type);

        if (type === 'image') {
            const image = document.createElement('img');
            image.src = src;
            image.alt = title;
            previewContent.appendChild(image);

        } else if (type === 'video') {
            const video = document.createElement('video');
            video.src = src;
            video.controls = true;
            video.autoplay = true;
            video.playsInline = true;
            previewContent.appendChild(video);

        } else if (type === 'youtube') {
            const videoId = getYoutubeId(src);

            if (/^[a-zA-Z0-9_-]{11}$/.test(videoId)) {
                const iframe = document.createElement('iframe');
                iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';
                iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                iframe.setAttribute('allowfullscreen', '');
                iframe.referrerPolicy = 'strict-origin-when-cross-origin';
                iframe.title = title;
                previewContent.appendChild(iframe);
            } else {
                previewContent.textContent = 'Invalid YouTube video URL.';
            }
        }

        counterEl.textContent = (index + 1) + ' / ' + items.length;

        const multiple = items.length > 1;
        prevBtn.hidden = !multiple;
        nextBtn.hidden = !multiple;

        const isImage = type === 'image';
        playBtn.hidden = !(isImage && multiple);

        if (!isImage && playing) {
            setPlaying(false);
        } else if (playing) {
            scheduleNext();
        }

        // Preload adjacent photos for smoother navigation.
        [1, -1].forEach(function (direction) {
            const adjacent = items[(index + direction + items.length) % items.length];

            if (adjacent && adjacent.dataset.type === 'image') {
                new Image().src = adjacent.dataset.src;
            }
        });
    }

    function step(delta) {
        if (!items.length) {
            return;
        }

        index = (index + delta + items.length) % items.length;
        render();
    }

    function openPreview(link) {
        // Only navigate through preview items on this Event Detail page.
        items = Array.from(document.querySelectorAll('.gallery-preview'));
        index = Math.max(0, items.indexOf(link));

        setPlaying(false);
        render();
        previewModal.show();
    }

    document.querySelectorAll('.gallery-preview').forEach(function (item) {
        item.addEventListener('click', function (event) {
            event.preventDefault();
            openPreview(this);
        });
    });

    prevBtn.addEventListener('click', function () {
        step(-1);
    });

    nextBtn.addEventListener('click', function () {
        step(1);
    });

    playBtn.addEventListener('click', function () {
        setPlaying(!playing);
    });

    document.addEventListener('keydown', function (event) {
        if (!modalElement.classList.contains('show')) {
            return;
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            step(1);
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            step(-1);
        } else if (
            (event.key === ' ' || event.key === 'Spacebar' || event.key.toLowerCase() === 'p')
            && !playBtn.hidden
        ) {
            if (event.target.closest && event.target.closest('video, button, a, iframe')) {
                if (event.key !== 'p' && event.key !== 'P') {
                    return;
                }
            }

            event.preventDefault();
            setPlaying(!playing);
        }
    });

    // Touch swipe navigation for images.
    previewContent.addEventListener('touchstart', function (event) {
        touchX = event.changedTouches[0].clientX;
    }, { passive: true });

    previewContent.addEventListener('touchend', function (event) {
        if (touchX === null) {
            return;
        }

        const deltaX = event.changedTouches[0].clientX - touchX;
        touchX = null;

        if (Math.abs(deltaX) > 50 && previewContent.getAttribute('data-type') === 'image') {
            step(deltaX < 0 ? 1 : -1);
        }
    }, { passive: true });

    // Stop slideshow when the browser tab is hidden.
    document.addEventListener('visibilitychange', function () {
        if (document.hidden && playing) {
            setPlaying(false);
        }
    });

    // Stop video playback and clear modal content when closed.
    modalElement.addEventListener('hidden.bs.modal', function () {
        setPlaying(false);
        previewContent.innerHTML = '';
        items = [];
    });
});
</script>
@endpush
