@extends('backend.layouts.app')

@section('title', isset($document) ? 'Edit Document' : 'Create Document')

@section('content')

    <div class="container-fluid">

        {{-- Heading --}}
        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">

                <div class="d-inline-block">

                    <h1 class="mb-1 fw-bold">
                        {{ isset($document) ? 'Edit Document' : 'Create Document' }}
                    </h1>

                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>

                </div>

                <a href="{{ route('documents.list') }}"
                    class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y">
                    <i class="bi bi-caret-left-fill" aria-hidden="true"></i>
                    Back
                </a>

            </div>
        </div>


        {{-- Form --}}
        <div class="row justify-content-between py-2">

            <div class="col-xl-12 col-12 mb-md-0 mb-3">

                <form id="document-form" action="{{ route('documents.saveUpdate') }}" method="post" class="form-box"
                    enctype="multipart/form-data">

                    @csrf

                    {{-- ID --}}
                    @if (isset($document))
                        <x-text-input name="id" type="hidden" value="{{ encrypt($document->id) }}" />
                    @endif

                    <div class="row">

                        {{-- Menu --}}
                        <div class="col-lg-4 mb-3">

                            <x-select-input name="menu" icon="bi-list" label="Select Menu"
                                selected="{{ old('menu', $document->parent_menu_id ?? '') }}" :options="$type"
                                placeholder="Select Menu" :required="true" />

                        </div>


                        {{-- Title --}}
                        <div class="col-lg-4 mb-3">
                            <x-text-input name="title" type="text" value="{{ old('title', $document->title ?? '') }}"
                                placeholder="Enter Document Title" label="Title" icon="bi-card-checklist"
                                autocomplete="off" :required="true" />

                        </div>
						
						
						<div class="col-lg-4 mb-3">

                            <x-select-input name="document_type" icon="bi-list" label="Document Type"
                                selected="{{ old('document_type', $document->document_type ?? '') }}" :options="$documentTypes"
                                placeholder="Select Document Type" :required="true" />

                        </div>
						
						


                        {{-- File --}}
                        <div class="col-lg-4 mb-3" id="pdf-upload-wrapper">

                            <div class="d-flex align-items-end gap-2">

                                <div class="flex-grow-1">

                                    <x-text-input name="file" type="file" placeholder="Upload Document"
                                        label="Document" icon="bi-upload" autocomplete="off" :required="false"
                                        accept="application/pdf" />

                                </div>

                                @if (isset($document) && $document->asset)
                                    <div class="pb-1">

                                        <a href="{{ asset($document->asset->url) }}" target="_blank"
                                            class="btn btn-outline-primary rounded-2 text-nowrap" title="View PDF">

                                            <i class="bi bi-eye me-1"></i>
                                            View

                                        </a>

                                    </div>
                                @endif

                            </div>

                            <small class="text-black d-block mt-2">
                                Only PDF files allowed, Max Size: 10MB
                            </small>

                        </div>
						
						<div class="col-lg-4 mb-3" id="document-url-wrapper">
                            <x-text-input name="document_url" type="url" value="{{ old('document_url', $document->document_url ?? '') }}"
                                placeholder="Enter Document URL" label="Document URL" icon="bi-card-checklist"
                                autocomplete="off"  />

                        </div>


                        {{-- Publish Start Date --}}
                        <div class="col-lg-4 mb-3">

                            <x-text-input name="publish_start_date" type="datetime-local"
                                value="{{ old(
                                    'publish_start_date',
                                    isset($document) && $document->publish_start_date
                                        ? \Carbon\Carbon::parse($document->publish_start_date)->format('Y-m-d\TH:i')
                                        : '',
                                ) }}"
                                placeholder="Publish Start Date" label="Publish Start Date" icon="bi-calendar-event"
                                autocomplete="off" :required="true" />

                        </div>


                        {{-- Publish End Date --}}
                        <div class="col-lg-4 mb-3">

                            <x-text-input name="publish_end_date" type="datetime-local"
                                value="{{ old(
                                    'publish_end_date',
                                    isset($document) && $document->publish_end_date
                                        ? \Carbon\Carbon::parse($document->publish_end_date)->format('Y-m-d\TH:i')
                                        : '',
                                ) }}"
                                placeholder="Publish End Date" label="Publish End Date" icon="bi-calendar-event"
                                autocomplete="off" :required="true" />

                        </div>


						
						 <div class="col-lg-12 mb-3">
							<label class="form-label">Description </label>
							<textarea name="description" class="form-control" rows="3" placeholder="Enter Description">{{ old('description',$document->description ?? '') }}</textarea>
							@error('description') <small class="text-danger">{{ $message }}</small> @enderror
						</div>
						
						
                        {{-- Status --}}
                        <div class="col-lg-4 mb-3">

                            <x-select-input name="status" icon="bi-check2-circle" label="Select Status"
                                selected="{{ old('status', $document->active ?? 1) }}" :options="statusoptions()"
                                placeholder="Select Status" :required="true" />

                        </div>


                        {{-- Buttons --}}
                        <div class="col-lg-12 my-4">

                            <div class="d-flex justify-content-end align-items-center gap-3">

                                <button type="submit" class="cms-btn rounded-2">

                                    {{ isset($document) ? 'Update' : 'Submit' }}

                                </button>

                                <button type="reset" class="border-btn rounded-2">

                                    Reset

                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection


