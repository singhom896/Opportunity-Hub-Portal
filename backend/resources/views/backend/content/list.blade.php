@extends('backend.layouts.app')

@section('title', 'Content Details')

@section('content')

    <div class="container-fluid">

        {{-- Page Heading --}}
        <div class="row mb-0">
            <div class="col-12 text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">Content Management</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        {{-- Create Button --}}
        <div class="d-flex justify-content-end gap-2 mb-3">
            @permission('create')
                <a href="{{ route('content.create') }}" class="theme-btn rounded-2">    <i class="bi bi-plus-lg me-1"></i>  Create  </a>
            @endpermission
        </div>


        {{-- Content Table --}}
        <div class="row py-2">

            <div class="col-12">

                <div class="table-responsive">

                    <table class="table table-bordered table-data">

                        <thead>

                            <tr>

                                <th scope="col">S.No</th>



                                <th scope="col">Menu</th>
                                <th scope="col">Title</th>

                                <th scope="col">Description</th>

                                <th scope="col">Created Date</th>

                                <th scope="col">Is Active</th>

                                <th scope="col">Action</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($contents as $index => $content)

                                <tr>

                                    {{-- S.No --}}
                                    <th scope="row" class="text-end">
                                        {{ $index + 1 }}
                                    </th>





                                    {{-- Menu --}}
                                    <td>

                                        @if ($content->menu)
                                            {{ $content->menu->title }}

                                            @if ($content->menu->parent)
                                                <small class="text-muted">
                                                    ({{ $content->menu->parent->title }})
                                                </small>
                                            @endif
                                        @else
                                            —
                                        @endif

                                    </td>
                                    {{-- Title --}}
                                    <td>
                                        {{ $content->title ?? '—' }}
                                    </td>

                                    {{-- Description --}}
                                    <td>

                                        {{ Str::limit(strip_tags($content->content), 100) }}

                                    </td>


                                    {{-- Created Date --}}
                                    <td>

                                        {{ $content->created_at ? $content->created_at->format('d-M-Y') : '—' }}

                                    </td>


                                    {{-- Status --}}
                                    <td class="text-center">

                                        @permission('edit')
                                            <div class="form-check form-switch">

                                                <input class="form-check-input status-change pointer" role="button"
                                                    type="checkbox" data-id="{{ $content->id }}"
                                                    data-active="{{ $content->is_active }}"
                                                    {{ $content->is_active ? 'checked' : '' }}>

                                            </div>
                                        @else
                                            @if ($content->is_active)
                                                <span class="badge bg-success">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    Inactive
                                                </span>
                                            @endif
                                        @endpermission

                                    </td>


                                    {{-- Action --}}
                                    <td>

                                        @permission('edit')
                                            <ul
                                                class="d-flex align-items-center justify-content-center gap-3 mb-0 list-unstyled">

                                                <li>

                                                    <a href="{{ route('content.edit', encrypt($content->id)) }}"
                                                        class="fs-5 edit" title="Edit">

                                                        <i class="bi bi-pencil-square"></i>

                                                    </a>

                                                </li>

                                            </ul>
                                        @endpermission

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center">
                                        No content found.
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

            let table = new DataTable('.table-data');


            // Content Status Change
            $(document).on('click', '.status-change', function(e) {

                e.preventDefault();

                let checkbox = $(this);

                let id = checkbox.data('id');

                let active = checkbox.data('active');


                Swal.fire({

                    title: 'Change status?',

                    text: active == 1 ?
                        'Do you want to deactivate this Content?' :
                        'Do you want to activate this Content?',

                    icon: 'question',

                    showCancelButton: true,

                    confirmButtonText: 'Yes, update',

                    cancelButtonText: 'Cancel'

                }).then((result) => {

                    if (!result.isConfirmed) {

                        checkbox.prop('checked', active == 1);

                        return;

                    }


                    $.ajax({

                        url: "{{ route('content.status') }}",

                        type: "POST",

                        data: {

                            _token: "{{ csrf_token() }}",

                            id: id,

                            status: active == 1 ? 0 : 1

                        },


                        beforeSend: function() {

                            checkbox.prop('disabled', true);

                        },


                        success: function(response) {

                            if (response.success) {

                                checkbox.data(
                                    'active',
                                    response.status
                                );

                                checkbox.prop(
                                    'checked',
                                    response.status == 1
                                );


                                Swal.fire(

                                    'Updated!',

                                    response.message,

                                    'success'

                                );

                            }

                        },


                        error: function(err) {

                            checkbox.prop(
                                'checked',
                                active == 1
                            );


                            let errorMessage =
                                'Something went wrong!';


                            if (
                                err.responseJSON &&
                                err.responseJSON.message
                            ) {

                                errorMessage =
                                    err.responseJSON.message;

                            }


                            Swal.fire(

                                'Error!',

                                errorMessage,

                                'error'

                            );

                        },


                        complete: function() {

                            checkbox.prop(
                                'disabled',
                                false
                            );

                        }

                    });

                });

            });

        })();
    </script>
@endpush
