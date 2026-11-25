@extends('backend.layouts.app')

@section('title', 'Artist Registration Preview')

@section('content')
    @php
        $detail = $individual ?? $group ?? $institution;
        $displayName = match ($type) {
            'group' => $group->group_name ?? $artist->full_name,
            'institutional' => $institution->institution_name ?? $artist->full_name,
            default => $artist->full_name,
        };
        $status = $artist->verified_status === null ? 'not tracked' : match ((int) $artist->verified_status) { 0 => 'pending', 1 => 'approved', 2 => 'rejected', default => 'not tracked' };
        $registrationNumber = $artist->regno ?: 'AR-'.str_pad((string) $artist->id, 8, '0', STR_PAD_LEFT);
        $documentPaths = [];
        if ($type === 'individual') {
            $documentPaths = [
                'Profile photograph' => $individual->profile_photo ?? null,
                'Identity document' => $individual->identity_document ?? null,
            ];
        } elseif ($type === 'group') {
            $documentPaths = [
                'Profile photograph' => $group->profile_photo ?? null,
                'Identity document' => $group->identity_document ?? null,
                'Supporting documents' => explode(',', (string) ($group->supporting_document ?? '')),
            ];
        } else {
            $documentPaths = [
                'Institution logo' => $institution->institution_logo ?? null,
                'Identity document' => $institution->identity_document ?? null,
                'Authorization letter' => $institution->authorization_letter ?? null,
                'Brochure / profile' => $institution->brochure_path ?? null,
                'Registration certificate' => $institution->registration_certificate ?? null,
                'Supporting documents' => explode(',', (string) ($institution->supporting_documents ?? '')),
            ];
        }
    @endphp
    <div class="container-fluid py-3 artist-preview-page">
        <div class="row mb-4 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="h3 mb-2 fw-bold">Artist Registration Preview</h1>
                    <p class="text-muted mb-0 small">Review registration details, documents and approval status</p>
                </div>
                <a href="{{ route('cp.artist.list') }}" class="btn btn-outline-secondary rounded-3 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        @if (session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @if ($errors->any())<div class="alert alert-danger" role="alert">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

        <div class="card mb-4">
            <div class="card-header fw-bold"><i class="bi bi-person-vcard me-1"></i> Registration Information</div>
            <div class="card-body">
                <div class="row g-3">
					 <div class="col-lg-3"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Registration Type</small>{{ ['individual' => 'Individual Artist', 'group' => 'Artist Group', 'institutional' => 'Institutional Artist'][$type] }}</div></div>
					 <div class="col-lg-3"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Registration Number</small>{{ $registrationNumber }}</div></div>
                    <div class="col-lg-6"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Artist / Group  / Institution  Name</small>{{ $displayName ?: '—' }}</div></div>
					
						
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Email</small><a href="mailto:{{ $artist->email }}">{{ $artist->email }}</a></div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Mobile</small>{{ $artist->mobile ?: '—' }}</div></div>
                   
                   
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Country</small>{{ $country ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Region / State</small>{{ $region ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">City / Town</small>{{ $artist->city ?: ($group->district_city ?? $institution->district_city ?? '—') }}</div></div>
					
				
            
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Postal / ZIP Code</small>{{ $artist->pincode ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Mailing Address</small>{{ $artist->address ?: '—' }}</div></div>
					
					 <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Submitted</small>{{ $detail->created_at ? \Illuminate\Support\Carbon::parse($detail->created_at)->format('d-m-Y H:i') : '—' }}</div></div>
					 
					   <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Registration Status</small><span class="badge {{ $status === 'approved' ? 'bg-success' : ($status === 'rejected' ? 'bg-danger' : ($status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary')) }}">{{ ucfirst($status) }}</span></div></div>
					
                    <div class="col-12"><div class="border rounded p-3"><small class="text-muted d-block">Art Form(s)</small>{{ $artForms->isNotEmpty() ? $artForms->values()->implode(', ') : ($individual->other_art_form ?? $group->other_art_form ?? $institution->other_art_form ?? '—') }}</div></div>
                    <div class="col-12"><div class="border rounded p-3"><small class="text-muted d-block">Languages</small>{{ $languages->isNotEmpty() ? $languages->implode(', ') : '—' }}</div></div>
                </div>
            </div>
        </div>

        @if ($type === 'individual')
            <div class="card mb-4">
                <div class="card-header fw-bold">Individual Artist Details</div>
                <div class="card-body"><div class="row g-3">
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Date of Birth</small>{{ $artist->dob ? \Illuminate\Support\Carbon::parse($artist->dob)->format('d-m-Y') : '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Gender</small>{{ [1 => 'Male', 2 => 'Female', 3 => 'Other'][$artist->gender] ?? '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Subgenre / Tradition</small>{{ $individual->other_subgenre ?: ($artForms[$individual->subgenre_id] ?? '—') }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Years of Practice</small>{{ $individual->years_of_practice ?? '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Teacher / Guru</small>{{ $individual->teacher_guru ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Institutional Affiliation</small>{{ $individual->institutional_affiliation ?: '—' }}</div></div>
                    <div class="col-12"><div class="border rounded p-3"><small class="text-muted d-block">Biography</small><div class="text-break">{!! nl2br(e($individual->biography ?: '—')) !!}</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Public Profile Consent</small>{{ $individual->public_profile ? 'Yes' : 'No' }}</div></div>
                </div></div>
            </div>
        @elseif ($type === 'group')
            <div class="card mb-4">
                <div class="card-header fw-bold">Artist Group Details</div>
                <div class="card-body"><div class="row g-3">
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Group Type</small>{{ $group->other_group_type ?: ($group->group_type ?: '—') }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Establishment Year</small>{{ $group->establishment_year ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Registration Number</small>{{ $group->registration_number ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Representative</small>{{ $artist->full_name ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Representative Designation</small>{{ $artist->designation ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Total Members</small>{{ $group->total_members }}</div></div>
                    <div class="col-12"><div class="border rounded p-3"><small class="text-muted d-block">Group Description</small><div class="text-break">{!! nl2br(e($group->group_description ?: '—')) !!}</div></div></div>
                    <div class="col-12"><div class="border rounded p-3"><small class="text-muted d-block">Group member details</small>Individual member records are not stored in the current registration schema.</div></div>
                </div></div>
            </div>
        @else
            <div class="card mb-4">
                <div class="card-header fw-bold">Cultural Institution Details</div>
                <div class="card-body"><div class="row g-3">
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Institution Type</small>{{ $institution->other_institution_type ?: ($institution->institution_type ?: '—') }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Establishment Year</small>{{ $institution->establishment_year ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Registration Type / Number</small>{{ $institution->institution_registration_type ?: '—' }} {{ $institution->registration_number ?: '' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Representative</small>{{ $artist->full_name ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Representative Designation</small>{{ $artist->designation ?: '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Associated Artists</small>{{ $institution->associated_artists ?? '—' }}</div></div>
                    <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">Website</small>@if ($institution->website)<a href="{{ $institution->website }}" target="_blank" rel="noopener">{{ $institution->website }}</a>@else — @endif</div></div>
                    @foreach (['Institution Description' => $institution->institution_description, 'Cultural Activities' => $institution->cultural_activities, 'Training / Research Activities' => $institution->training_research_activities, 'Major Events' => $institution->major_events, 'Collaborations' => $institution->collaborations] as $label => $value)
                        <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted d-block">{{ $label }}</small><div class="text-break">{!! nl2br(e($value ?: '—')) !!}</div></div></div>
                    @endforeach
                </div></div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header fw-bold">Documents</div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($documentPaths as $label => $paths)
                        @foreach (is_array($paths) ? $paths : [$paths] as $path)
                            @if ($path)
                                <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">{{ $label }}</small><a href="{{ asset('uploads/'.$path) }}" target="_blank" rel="noopener">{{ basename($path) }}</a></div></div>
                            @endif
                        @endforeach
                    @endforeach
                    @foreach ($awards as $award)
                        @if ($award->certificate_path)
                            <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted d-block">{{ $award->award_name ?: 'Award' }} certificate</small><a href="{{ asset('uploads/'.$award->certificate_path) }}" target="_blank" rel="noopener">{{ basename($award->certificate_path) }}</a></div></div>
                        @endif
                    @endforeach
                </div>
                @if (collect($documentPaths)->flatten()->filter()->isEmpty() && $awards->whereNotNull('certificate_path')->isEmpty())
                    <p class="text-muted mb-0">No documents have been uploaded.</p>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-bold">Awards and Portfolio</div>
            <div class="card-body">
                @if ($awards->isNotEmpty())
                    <div class="table-responsive"><table class="table table-bordered align-middle"><thead><tr><th>Award</th><th>Year</th><th>Description</th></tr></thead><tbody>
                        @foreach ($awards as $award)<tr><td>{{ $award->award_name ?: '—' }}</td><td>{{ $award->award_year ?: '—' }}</td><td>{{ $award->description ?: '—' }}</td></tr>@endforeach
                    </tbody></table></div>
                @else <p class="text-muted">No award records.</p> @endif
                @if ($portfolioLinks->isNotEmpty())
                    <ul class="mb-0">@foreach ($portfolioLinks as $link)<li><a href="{{ $link }}" target="_blank" rel="noopener">{{ $link }}</a></li>@endforeach</ul>
                @else <p class="text-muted mb-0">No portfolio links.</p> @endif
            </div>
        </div>

        @if ($status === 'rejected' && !empty($rejectionReason))
            <div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <div>
                    <strong>Rejection Remarks</strong>
                    <div class="mt-1 text-break">{!! nl2br(e($rejectionReason)) !!}</div>
                </div>
            </div>
        @endif

        @if ($status === 'approved' && !empty($approvalRemarks))
            <div class="alert alert-success d-flex align-items-start gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>
                    <strong>Approval Remarks</strong>
                    <div class="mt-1 text-break">{!! nl2br(e($approvalRemarks)) !!}</div>
                </div>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2 mb-4">
            @permission('edit', 'artist')
                @if ($status === 'pending')
                    <button type="button" class="btn btn-success rounded-3 px-4 js-open-status-modal"
                            data-status="approved" data-title="Approve Registration" data-button="Confirm Approval">
                        <i class="bi bi-check-circle me-1"></i> Approve
                    </button>

                    <button type="button" class="btn btn-danger rounded-3 px-4 js-open-status-modal"
                            data-status="rejected" data-title="Reject Registration" data-button="Confirm Rejection">
                        <i class="bi bi-x-circle me-1"></i> Reject
                    </button>

                    <a class="btn btn-outline-primary rounded-3 px-4"
                       href="{{ route('cp.artist.edit', rtrim(strtr(encrypt((string) $artist->id), '+/', '-_'), '=')) }}">
                        <i class="bi bi-pencil-square me-1"></i> Edit Registration
                    </a>
                @endif
            @endpermission
            <a href="{{ route('cp.artist.list') }}" class="btn btn-outline-secondary rounded-3 px-4">Back to List</a>
        </div>

        @permission('edit', 'artist')
            @if ($status === 'pending')
                <div class="modal fade" id="artistStatusModal" tabindex="-1" aria-labelledby="artistStatusModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form method="POST" action="{{ route('cp.artist.status') }}" id="artistStatusForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $artist->id }}">
                                <input type="hidden" name="status" id="artistStatusValue" value="">

                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="artistStatusModalLabel">Update Registration Status</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <p id="artistStatusMessage" class="text-muted mb-3">Please confirm your action.</p>
                                    <div id="artistRemarksWrapper" class="d-none">
                                        <label for="artistStatusRemarks" class="form-label fw-semibold">
                                            Rejection Remarks <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" name="remarks" id="artistStatusRemarks"
                                                  rows="4" maxlength="5000"
                                                  placeholder="Please provide the reason for rejection."></textarea>
                                        <div class="form-text">Remarks are required when rejecting a registration.</div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" id="artistStatusSubmit" class="btn btn-success">Confirm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endpermission
    </div>

@push('styles')
    <style>
        .artist-preview-page .card {
            border: 1px solid #e7ebf0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 3px 14px rgba(20, 35, 60, .04);
        }
        .artist-preview-page .card-header {
            background: #f7f9fc;
            border-bottom: 1px solid #e7ebf0;
            padding: 1rem 1.25rem;
            color: #243247;
        }
        .artist-preview-page .border.rounded.p-3 {
            border-color: #e6eaf0 !important;
            border-radius: 10px !important;
            background: #fff;
            overflow-wrap: anywhere;
        }
        .artist-preview-page .border.rounded.p-3 small {
            font-size: .76rem;
            letter-spacing: .025em;
            text-transform: uppercase;
            margin-bottom: .4rem;
        }
        .artist-preview-page .badge {
            padding: .45rem .7rem;
            font-size: .78rem;
            letter-spacing: .02em;
        }
        .artist-preview-page .table thead th {
            background: #f7f9fc;
            white-space: nowrap;
        }
        .artist-preview-page .btn { font-weight: 600; }
        @media (max-width: 767.98px) {
            .artist-preview-page .row.mb-4 > .col-12 { text-align: left !important; }
            .artist-preview-page .row.mb-4 .position-absolute {
                position: static !important;
                transform: none !important;
                margin-top: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('artistStatusModal');
            const form = document.getElementById('artistStatusForm');

            if (!modalElement || !form || typeof bootstrap === 'undefined') return;

            const modal = new bootstrap.Modal(modalElement);
            const statusInput = document.getElementById('artistStatusValue');
            const title = document.getElementById('artistStatusModalLabel');
            const message = document.getElementById('artistStatusMessage');
            const remarksWrapper = document.getElementById('artistRemarksWrapper');
            const remarks = document.getElementById('artistStatusRemarks');
            const submitButton = document.getElementById('artistStatusSubmit');

            document.querySelectorAll('.js-open-status-modal').forEach(function (button) {
                button.addEventListener('click', function () {
                    const selectedStatus = button.dataset.status;
                    const isRejecting = selectedStatus === 'rejected';

                    statusInput.value = selectedStatus;
                    title.textContent = button.dataset.title || 'Update Registration Status';
                    message.textContent = isRejecting
                        ? 'Are you sure you want to reject this registration? Please provide the rejection remarks below.'
                        : 'Are you sure you want to approve this registration?';

                    remarksWrapper.classList.toggle('d-none', !isRejecting);
                    remarks.required = isRejecting;
                    remarks.value = '';
                    remarks.setCustomValidity('');

                    submitButton.textContent = button.dataset.button || 'Confirm';
                    submitButton.className = 'btn ' + (isRejecting ? 'btn-danger' : 'btn-success');
                    modal.show();
                });
            });

            form.addEventListener('submit', function (event) {
                if (statusInput.value === 'rejected' && !remarks.value.trim()) {
                    event.preventDefault();
                    remarks.setCustomValidity('Please enter the rejection remarks.');
                    remarks.reportValidity();
                    remarks.focus();
                    return;
                }

                remarks.setCustomValidity('');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...';
            });

            remarks.addEventListener('input', function () {
                remarks.setCustomValidity('');
            });
        });
    </script>
@endpush
@endsection
