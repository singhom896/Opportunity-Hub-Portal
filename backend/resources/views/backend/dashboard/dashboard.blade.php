@extends('backend.layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="container-fluid dashboard-page">
    <header class="dashboard-heading">
        <div>
            <p class="dashboard-eyebrow mb-1">BRICS ARTIST REGISTRY</p>
            <h1 class="dashboard-title mb-1">Welcome back, {{ $agencyName }}</h1>
            <p class="dashboard-subtitle mb-0">
                @if ($isNodalAgency && $country)
                    {{ $country->name }} Nodal Agency · Country registrations and accounts at a glance.
                @else
                    Registrations, accounts and system modules at a glance.
                @endif
            </p>
        </div>
        <p class="dashboard-date mb-0"><i class="bi bi-calendar3 me-2" aria-hidden="true"></i>{{ now()->format('l, F j, Y') }}</p>
    </header>


@if ($isNodalAgency)
    <section class="dashboard-stats" aria-label="Registry overview">

        @php
            $stats = [
                [
                    'label' => 'Total Artist Registrations',
                    'value' => $artistRegistrationCount,
                    'icon' => 'bi-files',
                    'color' => 'blue',
                    'status' => '',
                ],
                [
                    'label' => 'Pending Registrations',
                    'value' => $pendingRegistrationCount,
                    'icon' => 'bi-hourglass-split',
                    'color' => 'amber',
                    'status' => '0',
                ],
                [
                    'label' => 'Approved Artists',
                    'value' => $approvedArtistCount,
                    'icon' => 'bi-check2-square',
                    'color' => 'green',
                    'status' => '1',
                ],
                [
                    'label' => 'Rejected Registrations',
                    'value' => $rejectedApplicationCount,
                    'icon' => 'bi-x-circle',
                    'color' => 'red',
                    'status' => '2',
                ],
            ];
        @endphp

        @foreach ($stats as $stat)
            <form method="POST"
                  action="{{ route('cp.artist.list') }}"
                  class="dashboard-stat-form">
                @csrf

                <input type="hidden"
                       name="registration_status"
                       value="{{ $stat['status'] }}">

                <button type="submit"
                        class="dashboard-stat dashboard-stat-link">

                    <div class="dashboard-stat-icon stat-icon-{{ $stat['color'] }}">
                        <i class="bi {{ $stat['icon'] }}" aria-hidden="true"></i>
                    </div>

                    <div>
                        <p class="dashboard-stat-label">
                            {{ $stat['label'] }}
                        </p>

                        <p class="dashboard-stat-value">
                            {{ number_format($stat['value']) }}
                        </p>
                    </div>

                    <span class="dashboard-stat-mark mark-{{ $stat['color'] }}"
                          aria-hidden="true"></span>
                </button>
            </form>
        @endforeach

    </section>
@endif
 



    <section class="dashboard-modules" aria-labelledby="dashboard-modules-title">
        <div class="dashboard-section-heading">
            <div>
                <p class="dashboard-eyebrow mb-1">WORKSPACE</p>
                <h2 id="dashboard-modules-title" class="dashboard-section-title mb-0">Your modules</h2>
            </div>
            <span class="dashboard-module-count">{{ $modules->count() }} available</span>
        </div>

        @if ($modules->isNotEmpty())
            <div class="dashboard-module-grid">
                @foreach ($modules as $module)
                    <a class="dashboard-module-link" href="{{ backendUrl($module->page_url) }}">
                        <span class="dashboard-module-icon"><i class="bi {{ $module->icon_name }}" aria-hidden="true"></i></span>
                        <span class="dashboard-module-name">{{ $module->module_name }}</span>
                        <i class="bi bi-arrow-up-right dashboard-module-arrow" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        @else
            <p class="dashboard-empty mb-0">No modules are currently available for your account.</p>
        @endif
    </section>
</div>

@push('styles')
<style>
    .dashboard-page {
        --dashboard-ink: #14233f;
        --dashboard-muted: #68758b;
        --dashboard-line: #e3e8ef;
        color: var(--dashboard-ink);
        padding: 0.5rem 0.25rem 1.5rem;
    }

    .dashboard-heading,
    .dashboard-section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
    }

    .dashboard-heading {
        padding: 0.25rem 0 1.5rem;
    }

    .dashboard-eyebrow {
        color: #526a91;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .dashboard-title {
        color: var(--color-secondary);
        font-size: 1.65rem;
        font-weight: 700;
    }

    .dashboard-subtitle,
    .dashboard-date {
        color: var(--dashboard-muted);
        font-size: 0.875rem;
    }

    .dashboard-date {
        white-space: nowrap;
        padding-bottom: 0.15rem;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
    }

    .dashboard-stat {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        min-width: 0;
        min-height: 116px;
        padding: 1.1rem;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--dashboard-line);
        border-radius: 6px;
    }

    .dashboard-stat-link {
        color: inherit;
        text-decoration: none;
        transition: box-shadow 0.15s ease, transform 0.15s ease;
    }

    .dashboard-stat-link:hover,
    .dashboard-stat-link:focus-visible {
        color: inherit;
        box-shadow: 0 0.4rem 1rem rgba(20, 35, 63, 0.12);
        transform: translateY(-2px);
    }

    .dashboard-stat-icon,
    .dashboard-module-icon {
        display: grid;
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        place-items: center;
        border-radius: 6px;
        font-size: 1.15rem;
    }

    .stat-icon-blue { color: #174b91; background: #eaf2fc; }
    .stat-icon-green { color: #16704d; background: #e8f5ee; }
    .stat-icon-amber { color: #925400; background: #fff3dc; }
    .stat-icon-red { color: #a83b45; background: #fbecee; }

    .dashboard-stat-label {
        margin: 0 0 0.2rem;
        color: var(--dashboard-muted);
        font-size: 0.78rem;
        line-height: 1.35;
    }

    .dashboard-stat-value {
        margin: 0;
        color: var(--dashboard-ink);
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1.15;
        font-variant-numeric: tabular-nums;
    }

    .dashboard-stat-mark {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 34px;
        height: 4px;
    }

    .mark-blue { background: #3474b9; }
    .mark-green { background: #3b9870; }
    .mark-amber { background: #d6952d; }
    .mark-red { background: #c35b63; }

    .dashboard-modules {
        margin-top: 1.75rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--dashboard-line);
    }

    .dashboard-section-heading {
        align-items: center;
        margin-bottom: 0.9rem;
    }

    .dashboard-section-title {
        color: var(--color-secondary);
        font-size: 1.15rem;
        font-weight: 700;
    }

    .dashboard-module-count {
        color: var(--dashboard-muted);
        font-size: 0.8rem;
    }

    .dashboard-module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 220px), 1fr));
        gap: 0.75rem;
    }

    .dashboard-module-link {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        min-width: 0;
        min-height: 76px;
        padding: 0.9rem 1rem;
        color: var(--dashboard-ink);
        text-decoration: none;
        background: #fff;
        border: 1px solid var(--dashboard-line);
        border-radius: 6px;
        transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
    }

    .dashboard-module-link:hover,
    .dashboard-module-link:focus-visible {
        color: var(--color-primary);
        border-color: #9aacc8;
        box-shadow: 0 4px 14px rgb(20 35 63 / 8%);
        transform: translateY(-2px);
    }

    .dashboard-module-link:focus-visible {
        outline: 2px solid #3474b9;
        outline-offset: 2px;
    }

    .dashboard-module-icon {
        flex-basis: 38px;
        width: 38px;
        height: 38px;
        color: var(--color-primary);
        background: #eef3fa;
        font-size: 1rem;
    }

    .dashboard-module-name {
        flex: 1;
        min-width: 0;
        overflow-wrap: anywhere;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .dashboard-module-arrow {
        color: #8491a5;
        font-size: 0.85rem;
    }

    .dashboard-empty {
        padding: 1rem;
        color: var(--dashboard-muted);
        background: #fff;
        border: 1px dashed var(--dashboard-line);
        border-radius: 6px;
    }

    @media (max-width: 991.98px) {
        .dashboard-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (max-width: 575.98px) {
        .dashboard-heading { align-items: flex-start; flex-direction: column; gap: 0.55rem; }
        .dashboard-title { font-size: 1.4rem; }
        .dashboard-date { white-space: normal; }
        .dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.65rem; }
        .dashboard-stat { min-height: 100px; gap: 0.6rem; padding: 0.8rem; }
        .dashboard-stat-icon { flex-basis: 38px; width: 38px; height: 38px; font-size: 1rem; }
        .dashboard-stat-label { font-size: 0.72rem; }
        .dashboard-stat-value { font-size: 1.45rem; }
        .dashboard-module-grid { grid-template-columns: minmax(0, 1fr); }
    }
</style>
@endpush
@endsection
