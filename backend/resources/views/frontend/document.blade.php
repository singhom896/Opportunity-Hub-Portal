@extends('frontend.layouts.app')
@section('title', 'Guidelines & Documents')

@section('content')
<section class="brx-page-header" aria-labelledby="brxPageTitle">
    <div class="container brx-page-header__inner">
        <nav class="brx-page-header__breadcrumb" aria-label="Breadcrumb">
            <a class="brx-page-header__crumb-link" href="{{ url('/') }}">Home</a>
            <span class="brx-page-header__crumb-sep" aria-hidden="true">/</span>
            <span class="brx-page-header__crumb-current" aria-current="page">Documents &amp; Guidelines</span>
        </nav>

        <span class="brx-page-header__eyebrow">BRICS ARTIST REGISTRY</span>

        <h1 class="brx-page-header__title" id="brxPageTitle">
            <span class="brx-page-header__title-main">Official Publications – Documents</span>
        </h1>
        <small class="brx-page-header__description">Access official notification of the BRICS Artist Registry.</small>
    </div>
</section>

<section class="py-4 py-lg-5">
    <div class="container">
	

		<h2 class="section-title text-center">   Official Publications </h2>
	
			
		<main class="directory-main py-4 ">
            <div class="wrap">

                <section class="artist-table-wrapper card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 px-3 px-md-4 pt-4 pb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-auto">
                                <select id="documentLength" class="form-select document-length"
                                    aria-label="Number of documents per page">
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-auto ms-md-auto">
                                <div class="input-group document-search">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search text-primary" aria-hidden="true"></i>
                                    </span>
                                    <input type="search" id="documentSearch" class="form-control"
                                        placeholder="Search..." aria-label="Search documents" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive px-2 px-md-3">
                        <table id="documents-table" class="artist-table table table-hover table-striped align-middle w-100 mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width:75px;">S.No</th>
                                    <th scope="col">Title</th>
                                    <th scope="col" style="width:165px;">Published Date</th>
                                    <th scope="col" style="width:135px;">Size</th>
                                    <th scope="col" class="text-center" style="width:120px;">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $rsdata)
                                    @php
                                        $documentLink = null;

                                        if ((int) $rsdata->document_type === 2) {
                                            $documentLink = $rsdata->document_url;
                                        } elseif ((int) $rsdata->document_type === 1 && optional($rsdata->asset)->url) {
                                            $documentLink = asset($rsdata->asset->url);
                                        }

                                        $documentSize = '—';
                                        if ((int) $rsdata->document_type === 1 && optional($rsdata->asset)->size) {
                                            $documentSize = function_exists('formatBytes')
                                                ? formatBytes($rsdata->asset->size)
                                                : ($rsdata->asset->size_mb ?? '—') . (isset($rsdata->asset->size_mb) ? ' MB' : '');
                                        } elseif ((int) $rsdata->document_type === 2) {
                                            $documentSize = '-';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $rsdata->title ?? '—' }}</td>
                                       <td>
											{{ $rsdata->publish_start_date
												? \Carbon\Carbon::parse($rsdata->publish_start_date)->format('d-m-Y')
												: '—' }}
										</td>
                                        <td>{{ $documentSize }}</td>
                                        <td class="text-center">
                                            @if ($documentLink)
                                                <a href="{{ $documentLink }}" target="_blank" rel="noopener noreferrer"
                                                    class="view-profile btn btn-sm">
                                                    <i class="bi bi-eye me-1" aria-hidden="true"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted">Not Available</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-secondary">
                                            No documents are currently available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </section>

            </div>
        </main>

		
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
    .document-length {
        width: 140px;
        min-width: 140px;
        font-size: 14px;
    }

    .document-search {
        width: 240px;
        max-width: 100%;
    }

    .document-search .form-control,
    .document-search .input-group-text {
        font-size: 14px;
    }

    @media (max-width: 767.98px) {
        .document-length,
        .document-search {
            width: 100%;
        }
    }

    #documents-table thead th {
        white-space: nowrap;
        font-weight: 600;
        vertical-align: middle;
    }
    #documents-table tbody td {
        vertical-align: middle;
    }
    #documents-table tbody td:nth-child(2) {
        min-width: 260px;
        white-space: normal;
        line-height: 1.5;
    }
    #documents-table .view-profile {
        white-space: nowrap;
    }
    .documents-table-footer {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
    }
    #documents-table_wrapper .dataTables_paginate .pagination {
        margin-bottom: 0;
        justify-content: flex-end;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function () {
        const table = $('#documents-table').DataTable({
            processing: false,
            serverSide: false,
            searching: true,
            lengthChange: false,
            pageLength: 10,
            order: [],
            autoWidth: false,
            language: {
                emptyTable: 'No documents available.',
                zeroRecords: 'No matching documents found.',
                info: 'Showing _START_ to _END_ of _TOTAL_ documents',
                infoEmpty: 'Showing 0 to 0 of 0 documents',
                infoFiltered: '(filtered from _MAX_ total documents)',
                paginate: {
                    previous: '&laquo;',
                    next: '&raquo;'
                }
            },
            dom: 'rt<"documents-table-footer mt-3"ip>',
            drawCallback: function () {
                const info = this.api().page.info();
                $('#documentTableInfo').text(
                    info.recordsDisplay
                        ? 'Showing ' + (info.start + 1) + ' to ' + info.end +
                          ' of ' + info.recordsDisplay + ' documents'
                        : 'No documents to display'
                );
            }
        });

        $('#documentSearch').on('input', function () {
            table.search(this.value).draw();
        });

        $('#documentLength').on('change', function () {
            table.page.len(parseInt(this.value, 10)).draw();
        });
    });
</script>
@endpush
