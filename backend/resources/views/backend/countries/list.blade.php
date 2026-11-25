@extends('backend.layouts.app')
@section('title', 'Country Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-0">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Country Management</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row py-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-data align-middle">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Country</th>
                                <th>Country Code</th>
                                <th>Flag</th>
								<th>Type</th>
                                <th>BRICS</th>							
                                <th>Status</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($countries as $country)
                                <tr>
                                    <th scope="row" class="text-end">{{ $loop->iteration }}</th>
                                    <td>{{ $country->name ?? '—' }}</td>
                                    <td>{{ $country->country_code ?? '—' }}</td>
                                  
                                    <td>
                                        @if (!empty($country->flag_image))
                                          <img src="{{ asset('/assets/frontend/v1/images/flags/' . $country->flag_image) }}"    alt="{{ $country->name ?? 'Country' }} flag"   width="72"    height="62"    style="object-fit: cover;">
                                        @else
                                            —
                                        @endif
                                    </td>
									 <td>{{ (int) $country->type === 1 ? 'Member Countries' : ((int) $country->type === 2 ? 'Partner Countries' : '—') }}</td>
                                    <td>
                                        <span class="badge {{ (int) $country->brics_flag === 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ (int) $country->brics_flag === 1 ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <!--td>
                                        <span class="badge {{ (int) $country->status === 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ (int) $country->status === 1 ? 'Active' : 'Inactive' }}
                                        </span>
										
                                    </td-->
									<td class="text-center">
    @permission('edit', 'countries')
        <div class="form-check form-switch d-flex justify-content-center">
            <input
                class="form-check-input country-status-change pointer"
                type="checkbox"
                role="switch"
                data-id="{{ $country->id }}"
                data-status="{{ $country->status }}"
                {{ $country->status == 1 ? 'checked' : '' }}>
        </div>
    @else
        <span class="badge {{ $country->status ? 'bg-success' : 'bg-danger' }}">
            {{ $country->status ? 'Active' : 'Inactive' }}
        </span>
    @endpermission
</td>
                                   
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-center py-4 text-muted">
                                        No countries found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
		   (() => {
			if (typeof DataTable === 'undefined') return;

			new DataTable('.table-data', {
				responsive: true,
				pageLength: 25,
				lengthMenu: [
					[25, 50, 100, -1],
					[25, 50, 100, 'All']
				],
				order: [[1, 'asc']],
				language: {
					emptyTable: 'No countries found.',
					zeroRecords: 'No matching countries found.',
					lengthMenu: 'Show _MENU_ entries'
				},
				columnDefs: [
					{ orderable: false, targets: [6] }
				]
			});
		})();
		
	$(document).on('change', '.country-status-change', function () {

    let checkbox = $(this);
    let id = checkbox.data('id');
    let oldStatus = Number(checkbox.data('status'));
    let newStatus = oldStatus === 1 ? 0 : 1;

    // Prevent immediate UI change until confirmation
    checkbox.prop('checked', oldStatus === 1);

    Swal.fire({
        title: 'Change Country Status?',
        text: newStatus === 1
            ? 'Do you want to activate this country?'
            : 'Do you want to deactivate this country?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Update',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: @json(route('cp.countrie.status')),
            type: 'POST',
            data: {
                _token: @json(csrf_token()),
                id: id,
                status: newStatus
            },

            beforeSend: function () {
                checkbox.prop('disabled', true);
            },

            success: function (response) {

                if (response.success) {

                    checkbox.data('status', response.status);
                    checkbox.prop('checked', response.status == 1);

                    Swal.fire({
                        title: 'Updated!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },

            error: function (xhr) {

                checkbox.prop('checked', oldStatus === 1);

                Swal.fire(
                    'Error!',
                    xhr.responseJSON?.message || 'Unable to update country status.',
                    'error'
                );
            },

            complete: function () {
                checkbox.prop('disabled', false);
            }
        });
    });
});

    </script>
@endpush
