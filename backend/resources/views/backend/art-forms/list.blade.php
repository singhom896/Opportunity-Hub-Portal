@extends('backend.layouts.app')
@section('title', 'Art Forms Management')
@section('content')
<div class="container-fluid">
    <div class="row mb-0"><div class="col-12 text-center"><div class="d-inline-block">
        <h1 class="mb-1 fw-bold">Art Forms Management</h1>
        <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
    </div></div></div>

    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    @if (($authRoleId ?? null) !== 8)
    <div class="row mb-4"><div class="col-12"><div class="bg-light border rounded-3 shadow-sm p-3">
        <form action="{{ route('cp.art-forms.countrySearch') }}" method="POST">@csrf
            <div class="row align-items-end justify-content-center">
                <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0">
                    <label for="country_filter" class="form-label fw-bold mb-2">Country</label>
                    <select class="form-select rounded-1" id="country_filter" name="country_id">
                        <option value="">All Countries</option>
                        @foreach ($countries as $id => $name)
                            <option value="{{ $id }}" {{ (string)($selectedCountry ?? '') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-3 mb-lg-0"><div class="d-flex align-items-center gap-2">
                    <button type="submit" class="theme-btn rounded-2 py-2 px-4">Search</button>
                    <a href="{{ route('cp.art-forms.list') }}" class="border-btn rounded-2 py-2 px-4">Reset</a>
                </div></div>
            </div>
        </form>
    </div></div></div>
    @endif

    <div class="d-flex justify-content-end gap-2">
        @permission('create', 'art-forms')
        <a href="{{ route('cp.art-forms.create') }}" class="theme-btn rounded-2"><i class="bi bi-plus-lg me-1"></i> Create</a>
        @endpermission
    </div>

    <div class="row py-2"><div class="col-12"><div class="table-responsive">
        <table class="table table-bordered table-data">
            <thead><tr><th>S.No</th><th>Country</th><th>Art Form Name</th><th>Type</th><th>Parent Category</th><th>Description</th><th>Is Active</th><th>Action</th></tr></thead>
            <tbody>
            @foreach ($artForms as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->country_name ?? '—' }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ (int)$item->parent_id === 0 ? 'Main Category' : 'Subcategory' }}</td>
                    <td>{{ (int)$item->parent_id === 0 ? '—' : ($item->parent_name ?? '—') }}</td>
                    <td>{{ $item->description ?: '—' }}</td>
                    <td>
                        @permission('edit', 'art-forms')
                        <div class="form-check form-switch d-flex justify-content-center">
                            <input class="form-check-input status-change" type="checkbox" role="switch" data-id="{{ $item->id }}" data-status="{{ $item->status }}" {{ $item->status == 1 ? 'checked' : '' }}>
                        </div>
                        @else
                            <span class="badge {{ $item->status == 1 ? 'bg-success' : 'bg-secondary' }}">{{ $item->status == 1 ? 'Active' : 'Inactive' }}</span>
                        @endpermission
                    </td>
                    <td>
                        @permission('edit', 'art-forms')
                        <a href="{{ route('cp.art-forms.edit', encrypt($item->id)) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                        @endpermission
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div></div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    new DataTable('.table-data', {
        responsive: true,
        pageLength: 10,
        language: { emptyTable: 'No art forms found.', zeroRecords: 'No matching art forms found.' },
        columnDefs: [{ orderable: false, targets: [6, 7] }]
    });

    $(document).on('click', '.status-change', function (e) {
        e.preventDefault();
        const checkbox = $(this);
        const id = checkbox.data('id');
        const currentStatus = Number(checkbox.data('status'));
        Swal.fire({
            title: 'Change status?',
            text: currentStatus === 1 ? 'Do you want to deactivate this Art Form?' : 'Do you want to activate this Art Form?',
            icon: 'question', showCancelButton: true,
            confirmButtonText: 'Yes, update', cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: @json(route('cp.art-forms.status')), type: 'POST',
                data: { _token: @json(csrf_token()), id: id, status: currentStatus === 1 ? 0 : 1 },
                beforeSend: () => checkbox.prop('disabled', true),
                success: (response) => {
                    if (response.success) {
                        checkbox.data('status', response.status).prop('checked', response.status == 1);
                        Swal.fire('Updated!', response.message, 'success');
                    }
                },
                error: (err) => {
                    checkbox.prop('checked', currentStatus == 1);
                    Swal.fire('Error!', err.responseJSON?.message || 'Something went wrong!', 'error');
                },
                complete: () => checkbox.prop('disabled', false)
            });
        });
    });
})();
</script>
@endpush
