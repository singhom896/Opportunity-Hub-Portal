@extends('frontend.layouts.app')

@section('title', 'Gallery')
@section('body-class', 'page-gallery')

@section('content')

<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">

        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Gallery</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Gallery</span>
        </h1>
        <small class="brx-page-header__description">Photo albums, event photographs and videos from BRICS cultural activities.</small>
    </div>
</section>

<main class="evt-main gal-main py-4 py-lg-5">
    <div class="container">

        {{-- Page Heading --}}
        <div class="gal-heading mb-4">
            <!--span class="evt-kicker">Moments &amp; Memories</span-->
            <h2 class="evt-title h3 mb-2">Photo &amp; Video Gallery</h2>
            <p class="text-secondary mb-0">
                Explore photos and videos of BRICS cultural activities and events.
            </p>
        </div>

        {{-- Main Categories --}}
        <ul class="nav nav-tabs evt-tabs gal-main-tabs mb-4" id="galleryMainTabs" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link active"
                        id="photos-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#photos-panel"
                        type="button"
                        role="tab">
                    <i class="bi bi-images me-2"></i>Photos
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link"
                        id="videos-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#videos-panel"
                        type="button"
                        role="tab">
                    <i class="bi bi-play-btn me-2"></i>Videos
                </button>
            </li>

        </ul>

        <div class="tab-content">

            {{-- PHOTOS MAIN CATEGORY --}}
            <div class="tab-pane fade show active"
                 id="photos-panel"
                 role="tabpanel">

                {{-- Photo Sub Categories --}}
                <ul class="nav nav-pills gal-pills mb-4" role="tablist">

                    <li class="nav-item">
                        <button class="nav-link active"
                                data-bs-toggle="pill"
                                data-bs-target="#albums-panel"
                                type="button">
                            Albums ({{ $albums->count() }})
                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link"
                                data-bs-toggle="pill"
                                data-bs-target="#event-photos-panel"
                                type="button">
                            Event Photos ({{ $eventPhotoCount }})
                        </button>
                    </li>

                </ul>

                <div class="tab-content">

                    {{-- ALBUMS --}}
                    <div class="tab-pane fade show active"
                         id="albums-panel">

                        <div class="row g-4">

                            @forelse($albums as $album)

                                @php
                                    $albumUrl = optional($album->asset)->url;
                                    $albumImage = $albumUrl
                                        ? (\Illuminate\Support\Str::startsWith($albumUrl, ['http://', 'https://'])
                                            ? $albumUrl
                                            : asset($albumUrl))
                                        : asset('assets/frontend/v1/images/no-image.png');
                                @endphp

                                <div class="col-lg-3 col-md-6">
                                    <div class="gal-card">

                                        <div class="gal-card__media">
                                            <a href="javascript:void(0)"
                                               class="gallery-preview gal-card__link"
                                               data-type="image"
                                               data-src="{{ $albumImage }}"
                                               data-title="{{ $album->title }}"
                                               aria-label="View photo: {{ $album->title }}">

                                                <img src="{{ $albumImage }}"
                                                     alt="{{ $album->title }}"
                                                     loading="lazy">

                                                <span class="gal-card__badge" aria-hidden="true"><i class="bi bi-arrows-fullscreen"></i></span>
                                            </a>
                                        </div>

                                        <div class="gal-card__body">
                                            <h5 class="gal-card__title">{{ $album->title }}</h5>

                                            @if($album->date)
                                                <p class="gal-card__date">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ \Carbon\Carbon::parse($album->date)->format('d M Y') }}
                                                </p>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                            @empty

                                <div class="col-12">
                                    <div class="evt-empty gal-empty text-center py-5 px-3">
                                        <i class="bi bi-images"></i>
                                        No photo albums available.
                                    </div>
                                </div>

                            @endforelse

                        </div>
                    </div>

                    {{-- EVENT PHOTOS --}}
                    <div class="tab-pane fade"
                         id="event-photos-panel">

                        <div class="row g-4">

                            @forelse($eventPhotos as $event)

                                @foreach($event->activeMedia as $media)

                                    @php
                                        $mediaUrl = optional($media->asset)->url;

                                        $photoUrl = $mediaUrl
                                            ? (\Illuminate\Support\Str::startsWith($mediaUrl, ['http://', 'https://'])
                                                ? $mediaUrl
                                                : asset($mediaUrl))
                                            : asset('assets/frontend/v1/images/no-image.png');
                                    @endphp

                                    <div class="col-lg-3 col-md-6">
                                        <div class="gal-card">

                                            <div class="gal-card__media">
                                                <a href="javascript:void(0)"
                                                   class="gallery-preview gal-card__link"
                                                   data-type="image"
                                                   data-src="{{ $photoUrl }}"
                                                   data-title="{{ $media->caption ?: $event->title }}"
                                                   aria-label="View photo: {{ $media->caption ?: $event->title }}">

                                                    <img src="{{ $photoUrl }}"
                                                         alt="{{ $media->caption ?: $event->title }}"
                                                         loading="lazy">

                                                    <span class="gal-card__badge" aria-hidden="true"><i class="bi bi-arrows-fullscreen"></i></span>
                                                </a>
                                            </div>

                                            <div class="gal-card__body">
                                                <h5 class="gal-card__title">
                                                    {{ $media->caption ?: $event->title }}
                                                </h5>

                                                <p class="gal-card__date">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $event->event_date
                                                        ? \Carbon\Carbon::parse($event->event_date)->format('d M Y')
                                                        : '' }}
                                                </p>


											 <a href="{{ route('event-detail', ['id' => Crypt::encrypt($event->id)]) }}" class="  btn-sm "  rel="noopener noreferrer">View Details  <i class="bi bi-arrow-right ms-2"></i>  </a>
											 
											
											 
                                            </div>

                                        </div>
                                    </div>

                                @endforeach

                            @empty

                                <div class="col-12">
                                    <div class="evt-empty gal-empty text-center py-5 px-3">
                                        <i class="bi bi-camera"></i>
                                        No event photos available.
                                    </div>
                                </div>

                            @endforelse

                        </div>
                    </div>

                </div>
            </div>

            {{-- VIDEOS MAIN CATEGORY --}}
            <div class="tab-pane fade"
                 id="videos-panel"
                 role="tabpanel">

                {{-- Video Sub Categories --}}
                <ul class="nav nav-pills gal-pills mb-4" role="tablist">

                    <li class="nav-item">
                        <button class="nav-link active"
                                data-bs-toggle="pill"
                                data-bs-target="#uploaded-videos-panel"
                                type="button">
                            Videos ({{ $videos->count() }})
                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link"
                                data-bs-toggle="pill"
                                data-bs-target="#event-videos-panel"
                                type="button">
                            Event Videos ({{ $eventVideoCount }})
                        </button>
                    </li>

                </ul>

                <div class="tab-content">

                    {{-- VIDEOS --}}
                    <div class="tab-pane fade show active"
                         id="uploaded-videos-panel">

                        <div class="row g-4">
						
						@forelse($videos as $video)

							@php
								$videoSource = '';

								if ($video->video_type == 2 && $video->asset) {
									$videoUrl = $video->asset->url;

									$videoSource = \Illuminate\Support\Str::startsWith(
										$videoUrl, ['http://', 'https://']
									) ? $videoUrl : asset($videoUrl);

								} elseif ($video->video_type == 1 && $video->video_url) {
									$videoSource = $video->video_url;
								}
							@endphp

							<div class="col-lg-3 col-md-6">
								<div class="gal-card gal-card--video">

									<div class="gal-card__media">

										@if($video->video_type == 2 && $video->asset)

											<a href="javascript:void(0)"
											   class="gallery-preview gal-card__link"
											   data-type="video"
											   data-src="{{ $videoSource }}"
											   data-title="{{ $video->title }}">

												<video preload="metadata" muted playsinline>
													<source src="{{ $videoSource }}" type="video/mp4">
												</video>

												<span class="gallery-play-icon gal-play">
													<i class="bi bi-play-fill"></i>
												</span>
											</a>

										@elseif($video->video_type == 1 && $video->video_url)

											<a href="javascript:void(0)"
											   class="gallery-preview gal-card__link"
											   data-type="youtube"
											   data-src="{{ $videoSource }}"
											   data-title="{{ $video->title }}">

												<img src="https://img.youtube.com/vi/{{ $video->video_url }}/hqdefault.jpg"
													 alt="{{ $video->title }}"
													 loading="lazy">

												<span class="gallery-play-icon gal-play">
													<i class="bi bi-play-fill"></i>
												</span>
											</a>

										@else
											<div class="gal-empty gal-empty--inline">
												Video not available.
											</div>
										@endif

									</div>

									<div class="gal-card__body">
										<h5 class="gal-card__title">{{ $video->title }}</h5>

										@if($video->date)
											<p class="gal-card__date">
												<i class="bi bi-calendar3 me-1"></i>
												{{ \Carbon\Carbon::parse($video->date)->format('d M Y') }}
											</p>
										@endif
									</div>

								</div>
							</div>

						@empty

							<div class="col-12">
								<div class="evt-empty gal-empty text-center py-5 px-3">
									<i class="bi bi-play-btn"></i>
									No videos available.
								</div>
							</div>

						@endforelse

                        </div>
                    </div>

                    {{-- EVENT VIDEOS --}}
                    <div class="tab-pane fade"
                         id="event-videos-panel">

                        <div class="row g-4">

                            @forelse($eventVideos as $event)

                                @foreach($event->activeMedia as $media)
                                    @continue(!in_array($media->media_type, ['video', 'youtube'], true))

                                    @php
                                        $rawEventVideoUrl = trim((string) ($media->video_url ?: optional($media->asset)->url));
                                        $eventVideoSource = '';
                                        $youtubeVideoId = '';

                                        if ($media->media_type === 'youtube' && $rawEventVideoUrl !== '') {
                                            // Accept either a direct 11-character YouTube ID or a YouTube URL.
                                            if (preg_match('~^[A-Za-z0-9_-]{11}$~D', $rawEventVideoUrl)) {
                                                $youtubeVideoId = $rawEventVideoUrl;
                                            } else {
                                                $youtubeUrlParts = parse_url($rawEventVideoUrl);
                                                $youtubeHost = strtolower($youtubeUrlParts['host'] ?? '');
                                                $youtubePath = trim($youtubeUrlParts['path'] ?? '', '/');
                                                $youtubeQuery = [];
                                                parse_str($youtubeUrlParts['query'] ?? '', $youtubeQuery);

                                                if (str_contains($youtubeHost, 'youtu.be')) {
                                                    $youtubeVideoId = explode('/', $youtubePath)[0] ?? '';
                                                } elseif (
                                                    str_contains($youtubeHost, 'youtube.com') ||
                                                    str_contains($youtubeHost, 'youtube-nocookie.com')
                                                ) {
                                                    $youtubeVideoId = $youtubeQuery['v'] ?? '';

                                                    if ($youtubeVideoId === '') {
                                                        $youtubePathParts = explode('/', $youtubePath);
                                                        if (count($youtubePathParts) >= 2 &&
                                                            in_array($youtubePathParts[0], ['embed', 'shorts', 'live', 'v'], true)) {
                                                            $youtubeVideoId = $youtubePathParts[1];
                                                        }
                                                    }
                                                }
                                            }

                                            // Validate and pass only the video ID to the modal.
                                            if (!preg_match('~^[A-Za-z0-9_-]{11}$~D', $youtubeVideoId)) {
                                                $youtubeVideoId = '';
                                            }
                                            $eventVideoSource = $youtubeVideoId;
                                        } elseif ($media->media_type === 'video' && $rawEventVideoUrl !== '') {
                                            $eventVideoSource = \Illuminate\Support\Str::startsWith(
                                                $rawEventVideoUrl,
                                                ['http://', 'https://', '//']
                                            )
                                                ? $rawEventVideoUrl
                                                : asset($rawEventVideoUrl);
                                        }
                                    @endphp

                                    <div class="col-lg-3 col-md-6">
                                        <div class="gal-card gal-card--video">
                                            <div class="gal-card__media">
                                                @if($media->media_type === 'youtube' && $youtubeVideoId !== '')
                                                    <a href="javascript:void(0)"
                                                       class="gallery-preview gal-card__link"
                                                       data-type="youtube"
                                                       data-src="{{ $youtubeVideoId }}"
                                                       data-title="{{ $media->caption ?: $event->title }}"
                                                       aria-label="Play video: {{ $media->caption ?: $event->title }}">
                                                        <img src="https://img.youtube.com/vi/{{ $youtubeVideoId }}/hqdefault.jpg"
                                                             alt="{{ $media->caption ?: $event->title }}"
                                                             loading="lazy">
                                                        <span class="gallery-play-icon gal-play" aria-hidden="true">
                                                            <i class="bi bi-play-fill"></i>
                                                        </span>
                                                    </a>
                                                @elseif($media->media_type === 'video' && $eventVideoSource !== '')
                                                    <a href="javascript:void(0)"
                                                       class="gallery-preview gal-card__link"
                                                       data-type="video"
                                                       data-src="{{ $eventVideoSource }}"
                                                       data-title="{{ $media->caption ?: $event->title }}"
                                                       aria-label="Play video: {{ $media->caption ?: $event->title }}">
                                                        <video preload="metadata" muted playsinline>
                                                            <source src="{{ $eventVideoSource }}">
                                                        </video>
                                                        <span class="gallery-play-icon gal-play" aria-hidden="true">
                                                            <i class="bi bi-play-fill"></i>
                                                        </span>
                                                    </a>
                                                @else
                                                    <div class="gal-empty gal-empty--inline">
                                                        Video not available. Please check the YouTube URL or uploaded video asset.
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="gal-card__body">
                                                <h5 class="gal-card__title">
                                                    {{ $media->caption ?: $event->title }}
                                                </h5>
                                                <p class="gal-card__date">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $event->event_date
                                                        ? \Carbon\Carbon::parse($event->event_date)->format('d M Y')
                                                        : '' }}
                                                </p>
												
												 <a href="{{ route('event-detail', ['id' => Crypt::encrypt($event->id)]) }}" class="  btn-sm "  rel="noopener noreferrer">View Details  <i class="bi bi-arrow-right ms-2"></i>  </a>
											 
											 
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            @empty

                                <div class="col-12">
                                    <div class="evt-empty gal-empty text-center py-5 px-3">
                                        <i class="bi bi-camera-video"></i>
                                        No event videos available.
                                    </div>
                                </div>

                            @endforelse

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

