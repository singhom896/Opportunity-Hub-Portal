@extends('frontend.layouts.app')

@section('title', 'Artist Directory')
@section('body-class', 'page-artist-directory')

@section('content')
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">

        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Artist Directory</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>
		

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Artist Directory</span> 
        </h1>
		
        <small class="brx-page-header__description">Discover verified artists, performing troupes, and cultural practitioners from across the BRICS countries.</small> 
			
				@php
					$selectedCountry = $countries->firstWhere(
						'id',
						(int) ($filters['country'] ?? 0)
					);
				@endphp

				@if ($selectedCountry)
					<div class="directory-flag">
						<img
							src="{{ asset('assets/frontend/v1/images/flags/' . $selectedCountry->flag_image) }}"
							alt="{{ $selectedCountry->name }} Flag"
						>
                        <span class="d-block small text-center text-white-50 mt-1">{{ $selectedCountry->name }}</span>
					</div>
				@endif
	
		
    </div>
</section>

    <!--section class="directory-banner" aria-labelledby="directoryBannerTitle">
        <div class="wrap">
            <div class="breadcrumb mb-2">
                <a href="{{ url('/') }}">Home</a>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current" aria-current="page">Artist Directory</span>
            </div>

            <div class="directory-title">
                <div class="directory-title-content">
                    <span class="directory-label">BRICS ARTIST REGISTRY</span>
                    <h1 id="directoryBannerTitle">Artist Directory</h1>
                    <p>Discover verified artists, performing troupes, and cultural practitioners from across the BRICS countries.</p>
                </div>
				
				@php
					$selectedCountry = $countries->firstWhere(
						'id',
						(int) ($filters['country'] ?? 0)
					);
				@endphp

				@if ($selectedCountry)
					<div class="directory-flag">
						<img
							src="{{ asset('assets/frontend/v1/images/flags/' . $selectedCountry->flag_image) }}"
							alt="{{ $selectedCountry->name }} Flag"
						>
                        <span class="d-block small text-center text-white-50 mt-1">{{ $selectedCountry->name }}</span>
					</div>
				@endif
            </div>
        </div>
    </section-->
	
	
	
	


    <main class="directory-main py-4 py-lg-5">
        <div class="wrap">
            <section class="filter-panel mb-4">
                <div class="filter-heading d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="text-uppercase small fw-bold">FIND AN ARTIST</span>
                        <h2 class="h4 mb-0">Search &amp; Filter</h2>
                    </div>
                    <div class="artist-total-badge text-end">
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                            Total: {{ number_format($artists->total()) }} Artists
                        </span>
                    </div>
                </div>

                <form class="filters" method="post" action="{{ route('artist.list') }}">
					@csrf
					<meta name="csrf-token" content="{{ csrf_token() }}">
						
                    <div class="filter-field search-field">
                        <label for="artistSearch">Search Artist</label>
                        <div class="input-wrapper">
                            <span aria-hidden="true">⌕</span>
                            <input type="text" id="artistSearch" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Enter artist name or discipline...">
                        </div>
                    </div>

                    <div class="filter-field">
						<label for="countryFilter">Country</label>
						<select id="countryFilter" name="country">
							<option value="">All Countries</option>
							@foreach ($countries as $country)
								<option value="{{ $country->id }}"
									@selected((string) ($filters['country'] ?? '') === (string) $country->id)>
									{{ $country->name }}
								</option>
							@endforeach
						</select>
					</div>
					
					<div class="filter-field">
					    <label for="artFormFilter">Art Form</label>
    					<select id="artFormFilter" name="art_form">
    						<option value="">All Art Forms</option>
    						@foreach ($artForms as $artForm)
    							<option value="{{ $artForm }}"
    								@selected(($filters['art_form'] ?? '') === $artForm)>
    								{{ $artForm }}
    							</option>
    						@endforeach
    					</select>
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="apply-btn" id="applyFilters">Apply Filters</button>
						<a href="{{ route('artist.list') }}" class="btn btn-outline-secondary reset-btn" id="resetFilters">
							Reset
						</a>
                    </div>
                </form>
				
				@if ($artForms->isNotEmpty())
					<section class="exploreByCategory mt-4" id="exploreByCategory">
						<div class="bg-light border rounded-3 p-3 p-md-4">
							<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
								<div>
									<span class="text-uppercase text-primary fw-semibold small">
										Explore by Category
									</span>
								</div>

								<span class="badge bg-primary rounded-pill px-3 py-2" id="artFormCount">
									{{ $artForms->count() }} Categories
								</span>
							</div>

							<div class="d-flex flex-wrap gap-2" id="exploreArtForms">
								@foreach ($artForms as $artForm)
									<span class="badge bg-white text-dark border rounded-pill px-3 py-2 fw-normal artForm"
										  data-art-form="{{ $artForm }}"
										  role="button"
										  style="cursor:pointer;">
										{{ $artForm }}
									</span>
								@endforeach
							</div>
						</div>
					</section>
				@endif
            </section>

            <!--section class="results-header d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h4 mb-0">Registered Artists</h2>
                    <p class="text-muted small mb-0">Showing <strong>{{ $artists->firstItem() ?? 0 }}–{{ $artists->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($artists->total()) }}</strong> profiles</p>
                </div>
                <div class="export-buttons d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary export-btn" id="exportCSV">
                        <i class="bi bi-file-earmark-spreadsheet me-1" aria-hidden="true"></i> Export CSV
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary export-btn" id="exportPDF">
                        <i class="bi bi-printer me-1" aria-hidden="true"></i> Print / PDF
                    </button>
                </div>
            </section-->

            <section class="artist-table-wrapper card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="artist-table table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Artist Name</th>
                                <th>Country</th>
                                <th>Art Form</th>
                                <th>Specialisation</th>
                                <th>Experience</th>
                                <th class="text-end" style="width: 140px;">Profile</th>
                            </tr>
                        </thead>
                        <tbody id="artistTableBody">
                            @forelse ($artists as $artist)
                                @php
                                    $artistName = trim((string) ($artist->display_name  ?? $artist->name ?? 'Unknown Artist'));
                                    $initials = collect(explode(' ', $artistName))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                        ->implode('');
                                @endphp
                                <tr>
                                    <td class="text-muted small">{{ str_pad((string) (($artists->firstItem() ?? 1) + $loop->index), 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>																		
                                        <div class="artist-cell d-flex align-items-center gap-3">
                                            @if (!empty($artist->profile_photo_url))
                                                <img class="artist-avatar rounded-circle" src="{{ $artist->profile_photo_url }}" alt="{{ $artistName }}">
                                            @else
                                                <div class="artist-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold">{{ $initials ?: 'A' }}</div>
                                            @endif
                                            
                                            <div>										
                                                <strong class="d-block">{{ $artist->display_name }}</strong>
                                                <small class="text-muted text-start">
                                                    {{ match ((int) $artist->role_id) {
                                                        5 => 'Artist',
                                                        6 => 'Group Artist',
                                                        7 => 'Institute / Organization',
                                                        default => 'Artist',
                                                    } }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $artist->country ?? '—' }}</td>
                                    <td>{{ filled($artist->art_form ?? null) ? $artist->art_form : '—' }}</td>
                                    <td>{{ filled($artist->subgenre ?? null) ? $artist->subgenre : '—' }}</td>
                                    <td>{{ isset($artist->years_of_practice) && $artist->years_of_practice !== '' ? $artist->years_of_practice.' Years' : '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('artist.detail', ['id' => encrypt($artist->id)]) }}" class="view-profile btn btn-sm">
                                            View Profile →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-secondary">No public artist profiles match your search criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="d-flex justify-content-center mt-4">
               {{ $artists->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // =========================
    // Export CSV
    // =========================
    $('#exportCSV').on('click', function (e) {
        e.preventDefault();
        const table = document.querySelector('.artist-table');
        if (!table) {
            alert('Artist table not found.');
            return;
        }

        const rows = Array.from(table.querySelectorAll('tr'));
        if (!rows.length) {
            alert('No data available to export.');
            return;
        }

        const csv = rows.map(function (row) {
            return Array.from(row.querySelectorAll('th, td'))
                .map(function (cell) {
                    let text = cell.innerText.replace(/\s+/g, ' ').trim();
                    text = text.replace(/"/g, '""');
                    return '"' + text + '"';
                })
                .join(',');
        }).join('\r\n');

        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'brics-artist-directory.csv';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 1000);
    });

    // =========================
    // Export PDF / Print
    // =========================
    $('#exportPDF').on('click', function (e) {
        e.preventDefault();
        window.print();
    });

    // =========================
    // Country Filter Change
    // =========================
    $('#countryFilter').on('change', function () {
        const country = $(this).val();
        const artFormSelect = $('#artFormFilter');
        const exploreArtForms = $('#exploreArtForms');

        artFormSelect.html('<option value="">Loading...</option>');
        $('#exploreByCategory').hide();

        if (!country) {
            artFormSelect.html('<option value="">All Art Forms</option>');
            exploreArtForms.empty();
            $('#artFormCount').text('0 Categories');
            return;
        }

        $.ajax({
            url: "{{ route('artist.art-forms') }}",
            type: 'POST',
            data: {
                country: country,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function (artForms) {
                artFormSelect.empty().append(
                    $('<option>', {
                        value: '',
                        text: 'All Art Forms'
                    })
                );

                exploreArtForms.empty();

                $.each(artForms, function (index, artForm) {
                    const name = typeof artForm === 'object' ? artForm.name : artForm;
                    artFormSelect.append(
                        $('<option>', {
                            value: name,
                            text: name
                        })
                    );
                    exploreArtForms.append(
                        $('<span>', {
                            class: 'badge bg-white text-dark border rounded-pill px-3 py-2 fw-normal artForm',
                            text: name,
                            'data-art-form': name,
                            role: 'button'
                        }).css('cursor', 'pointer')
                    );
                });

                $('#artFormCount').text(artForms.length + ' Categories');
                $('#exploreByCategory').toggle(artForms.length > 0);
            },
            error: function (xhr) {
                console.error('Art Forms error:', xhr.status, xhr.responseText);
                artFormSelect.html('<option value="">Unable to load Art Forms</option>');
                exploreArtForms.empty();
                $('#artFormCount').text('0 Categories');
                $('#exploreByCategory').hide();
            }
        });
    });

    $(document).on('click', '.artForm', function () {
        let artForm = $(this).attr('data-art-form');
        $('#artFormFilter').val(artForm);
        $('.filters').trigger('submit');
    });
});
</script>
@endpush
