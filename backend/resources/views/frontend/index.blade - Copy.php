@extends('frontend.layouts.app')
@section('title', 'BRICS Artist Registry')


@section('content')

<style>


</style>


    <section class="artist-banner">
        <div class="carousel">

            @foreach ($activeBanners as $index => $banner)
                <div class="carousel-slide {{ $index === 0 ? 'active' : '' }}">

                    <img src="{{ asset($banner->asset->url) }}" alt="Banner {{ $index + 1 }}">

                    <div class="carousel-content">              
                            <!--span class="banner-label">
                                BRICS INDIA 2026
                            </span-->
                            <h1>
                                BRICS Artist Registry
                            </h1>
                            <!--p>
                                    A shared digital space for artists, opportunities and cultural exchange.
                                </p-->

                            <div class="banner-buttons">

                                <a href="{{ route('artist.registration') }}" class="banner-btn secondary-btn">
                                    Artist Directory
                                </a>

                            </div>

                    </div>
                </div>
            @endforeach

            <button class="carousel-arrow carousel-prev" onclick="changeSlide(-1)" aria-label="Previous Slide">
                &#10094;
            </button>

            <button class="carousel-arrow carousel-next" onclick="changeSlide(1)" aria-label="Next Slide">
                &#10095;
            </button>

            <div class="carousel-dots">
                @foreach ($activeBanners as $index => $banner)
                    <button class="carousel-dot {{ $index === 0 ? 'active' : '' }}"
                        onclick="currentSlide({{ $index + 1 }})">
                    </button>
                @endforeach
            </div>

        </div>
    </section>
    <div class="announcement-bar">
        <div class="announcement-label-wrap">
            <div class="announcement-label">
                ANNOUNCEMENTS
            </div>
        </div>
        <div class="announcement-track">
            <div class="announcement-content">
                <span>
                    BRICS Artist Registry registration is now open for artists
                    from participating BRICS countries.
                </span>
                <span class="announcement-separator">◆</span>
                <span>
                    Explore artists, art forms and cultural practitioners from
                    across the BRICS countries.
                </span>
                <span class="announcement-separator">◆</span>
                <span>
                    Countries can discover and engage artists for cultural
                    performances and events.
                </span>
            </div>
        </div>
    </div>




