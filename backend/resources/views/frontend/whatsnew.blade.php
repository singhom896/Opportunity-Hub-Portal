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
            <span class="brx-page-header__title-main">Guidelines &amp; Official Publications</span>
        </h1>
        <small class="brx-page-header__description">Access official orders, notifications, reports, and guidance materials of the BRICS Artist Registry.</small>
    </div>
</section>

<section class="py-4 py-lg-5">
    <div class="container">
        <div class="filter-panel mb-4 p-3 p-md-4">
            <form class="filter-tools" role="search" onsubmit="return false;">
                <div class="row g-3 align-items-center">
				
					<div class="col-12 col-md-4 col-lg-2">
                        <label for="perpage" class="form-label small fw-semibold text-muted mb-1">Items Per Page</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-journal-text text-primary" aria-hidden="true"></i></span>
                            <select class="form-select" id="perpage">
                                <option value="10">10 Per Page</option>
                                <option value="20">20 Per Page</option>
                                <option value="30">30 Per Page</option>
                            </select>
                        </div>
                    </div>
						
					
                    <div class="col-12 col-md-4 col-lg-4 ">
                        <label for="custom_search" class="form-label small fw-semibold text-muted mb-1">Search Documents</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-primary" aria-hidden="true"></i></span>
                            <input id="custom_search" type="search" class="form-control" placeholder="Search title or keyword..." aria-label="Search">
                        </div>
                    </div>
					
                    <div class="col-12 col-md-4 col-lg-2 ms-lg-auto">
                        <label for="sortby" class="form-label small fw-semibold text-muted mb-1">Sort Order</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-filter-left text-primary" aria-hidden="true"></i></span>
                            <select class="form-select" id="sortby">
                                <option selected disabled>Sort By</option>
                                <option value="DESC">Latest First</option>
                                <option value="ASC">Oldest First</option>
                            </select>
                        </div>
                    </div>
                   
					
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive custom-table-wrapper p-2 p-md-3">
                <table id="documents-table" class="table custom-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 70px;">S.No</th>
                            <th scope="col">Title</th>
                            <th scope="col" style="width: 160px;">Published Date</th>
                            <th scope="col" style="width: 120px;">Size</th>
                            <th scope="col" style="width: 100px;">View</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    (() => {
        const currentRoute = "/{{ request()->path() }}";
        var table = $('#documents-table').DataTable({
            processing: false,
            serverSide: true,
            searching: false,
            lengthChange: false,
            order: [],
            pageLength: parseInt($('#perpage').val()),
            ajax: {
                url: '{{ route("yajraData") }}',
                data: function(d) {
                    d.perpage = $('#perpage').val();
                    d.search = $('#custom_search').val();
                    d.sortby = $('#sortby').val();
                    d.route  = currentRoute;
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'title',
                    name: 'title',
                    orderable: false
                },
                {
                    data: 'published_date',
                    name: 'created_at',
                    orderable: false
                },
                {
                    data: 'size',
                    name: 'asset.size',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            pagingType: "simple_numbers",
            language: {
                paginate: {
                    previous: "&#8249;",
                    next: "&#8250;"
                }
            }
        });
        $('#custom_search').on('keyup', function() {
            table.ajax.reload();
        });
        $('#perpage, #sortby').change(function() {
            table.ajax.reload();
        });
        $('#perpage').change(function() {
            table.page.len($(this).val()).draw();
        });
    })();
</script>
@endpush
