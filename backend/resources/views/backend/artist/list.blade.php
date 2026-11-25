@extends('backend.layouts.app')

@section('title', 'Artist Registration Management')

@section('content')
    <div class="container-fluid artist-registration-list">
        <div class="row mb-0">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Artist Registration Management </h1>
                    <div class="d-flex justify-content-center"><div class="border-bottom border-3 border-primary rounded-pill w-75"></div></div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-12">
                <div class="bg-light border rounded-3 shadow-sm p-3">
                    <form method="POST" action="{{ route('cp.artist.list') }}">
						  @csrf
                        <div class="row align-items-end">
							
							<div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label fw-bold" for="registration_type">Registration Type</label>
                                <select class="form-select" id="registration_type" name="registration_type">
                                    <option value="">All</option>
                                    @foreach (['individual' => 'Individual Artist', 'group' => 'Artist Group', 'institutional' => 'Institute / Organization'] as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['registration_type'] ?? '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
							
							<div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label fw-bold" for="registration_status">Registration Status</label>		
								<select class="form-select" id="registration_status" name="registration_status">
									<option value="">All</option>
									@foreach (['0' => 'Pending', '1' => 'Approved', '2' => 'Rejected'] as $value => $label)
										<option value="{{ $value }}" {{ (string) old('registration_status', $filters['registration_status'] ?? '') === (string) $value ? 'selected' : '' }}>
											{{ $label }}
										</option>
									@endforeach
								</select>
                            </div>
							
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label fw-bold" for="search">Search</label>
                                <input class="form-control" type="search" id="search" name="search" maxlength="255"
                                    value="{{ $filters['search'] ?? '' }}" placeholder="Registration number, name, email or mobile">
                            </div>
                        
                         
                            <div class="col-lg-2 col-md-6 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" class="theme-btn rounded-2 py-2 px-4">Search</button>
                                    <a href="{{ route('cp.artist.list') }}" class="border-btn rounded-2 py-2 px-3">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @permission('create', 'artist')
            <div class="d-flex justify-content-end gap-2 mb-2">
                <a href="{{ route('cp.artist.import.index') }}" class="border-btn rounded-2 py-2 px-3"><i class="bi bi-upload me-1"></i> Bulk Import</a>
                <a href="{{ route('cp.artist.create') }}" class="theme-btn rounded-2"><i class="bi bi-plus-lg me-1"></i> Artist Registration</a>
            </div>
        @endpermission

        <div class="row py-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th width="5%">S.No.</th>
                                <th width="23%">Registration Number / </br> Registration Type </th>
                                <th width="20%">Artist / Group  /</br> Institution  Name</th>     
                                <th width="15%">Country</th>
                                <!--th>Art Form</th-->
                                <th width="15%">Email / Mobile</th>
                                <th width="7%">Status</th>
                                <th width="8%">Submission Date</th>
                                <th width="8%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
						
						@forelse ($artists as $artist)
								@php
									$artistRouteId = rtrim(strtr(encrypt((string) $artist->user_id), '+/', '-_'), '=');
									$registrationNumber = $artist->regno ?: 'AR-' . str_pad((string) $artist->user_id, 8, '0', STR_PAD_LEFT);
									$statusLabel = $artist->verified_status === null ? 'Not Tracked' : match ((int) $artist->verified_status) { 0 => 'Pending', 1 => 'Approved', 2 => 'Rejected', default => 'Not Tracked' };
									$statusBadgeClass = $artist->verified_status === null ? 'bg-secondary' : match ((int) $artist->verified_status) { 0 => 'bg-warning text-dark', 1 => 'bg-success', 2 => 'bg-danger', default => 'bg-secondary' };
								@endphp
								<tr>
									<td>{{ $artists->firstItem() + $loop->index }}</td>
									<td>{{ $registrationNumber }} <br> {{ ['individual' => 'Individual Artist', 'group' => 'Artist Group', 'institutional' => 'Institute / Organization'][$artist->registration_type] ?? '—' }}</td>
									<td>{{ $artist->display_name ?: $artist->applicant_name ?: '—' }}</td>
									<td>{{ $artist->country_name ?: '—' }}</td>
									<td><span class="d-block">{{ $artist->email ?: '—' }}</span><small class="text-muted">{{ $artist->mobile ?: '—' }}</small></td>
									<td><span class="badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
									<td>{{ $artist->submitted_at ? \Illuminate\Support\Carbon::parse($artist->submitted_at)->format('d-m-Y') : '—' }}</td>
									<td class="text-nowrap">
										@permission('view', 'artist')
											<a href="{{ route('cp.artist.preview', $artistRouteId) }}" class="btn btn-sm btn-outline-info" title="Preview" aria-label="Preview registration"><i class="bi bi-eye"></i></a>
										@endpermission
										@permission('edit', 'artist')
												@if ((int) $artist->verified_status === 0)
												<a href="{{ route('cp.artist.edit', $artistRouteId) }}" class="btn btn-sm btn-outline-primary" title="Edit" aria-label="Edit registration"><i class="bi bi-pencil-square"></i></a>
										
												<!--button type="button" class="btn btn-sm btn-outline-success registration-action" data-id="{{ $artist->user_id }}" data-status="approved" title="Approve"><i class="bi bi-check-lg"></i></button>
												<button type="button" class="btn btn-sm btn-outline-danger registration-action" data-id="{{ $artist->user_id }}" data-status="rejected" title="Reject"><i class="bi bi-x-lg"></i></button-->
											@endif
										@endpermission
									</td>
								</tr>
							@empty
								<tr><td colspan="8" class="text-center py-4">No artist registrations found.</td></tr>
							@endforelse
                           
                        </tbody>
                    </table>
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                    <small class="text-muted">
                        Showing {{ $artists->firstItem() ?? 0 }}–{{ $artists->lastItem() ?? 0 }} of {{ $artists->total() }} 
                    </small>
                  	{{ $artists->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('.registration-action').on('click', function () {
                const button = $(this);
                const status = button.data('status');
                const label = status === 'approved' ? 'approve' : 'reject';
                if (!window.confirm('Are you sure you want to ' + label + ' this pending registration?')) return;

                $.ajax({
                    url: @json(route('cp.artist.status')),
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: @json(csrf_token()),
                        id: button.data('id'),
                        status: status
                    }
                }).done(function (response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        window.alert(response.message || 'Unable to update registration status.');
                    }
                }).fail(function (xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Unable to update registration status.';
                    window.alert(message);
                });
            });
        });
    </script>
@endpush