<!-- About BRICS Artist Registry -->
<section class="about-registry py-4" id="about-registry">
    <div class="container py-lg-3">
        <div class="about-registry-card">
            <div class="row g-0 align-items-stretch">

                <!-- Left: Official Declaration -->
                <div class="col-lg-5">
                    <div class="about-declaration h-100">
                        <!--span class="about-eyebrow">
                            <span class="about-eyebrow-dot"></span>
                            OFFICIAL DECLARATION
                        </span-->

                        <div class="declaration-quote-mark">“</div>

                        <blockquote class="declaration-text mb-4">
                            We take notice of Indian Chairship’s initiative
                            to create and develop an Artist Registry on pilot
                            basis for voluntarily participating members.
                        </blockquote>

                        <div class="declaration-divider"></div>

                        <div class="declaration-source">
                            <!--span class="source-label">SOURCE</span-->
                            <p class="mb-0">
                                Declaration of the XI BRICS Culture Ministers’
                                Meeting
                            </p>
                            <small>Bhopal, India · 8 August 2026</small>
                        </div>
                    </div>
                </div>

                <!-- Right: About the Portal -->
                <div class="col-lg-7">
                    <div class="about-introduction h-100">
                     
                        <h2 class="about-heading">
                            Brics Artist Registry
                            <span>Pilot Initiative</span>
                        </h2>

                        <p class="about-description">
                            The BRICS Artist Registry is a voluntary pilot
                            initiative to connect artists, performers,
                            craftspeople and cultural organisations across
                            participating BRICS countries.
                        </p>

                        <p class="about-description mb-4">
                            Through a searchable digital directory, it aims
                            to enhance visibility, foster professional
                            connections, and promote cultural exchange and
                            collaboration among creative communities.
                        </p>

			
                        <div class="about-actions mt-2">
							
                            <a href="{{ url('about-us') }}"
                               class="about-read-more">
                                Read More
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>

                          
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- End About BRICS Artist Registry -->





    <section class="countries-dark" id="countries">
        <div class="wrap">
            @foreach ([['title' => 'BRICS Member Countries', 'countries' => $memberCountries], ['title' => 'BRICS Partner Countries', 'countries' => $partnerCountries]] as $group)
                <div class="country-block">
                    <div class="country-block-title">
                        <h2>{{ $group['title'] }}</h2>
                    </div>

                    <div class="country-box">
						<div class="text-center mb-3">
							<small class="country-instruction text-white">
								Click on a country flag to view artists.
							</small>
						</div>
						
                        <div class="country-list">
                            
							@forelse($group['countries'] as $country)
								<div class="country">
									<a href="{{ route('artist.list.country', ['id' => encrypt($country->id)]) }}"
									   class="{{ (int) $country->status === 0 ? 'country-not-joined' : '' }}"
									   @if((int) $country->status === 0)
										   data-country-name="{{ $country->name }}"
										   data-country-flag="{{ $country->flag_image
											   ? (\Illuminate\Support\Str::startsWith($country->flag_image, ['http://', 'https://'])
												   ? $country->flag_image
												   : asset(str_contains($country->flag_image, '/')
													   ? $country->flag_image
													   : 'assets/frontend/v1/images/flags/' . $country->flag_image))
											   : '' }}"
										   data-bs-toggle="modal"
										   data-bs-target="#countryStatusModal"
										   role="button"
									   @endif>

										@if ($country->flag_image)
											<img src="{{ \Illuminate\Support\Str::startsWith($country->flag_image, ['http://', 'https://'])
												? $country->flag_image
												: asset(str_contains($country->flag_image, '/')
													? $country->flag_image
													: 'assets/frontend/v1/images/flags/' . $country->flag_image) }}"
												alt="{{ $country->name }} Flag"
												loading="lazy">
										@endif

										<span>{{ $country->name }}</span>
									</a>
								</div>
							@empty
								<p>No countries available.</p>
							@endforelse

                        </div>
                    </div>
                </div>
            @endforeach

            <!--div class="view-all-container">
                <a href="{{ route('artist.list') }}" class="view-all-btn">▦ View All Artists</a>
            </div-->
        </div>
    </section>

    <div class="artistic-content-bg">
        <section class="features" id="forms">
            <div class="wrap feature-container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-5 g-3">
                    <div class="col">
                        <article class="card feature-card h-100" id="register">
                            <img src="{{ asset('assets/frontend/v1/images/icons/artist-registration.svg') }}"
                                alt="">
                            <h3>Artist Registration</h3>
                            <p>Join the BRICS creative network.</p>
                            <a href="{{ route('artist.registration') }}">Register now →</a>
                        </article>
                    </div>
                    <div class="col">
                        <article class="card feature-card h-100">
                            <img src="{{ asset('assets/frontend/v1/images/icons/artist-directory.svg') }}" alt="">
                            <h3>Artist Directory</h3>
                            <p>Search &amp; discover artists from 21 countries.</p>
                            <a href="{{ route('artist.list') }}">Explore →</a>
                        </article>
                    </div>
                    <div class="col">
                        <article class="card feature-card h-100">
                            <img src="{{ asset('assets/frontend/v1/images/icons/art-forms.svg') }}" alt="">
                            <h3>Art Forms</h3>
                            <p>Explore diverse art forms &amp; traditions.</p>
                            <a href="{{ route('artforms') }}">Discover →</a>
                        </article>
                    </div>
                    <div class="col">
                        <article class="card feature-card h-100">
                            <img src="{{ asset('assets/frontend/v1/images/icons/country-profiles.svg') }}" alt="">
                            <h3>Country Profiles</h3>
                            <p>Explore rich cultural heritage of BRICS.</p>
                            <a href="{{ url('countries') }}">Explore →</a>

                        </article>
                    </div>
                    <div class="col">
                        <article class="card feature-card h-100" id="media">
                            <img src="{{ asset('assets/frontend/v1/images/icons/media-gallery.svg') }}" alt="">
                            <h3>Media Gallery</h3>
                            <p>Photos, videos &amp; publications.</p>
                            <a href="https://www.brics2026.gov.in/events/brics-cultural-festival-held-on-6-7-august-2026"
                                target="_blank">Explore →</a>

                        </article>
                    </div>
                </div>
            </div>
        </section>
        <section class="wrap grid">
            <article class="panel">
                <div class="head">
                    <h2>What's New</h2>
                    <a href="#">View All</a>
                </div>
                <div class="news">
                    <!-- Featured News -->
                    <div class="featured-news">
                        <div class="event-photo">
                            <img src="{{ asset('assets/frontend/v1/images/meeting.png') }}" alt="Creative meeting">
                        </div>
                        <h3>BRICS Cultural Ministers' Meeting</h3>
                        <p>
                            Connecting cultural leaders and creative communities
                            under India's BRICS Chairship 2026.
                        </p>
                        <a href="#">Read More →</a>
                    </div>
                    <!-- News List -->
                    <div class="news-list">
                        <div class="news-item">
                            <span class="news-date">▣ &nbsp; AUG 07, 2026</span>
                            <b>BRICS Culture Ministers' Meeting</b>
                        </div>
                        <div class="news-item">
                            <span class="news-date">▣ &nbsp; AUG 07, 2026</span>
                            <b>BRICS Trade Ministers' Meeting</b>
                        </div>
                        <div class="news-item">
                            <span class="news-date">▣ &nbsp; AUG 07, 2026</span>
                            <b>BRICS Education Ministers' Meeting</b>
                        </div>
                    </div>
                </div>
            </article>
            <article class="panel" id="artists">
                <div class="head">
                    <h2>Featured Artists</h2><a>View All</a>
                </div>
                <div class="artistgrid">
                    <div>
                        <div class="portrait a"></div><b>Amina El Said</b><small>Egypt · Visual Arts</small>
                    </div>
                    <div>
                        <div class="portrait b"></div><b>Zhang Wei</b><small>China · Calligraphy</small>
                    </div>
                    <div>
                        <div class="portrait c"></div><b>Thandiwe Nkosi</b><small>South Africa · Music</small>
                    </div>
                    <div>
                        <div class="portrait d"></div><b>Rajesh Verma</b><small>India · Folk Dance</small>
                    </div>
                </div>
            </article>
        </section>


        <section class="wrap events" id="opportunities">
            <div class="head">
                <h2>Upcoming Events</h2>
                <a href="{{ url('calendar') }}">View Calendar →</a>
            </div>

            <div class="eventgrid">
                @forelse($upcomingEvents as $event)
                    <div class="event e{{ $loop->iteration }}">
                        @if ($event->media->first()?->asset?->url)
                            <img src="{{ asset($event->media->first()->asset->url) }}" alt="{{ $event->title }}">
                        @else
                            <img src="{{ asset('assets/frontend/v1/images/upcoming' . $loop->iteration . '.png') }}"
                                alt="{{ $event->title }}">
                        @endif

                        <b>{{ strtoupper($event->event_date->format('M')) }}<br>{{ $event->event_date->format('d') }}</b>

                        <span>
                            {{ $event->title }}
                            <small>{{ $event->country_name ?? 'Country not specified' }}</small>
                        </span>
                    </div>
                @empty
                    <div class="no-events">No upcoming events available.</div>
                @endforelse
            </div>
        </section>


