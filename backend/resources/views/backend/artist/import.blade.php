@extends('backend.layouts.app')

@section('title', 'Bulk Artist Registration')

@section('content')
    <div class="container-fluid artist-registration-import">
        <div class="row mb-3">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Bulk Artist Registration</h1>
                    <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        @if (! $state)
            <div class="row g-3">
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-header fw-bold">1. Download template</div>
                        <div class="card-body">
                            <label class="form-label fw-bold" for="template_type">Registration type</label>
                            <select class="form-select mb-3" id="template_type">
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}" @selected(old('registration_type') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="d-flex gap-2 mb-3">
                                <a class="btn btn-outline-success" id="tpl-xlsx" href="#"><i class="bi bi-file-earmark-excel me-1"></i> Excel template</a>
                                <a class="btn btn-outline-secondary" id="tpl-csv" href="#"><i class="bi bi-filetype-csv me-1"></i> CSV template</a>
                            </div>
                            <ul class="small text-muted ps-3 mb-0">
                                <li>The Excel template has an Instructions sheet and master-data ID sheets (regions, art forms, languages, awards, types) for your country.</li>
                                <li>The CSV template contains only the header row; look up IDs in the Excel template.</li>
                                <li>Dates: <strong>YYYY-MM-DD</strong>. Gender: <strong>Male, Female or Other</strong>.</li>
                                <li>Country is taken from your login; there is no country column.</li>
                                <li>Photos, ID documents, certificates and brochures cannot be imported; they stay empty and can be added later by editing each registration.</li>
                                <li>Existing emails are skipped, never updated. Imported artists are created as <strong>Pending</strong> and receive an account setup email.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header fw-bold">2. Upload and validate</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('cp.artist.import.preview') }}" enctype="multipart/form-data" id="upload-form">
                                @csrf
                                <label class="form-label fw-bold" for="registration_type">Registration type</label>
                                <select class="form-select mb-3" name="registration_type" id="registration_type" required>
                                    @foreach ($types as $key => $label)
                                        <option value="{{ $key }}" @selected(old('registration_type') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <label class="form-label fw-bold" for="file">File (.xlsx or .csv, max 5 MB, {{ $maxRows }} rows)</label>
                                <input class="form-control mb-3" type="file" name="file" id="file" accept=".xlsx,.csv" required>
                                <button class="theme-btn rounded-2" type="submit" id="upload-btn"><i class="bi bi-search me-1"></i> Validate &amp; preview</button>
                                <div class="form-text mt-2">Nothing is saved until you confirm on the next screen.</div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @php
                $statusClass = ['valid' => 'success', 'imported' => 'success', 'failed' => 'danger', 'skipped' => 'warning'];
                $statusLabel = ['valid' => 'Ready', 'imported' => 'Imported', 'failed' => 'Failed', 'skipped' => 'Skipped'];
            @endphp
            <div class="card mb-3">
                <div class="card-header fw-bold">
                    {{ $types[$state['type']] }} &mdash; {{ $state['file_name'] }}
                </div>
                <div class="card-body">
                    <div class="row text-center g-2 mb-3">
                        <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold" id="c-total">{{ $counts['total'] }}</div>Total rows</div></div>
                        <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-success" id="c-ready">{{ $counts['pending'] }}</div>Ready to import</div></div>
                        <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-success" id="c-imported">{{ $counts['imported'] }}</div>Imported</div></div>
                        <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-danger" id="c-failed">{{ $counts['failed'] }}</div>Failed</div></div>
                        <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-warning" id="c-skipped">{{ $counts['skipped'] }}</div>Skipped</div></div>
                    </div>

                    <div class="progress mb-3 d-none" id="progress-wrap" style="height: 22px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" style="width: 0%">0%</div>
                    </div>
                    <div class="alert d-none" id="import-status" role="alert"></div>

                    <div class="d-flex flex-wrap gap-2">
                        @if ($counts['pending'] > 0)
                            <button class="theme-btn rounded-2" type="button" id="confirm-btn"><i class="bi bi-check2-circle me-1"></i> Confirm &amp; import {{ $counts['pending'] }} valid row(s)</button>
                        @endif
                        <a class="border-btn rounded-2 py-2 px-3 {{ ($counts['failed'] + $counts['skipped'] + $counts['pending']) === 0 ? 'd-none' : '' }}" id="report-link" href="{{ route('cp.artist.import.report', $token) }}"><i class="bi bi-download me-1"></i> Download failed/skipped report</a>
                        <a class="border-btn rounded-2 py-2 px-3" href="{{ route('cp.artist.import.index') }}">Start new import</a>
                        <a class="border-btn rounded-2 py-2 px-3" href="{{ route('cp.artist.list') }}">Back to registrations</a>
                    </div>
                    @if ($counts['pending'] > 0 && $counts['failed'] + $counts['skipped'] > 0)
                        <div class="form-text mt-2">Only valid rows will be imported; failed and skipped rows are left out and listed in the report.</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">Row preview</div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead>
                            <tr><th style="width:70px">Row</th><th>Name</th><th>Email</th><th style="width:110px">Status</th><th>Registration No</th><th>Message</th></tr>
                        </thead>
                        <tbody id="rows-body">
                            @foreach ($state['rows'] as $row)
                                <tr data-row="{{ $row['row'] }}">
                                    <td>{{ $row['row'] }}</td>
                                    <td>{{ $row['name'] }}</td>
                                    <td>{{ $row['email'] }}</td>
                                    <td><span class="badge text-bg-{{ $statusClass[$row['status']] }}">{{ $statusLabel[$row['status']] }}</span></td>
                                    <td>{{ $row['regno'] }}</td>
                                    <td class="small">{{ $row['message'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const typeSel = document.getElementById('template_type');
    if (typeSel) {
        const base = @json(url(config('app.backend_prefix').'/artist/import/template'));
        const update = () => {
            document.getElementById('tpl-xlsx').href = base + '/xlsx?type=' + typeSel.value;
            document.getElementById('tpl-csv').href = base + '/csv?type=' + typeSel.value;
            const upload = document.getElementById('registration_type');
            if (upload) upload.value = typeSel.value;
        };
        typeSel.addEventListener('change', update);
        update();
    }

    const form = document.getElementById('upload-form');
    if (form) form.addEventListener('submit', function () {
        const b = document.getElementById('upload-btn');
        b.disabled = true; b.innerHTML = 'Validating...';
    });

    const btn = document.getElementById('confirm-btn');
    if (!btn) return;
    @if ($token)
    const url = @json(route('cp.artist.import.process', $token));
    const wrap = document.getElementById('progress-wrap');
    const bar = document.getElementById('progress-bar');
    const status = document.getElementById('import-status');
    let busy = false;

    async function run() {
        const initial = parseInt(document.getElementById('c-ready').textContent, 10);
        while (true) {
            const res = await fetch(url, {method: 'POST', headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}});
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(data.message || 'Import request failed (' + res.status + ').');
            document.getElementById('c-ready').textContent = data.pending;
            document.getElementById('c-imported').textContent = data.imported;
            document.getElementById('c-failed').textContent = data.failed;
            document.getElementById('c-skipped').textContent = data.skipped;
            const pct = initial ? Math.round(((initial - data.pending) / initial) * 100) : 100;
            bar.style.width = pct + '%'; bar.textContent = pct + '%';
            if (data.done) return data;
        }
    }

    btn.addEventListener('click', async function () {
        if (busy) return;
        busy = true; btn.disabled = true;
        wrap.classList.remove('d-none');
        status.className = 'alert alert-info'; status.textContent = 'Importing... please keep this page open.';
        try {
            const d = await run();
            bar.classList.remove('progress-bar-animated');
            status.className = 'alert alert-success';
            status.textContent = 'Import finished: ' + d.imported + ' imported, ' + d.failed + ' failed, ' + d.skipped + ' skipped.';
            document.getElementById('report-link').classList.toggle('d-none', d.failed + d.skipped === 0);
            setTimeout(() => window.location.reload(), 2500);
        } catch (e) {
            status.className = 'alert alert-danger'; status.textContent = e.message + ' Reload the page to see the current status; already imported rows are not repeated.';
            busy = false;
        }
    });
    @endif
})();
</script>
@endpush
