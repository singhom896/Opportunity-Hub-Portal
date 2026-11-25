@extends('frontend.layouts.app')

@section('title', 'Events')
@section('body-class', 'page-events')

@section('content')
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Events</span>
        </nav>
        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>
        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Cultural Events</span>
        </h1>
        <small class="brx-page-header__description">
            Discover cultural programmes, exhibitions, festivals and artistic events across BRICS countries.
        </small>
    </div>
</section>

<main class="evt-main py-4 py-lg-5">
    <div class="container">
        <ul class="nav nav-tabs evt-tabs mb-4" id="eventTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $type === 'upcoming' ? 'active' : '' }}" id="upcoming-tab"
                    data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                    Upcoming Events <span class="badge text-bg-light evt-count ms-1">{{ $eventUpcomingData->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $type === 'past' ? 'active' : '' }}" id="past-tab"
                    data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
                    Past Events <span class="badge text-bg-light evt-count ms-1">{{ $eventPastData->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            @foreach (['upcoming' => $eventUpcomingData, 'past' => $eventPastData] as $tabType => $events)
                <div class="tab-pane fade {{ $type === $tabType ? 'show active' : '' }}"
                    id="{{ $tabType }}" role="tabpanel">
                    <div class="evt-heading d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <span class="evt-kicker">Discover & Participate</span>
                            <h2 class="evt-title h3 mb-0">{{ ucfirst($tabType) }} Events</h2>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('events.search') }}" class="evt-filter row g-3 mb-4">
                        @csrf
                        <input type="hidden" name="type" value="{{ $tabType }}">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="{{ $tabType }}-eventTitle">Event Name</label>
                            <input class="form-control" type="text" id="{{ $tabType }}-eventTitle"
                                name="eventTitle" value="{{ $type === $tabType ? request('eventTitle') : '' }}"
                                placeholder="Search event">
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label" for="{{ $tabType }}-country">Country</label>
                            <select class="form-select" id="{{ $tabType }}-country" name="country_id">
                                <option value="">All Countries</option>
                                @foreach ($countries as $countryId => $countryName)
                                    <option value="{{ $countryId }}" {{ $type === $tabType && (string)request('country_id') === (string)$countryId ? 'selected' : '' }}>
                                        {{ $countryName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label" for="{{ $tabType }}-region">Region</label>
                            <select class="form-select" id="{{ $tabType }}-region" name="region_id">
                                <option value="">All Regions</option>
                                @foreach ($regions as $regionId => $regionName)
                                    <option value="{{ $regionId }}" {{ $type === $tabType && (string)request('region_id') === (string)$regionId ? 'selected' : '' }}>
                                        {{ $regionName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label" for="{{ $tabType }}-city">City</label>
                            <input class="form-control" type="text" id="{{ $tabType }}-city" name="city"
                                value="{{ $type === $tabType ? request('city') : '' }}" placeholder="City">
                        </div>
                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label" for="{{ $tabType }}-date">Event Date</label>
                            <input class="form-control" type="date" id="{{ $tabType }}-date" name="eventDate"
                                value="{{ $type === $tabType ? request('eventDate') : '' }}">
                        </div>
                        <div class="col-12 col-lg-1 d-flex align-items-end gap-2">
                            <button class="btn btn-primary evt-search w-100" type="submit">Search</button>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('events') }}" class="evt-reset small text-decoration-none">Reset filters</a>
                        </div>
                    </form>

                    @forelse ($events as $event)
                        @php
                            $eventDate = $event->event_date;
                            $description = trim(preg_replace('/\s+/', ' ', strip_tags($event->event_description ?? '')));
                            $location = collect([$event->venue, $event->city, optional($event->region)->name, optional($event->country)->name])
                                ->filter()->implode(', ');
                        @endphp
                        <article class="evt-card evt-card--{{ $tabType }} mb-3">
                            <div class="row g-3 align-items-start">
                                <div class="col-auto">
                                    <div class="evt-date text-center">
                                        <div class="evt-date__day fs-3 fw-bold">{{ $eventDate?->format('d') }}</div>
                                        <div class="evt-date__month small text-uppercase">{{ $eventDate?->format('M Y') }}</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <div>
                                            <h3 class="evt-card__title h5 mb-1">{{ $event->title }}</h3>
                                            <div class="evt-card__when small text-secondary">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ $eventDate?->format('d-m-Y h:i A') }}
                                                @if($event->event_end_date)
                                                    <span class="mx-1">–</span>{{ $event->event_end_date->format('d-m-Y') }}
                                                @endif
                                            </div>
                                        </div>
                                        <a class="evt-card__cta btn btn-outline-primary btn-sm align-self-start"
                                            href="{{ route('event-detail', ['id' => Crypt::encrypt($event->id)]) }}">
                                            View Details <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                    @if($location)
                                        <p class="evt-card__place small text-secondary mt-3 mb-2">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $location }}
                                        </p>
                                    @endif
                                    @if($description)
                                        <p class="evt-card__text mb-2">{{ \Illuminate\Support\Str::limit($description, 420) }}</p>
                                    @endif
                                    <div class="evt-card__meta d-flex flex-wrap gap-3 small text-secondary mt-3">
                                        @if($event->organised_by)
                                            <span><i class="bi bi-building me-1"></i>{{ $event->organised_by }}</span>
                                        @endif
                                        @if($event->email)
                                            <span><i class="bi bi-envelope me-1"></i>{{ $event->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="evt-empty text-center py-5 px-3">
                            <i class="bi bi-calendar2-x fs-1 text-secondary"></i>
                            <h3 class="h5 mt-3">No {{ $tabType }} events found</h3>
                            <p class="text-secondary mb-0">Please try another search or check back later.</p>
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>

        <aside class="evt-info mt-4 p-4 d-flex gap-3">
            <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
            <div>
                <h3 class="h6 fw-bold mb-1">Information for Visitors</h3>
                <p class="text-secondary mb-0">Please contact the event organiser for the latest schedule, venue details and participation requirements.</p>
            </div>
        </aside>
    </div>
</main>
@endsection