<div class="modal fade" id="countryStatusModal" tabindex="-1"
     aria-labelledby="countryStatusModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

            <!-- Header -->
            <!--div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="country-status-icon rounded-circle d-flex align-items-center justify-content-center overflow-hidden">
					<img id="countryStatusFlag"
						 src=""
						 alt="Country Flag"
						 class="country-status-flag">
				</div>

                    <div>
                        <h5 class="modal-title fw-bold mb-1"  id="countryStatusModalLabel"> Country Update   </h5>
                        <small class="text-muted">BRICS Artist Registry</small>
                    </div>
                </div>

                <button type="button" class="btn-close align-self-start"
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div-->
			
			<div class="modal-header border-0 justify-content-center pb-0 px-4 pt-4">
					<img id="countryStatusFlag"
						 src=""
						 alt="Country Flag"
						 class="country-status-flag">
				</div>

            <!-- Body -->
            <div class="modal-body text-center px-4 py-4">
                <div class="country-status-divider mx-auto mb-3"></div>

                <p id="countryStatusMessage"
                   class="country-status-message mb-0">
                </p>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                <button type="button"
                        class="btn btn-primary rounded-pill px-5 py-2 fw-semibold"
                        data-bs-dismiss="modal">
                    Close <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>

        </div>
    </div>
</div>




    @endsection
    @push('scripts')
        <script>
            let slideIndex = 1;
            let carouselTimer;
            /* Show selected slide */
            function showSlide(n) {
                let slides =
                    document.getElementsByClassName(
                        "carousel-slide"
                    );
                let dots =
                    document.getElementsByClassName(
                        "carousel-dot"
                    );
                if (n > slides.length) {
                    slideIndex = 1;
                }
                if (n < 1) {
                    slideIndex = slides.length;
                }
                /* Hide all slides */
                for (let i = 0; i < slides.length; i++) {
                    slides[i].classList.remove("active");
                }
                /* Remove active dots */
                for (let i = 0; i < dots.length; i++) {
                    dots[i].classList.remove("active");
                }
                /* Show current slide */
                slides[slideIndex - 1]
                    .classList.add("active");
                dots[slideIndex - 1]
                    .classList.add("active");
            }
            /* Next / Previous */
            function changeSlide(n) {
                slideIndex += n;
                showSlide(slideIndex);
                restartCarousel();
            }
            /* Direct slide */
            function currentSlide(n) {
                slideIndex = n;
                showSlide(slideIndex);
                restartCarousel();
            }
            /* Automatic carousel */
            function startCarousel() {
                carouselTimer = setInterval(
                    function() {
                        slideIndex++;
                        showSlide(slideIndex);
                    },
                    6000
                );
            }
            /* Restart timer after manual navigation */
            function restartCarousel() {
                clearInterval(carouselTimer);
                startCarousel();
            }
            /* Initialize */
            document.addEventListener(
                "DOMContentLoaded",
                function() {
                    showSlide(slideIndex);
                    startCarousel();
                }
            );
			
			
			
        </script>
		
		
		<script>
document.addEventListener('DOMContentLoaded', function () {
    const countryModal = document.getElementById('countryStatusModal');

    countryModal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const countryName = trigger.getAttribute('data-country-name');
		
		const countryFlag = trigger.getAttribute('data-country-flag');
		const flagImage = document.getElementById('countryStatusFlag');

		flagImage.src = countryFlag || '';
		flagImage.style.display = countryFlag ? 'block' : 'none';

        document.getElementById('countryStatusMessage').textContent =
            countryName + ' has not joined the BRICS Artist Registry so far.';
    });
});
</script>
    @endpush