{{-- Preview Modal / Lightbox --}}
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
                    <button type="button" class="gal-lightbox__nav gal-lightbox__nav--prev" id="galleryPrev" aria-label="Previous">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                    </button>

                    <div id="galleryPreviewContent" class="gal-lightbox__content"></div>

                    <button type="button" class="gal-lightbox__nav gal-lightbox__nav--next" id="galleryNext" aria-label="Next">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <div class="gal-lightbox__progress" id="galleryProgress" aria-hidden="true"><span></span></div>

                <div class="gal-lightbox__bar">
                    <button type="button" class="gal-lightbox__btn" id="galleryPlay" aria-label="Start slideshow" aria-pressed="false">
                        <svg class="gal-ico-play" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.5-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z"/></svg>
                        <svg class="gal-ico-pause" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                        <span class="gal-lightbox__btn-text">Play</span>
                    </button>
                    <!--span class="gal-lightbox__hint d-none d-lg-inline">&larr; &rarr; navigate &middot; Space play/pause &middot; Esc close</span-->
                </div>

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalElement   = document.getElementById('galleryPreviewModal');
    const previewContent = document.getElementById('galleryPreviewContent');
    const previewTitle   = document.getElementById('galleryPreviewTitle');
    const counterEl      = document.getElementById('galleryPreviewCounter');
    const prevBtn        = document.getElementById('galleryPrev');
    const nextBtn        = document.getElementById('galleryNext');
    const playBtn        = document.getElementById('galleryPlay');
    const playText       = playBtn ? playBtn.querySelector('.gal-lightbox__btn-text') : null;
    const progress       = document.getElementById('galleryProgress');
    const progressBar    = progress ? progress.firstElementChild : null;

    if (!modalElement || typeof bootstrap === 'undefined') {
        return;
    }

    const SLIDE_MS = 4000;
    const previewModal = new bootstrap.Modal(modalElement);

    let items = [];          // preview links of the currently selected category
    let index = 0;
    let playing = false;
    let timer = null;
    let touchX = null;

    function youtubeId(src) {
        if (!src) return '';

        const value = String(src).trim();

        // Event Videos now pass a validated YouTube ID directly.
        if (/^[A-Za-z0-9_-]{11}$/.test(value)) {
            return value;
        }

        try {
            const url = new URL(value, window.location.href);
            const host = url.hostname.toLowerCase();
            const pathParts = url.pathname.split('/').filter(Boolean);
            let id = '';

            if (host === 'youtu.be' || host.endsWith('.youtu.be')) {
                id = pathParts[0] || '';
            } else if (
                host.includes('youtube.com') ||
                host.includes('youtube-nocookie.com')
            ) {
                id = url.searchParams.get('v') || '';

                if (!id && pathParts.length >= 2 &&
                    ['embed', 'shorts', 'live', 'v'].includes(pathParts[0])) {
                    id = pathParts[1];
                }
            }

            return /^[A-Za-z0-9_-]{11}$/.test(id) ? id : '';
        } catch (error) {
            return '';
        }
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
            void progressBar.offsetWidth; // restart the CSS animation
            progressBar.classList.add('is-running');
        }
        timer = setTimeout(function () { step(1); }, SLIDE_MS);
    }

    function setPlaying(state) {
        playing = !!state;
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
        const item  = items[index];
        const type  = item.dataset.type;
        const src   = item.dataset.src;
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

            const videoId = youtubeId(src);

            if (/^[a-zA-Z0-9_-]{11}$/.test(videoId)) {
                const iframe = document.createElement('iframe');
                iframe.src = 'https://www.youtube-nocookie.com/embed/' + videoId + '?autoplay=1&rel=0';
                iframe.referrerPolicy = 'strict-origin-when-cross-origin';
                iframe.allow = 'autoplay; encrypted-media; picture-in-picture';
                iframe.allowFullscreen = true;
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

        // The slideshow only runs on photo categories.
        const isImage = type === 'image';
        playBtn.hidden = !(isImage && multiple);
        if (!isImage && playing) {
            setPlaying(false);
        } else if (playing) {
            scheduleNext();
        }

        // Warm the cache for the neighbouring photos.
        [1, -1].forEach(function (d) {
            const n = items[(index + d + items.length) % items.length];
            if (n && n.dataset.type === 'image') {
                new Image().src = n.dataset.src;
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

    function open(link) {
        const pane = link.closest('.tab-pane');
        items = Array.prototype.slice.call(
            (pane || document).querySelectorAll('.gallery-preview')
        );
        index = Math.max(0, items.indexOf(link));
        setPlaying(false);
        render();
        previewModal.show();
    }

    document.querySelectorAll('.gallery-preview').forEach(function (item) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            open(this);
        });
    });

    prevBtn.addEventListener('click', function () { step(-1); });
    nextBtn.addEventListener('click', function () { step(1); });
    playBtn.addEventListener('click', function () { setPlaying(!playing); });

    document.addEventListener('keydown', function (e) {
        if (!modalElement.classList.contains('show')) {
            return;
        }

        if (e.key === 'ArrowRight') {
            e.preventDefault();
            step(1);
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            step(-1);
        } else if (e.key === 'Home') {
            e.preventDefault();
            index = 0;
            render();
        } else if (e.key === 'End') {
            e.preventDefault();
            index = items.length - 1;
            render();
        } else if ((e.key === ' ' || e.key === 'Spacebar' || e.key.toLowerCase() === 'p') && !playBtn.hidden) {
            if (e.target.closest && e.target.closest('video, button, a, iframe')) {
                if (e.key !== 'p' && e.key !== 'P') {
                    return;
                }
            }
            e.preventDefault();
            setPlaying(!playing);
        }
    });

    // Touch swipe
    previewContent.addEventListener('touchstart', function (e) {
        touchX = e.changedTouches[0].clientX;
    }, { passive: true });

    previewContent.addEventListener('touchend', function (e) {
        if (touchX === null) {
            return;
        }
        const dx = e.changedTouches[0].clientX - touchX;
        touchX = null;
        if (Math.abs(dx) > 50 && previewContent.getAttribute('data-type') === 'image') {
            step(dx < 0 ? 1 : -1);
        }
    }, { passive: true });

    // Pause the slideshow while the tab is hidden
    document.addEventListener('visibilitychange', function () {
        if (document.hidden && playing) {
            setPlaying(false);
        }
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        setPlaying(false);
        previewContent.innerHTML = '';
        items = [];
    });

});
</script>
@endpush