@push('scripts')
    <script>
        (() => {

            'use strict';


            const $documentType = $('[name="document_type"]');
            const $pdfWrapper = $('#pdf-upload-wrapper');
            const $urlWrapper = $('#document-url-wrapper');
            const $file = $('[name="file"]');
            const $url = $('[name="document_url"]');

            function toggleDocumentFields() {
                const selectedType = String($documentType.val() || '');
                const isPdf = selectedType === '1';
                const isUrl = selectedType === '2';
                $pdfWrapper.toggle(isPdf);
                $urlWrapper.toggle(isUrl);
                $file.prop('required', isPdf && {{ isset($document) && $document->asset ? 'false' : 'true' }});
                $url.prop('required', isUrl);
                if (!isPdf) $file.val('').removeClass('is-invalid');
                if (!isUrl) $url.removeClass('is-invalid');
            }
            $documentType.on('change', toggleDocumentFields);
            toggleDocumentFields();

            $('#document-form').validate({

                ignore: [],

                errorClass: 'text-danger',

                errorElement: 'small',


                rules: {

                    title: {
                        required: true,
                        alphanumericSpecial: true
                    },

                    menu: {
                        required: true,
                        digits: true
                    },

                    document_type: { required: true, digits: true, range: [1, 2] },
                    file: {
                        required: function () {
                            return String($documentType.val()) === '1' &&
                                {{ isset($document) && $document->asset ? 'false' : 'true' }};
                        },
                        pdfAndSize: true
                    },
                    document_url: {
                        required: function () { return String($documentType.val()) === '2'; },
                        url: true,
                        maxlength: 555
                    },

                    publish_start_date: {
                        required: true
                    },

                    publish_end_date: {
                        required: true
                    },

                    status: {
                        required: true,
                        digits: true,
                        range: [0, 1]
                    }

                },


                messages: {

                    title: {
                        required: 'Enter Document Title'
                    },

                    menu: {
                        required: 'Please Choose Menu',
                        digits: 'Invalid Menu Chosen'
                    },

                    document_type: {
                        required: 'Please select Document Type',
                        range: 'Please select a valid Document Type'
                    },
                    file: { required: 'Please upload a PDF document' },
                    document_url: {
                        required: 'Please enter Document URL',
                        url: 'Please enter a valid URL'
                    },

                    publish_start_date: {
                        required: 'Please Select Publish Start Date'
                    },

                    publish_end_date: {
                        required: 'Please Select Publish End Date'
                    },

                    status: {
                        required: 'Please Choose Status',
                        digits: 'Status must be an integer',
                        range: 'Invalid status selected'
                    }

                },


                highlight: function(element) {

                    $(element).addClass('is-invalid');

                },


                unhighlight: function(element) {

                    $(element).removeClass('is-invalid');

                },


                errorPlacement: function(error, element) {

                    if (element.parent('.input-group').length) {

                        error.insertAfter(element.parent());

                    } else {

                        error.insertAfter(element);

                    }

                }

            });


            /*
            |--------------------------------------------------------------------------
            | End Date should not be before Start Date
            |--------------------------------------------------------------------------
            */

            $('#publish_start_date').on('change', function() {

                $('#publish_end_date').attr(
                    'min',
                    $(this).val()
                );

            });


            $('#publish_end_date').on('change', function() {

                let startDate = $('#publish_start_date').val();
                let endDate = $(this).val();

                if (startDate && endDate && endDate < startDate) {

                    Swal.fire({
                        title: 'Invalid Date!',
                        text: 'Publish End Date cannot be before Publish Start Date.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });

                    $(this).val('');

                }

            });


        })();
    </script>
@endpush
