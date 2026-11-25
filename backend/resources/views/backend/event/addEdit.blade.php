@extends('backend.layouts.app')

@section('title', isset($event) ? 'Edit Event' : 'Create Event')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12 position-relative text-center">
                <div class="d-inline-block">
                    <h1 class="mb-1 fw-bold">{{ isset($event) ? 'Edit Event' : 'Create Event' }}</h1>
                    <div class="d-flex justify-content-center">
                        <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                    </div>
                </div>
                <a href="{{ route('event.list') }}"
                    class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y"><i
                        class="bi bi-caret-left-fill"></i> Back</a>
            </div>
        </div>

        <div class="row justify-content-between py-2">
            <div class="col-xl-12 col-12 mb-md-0 mb-3">
                <form id="event-form" action="{{ route('event.saveUpdate') }}" method="post" class="form-box">
                    @csrf
                    @if (isset($event))
                        <x-text-input name="id" type="hidden" value="{{ $event->id }}" />
                    @endif
                    <input type="hidden" name="lang_id" value="{{ old('lang_id', $event->lang_id ?? 1) }}">

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="row">
					
                        <div class="col-lg-6 mb-3">
                            <x-text-input name="title" type="text" value="{{ old('title', $event->title ?? '') }}"
                                placeholder="Enter Event Title" label="Event Title" icon="bi-calendar-event"
                                autocomplete="off" :required="true" />
                        </div>
						
                        <div class="col-lg-6 mb-3">
                            <x-text-input name="venue" type="text" value="{{ old('venue', $event->venue ?? '') }}"
                                placeholder="Enter Venue" label="Venue" icon="bi-geo-alt" autocomplete="off"
                                :required="true" />
                        </div>

                       

                        <div class="col-lg-3 mb-3">
                            <x-text-input name="event_date" type="date"
                                value="{{ old('event_date', $event?->event_date?->format('Y-m-d') ?? '') }}"
                                placeholder="Select Event Date" label="Event Date" icon="bi-calendar-date"
                                autocomplete="off" :required="true" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="event_time" type="time"
                                value="{{ old('event_time', $event->event_time ?? '') }}" placeholder="Select Event Time"
                                label="Event Time" icon="bi-clock" autocomplete="off" :required="true" />
                        </div>
						
						<div class="col-lg-3 mb-3">
                            <x-text-input name="event_end_date" type="date"
                                value="{{ old('event_end_date', $event?->event_end_date?->format('Y-m-d') ?? '') }}"
                                placeholder="Select Event End Date" label="Event End Date" icon="bi-calendar-date"
                                autocomplete="off" :required="true" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="event_end_time" type="time"
                                value="{{ old('event_end_time', $event->event_end_time ?? '') }}" placeholder="Select Event End Time"
                                label="Event End Time" icon="bi-clock" autocomplete="off" :required="true" />
                            @error('event_end_time')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
						
                        <div class="col-lg-6 mb-3">
                            <x-text-input name="organised_by" type="text"
                                value="{{ old('organised_by', $event->organised_by ?? '') }}"
                                placeholder="Enter Organiser Name" label="Organised By" icon="bi-person-badge"
                                autocomplete="off" :required="true" />
                        </div>
						
						<div class="col-lg-6 mb-3">
                            <x-text-input name="email" type="email" value="{{ old('email', $event->email ?? '') }}"
                                placeholder="Enter Email Address" label="Email Address" icon="bi-envelope"
                                autocomplete="off" />
                        </div>
						
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="mobile" type="text"
                                value="{{ old('mobile', $event->mobile ?? '') }}" placeholder="Enter Mobile Number"
                                label="Mobile Number" icon="bi-phone" autocomplete="off" />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <x-text-input name="other_mobile" type="text"
                                value="{{ old('other_mobile', $event->other_mobile ?? '') }}"
                                placeholder="Enter Alternate Mobile" label="Other Mobile" icon="bi-phone-vibrate"
                                autocomplete="off" />
                        </div>
                        
						
				                        <div class="col-lg-3 mb-3">
                            <label for="country_id" class="form-label">
                                <i class="bi bi-globe me-1"></i> Country <span class="text-danger">*</span>
                            </label>
                            <select
                                class="form-select @error('country_id') is-invalid @enderror"
                                id="country_id"
                                name="{{ (int) $authRoleId === 8 ? 'country_id_display' : 'country_id' }}"
                                {{ (int) $authRoleId === 8 ? 'disabled' : '' }}
                                required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $id => $name)
                                    <option value="{{ $id }}" @selected((string) old('country_id', $countryId) === (string) $id)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ((int) $authRoleId === 8)
                                <input type="hidden" name="country_id" value="{{ $countryId }}">
                            @endif
                            @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ((int) $authRoleId === 8)
                                <!--small class="text-muted">Country is fixed for your account.</small-->
                            @endif
                        </div>

                        <div class="col-lg-3 mb-3">
                            <label for="region_id" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i> Region / State / Province
                            </label>
                            <select
                                class="form-select @error('region_id') is-invalid @enderror"
                                id="region_id"
                                name="region_id">
                                <option value="">Select Region / State / Province</option>
                                @foreach ($regions as $id => $name)
                                    <option value="{{ $id }}" @selected((string) old('region_id', $event->region_id ?? '') === (string) $id)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
						
						
						 <div class="col-lg-3 mb-3">
                            <x-text-input name="city" type="text" value="{{ old('city', $event->city ?? '') }}"
                                placeholder="City / Town" label="City / Town" icon="bi-geo" autocomplete="off"
                                :required="true" />
                        </div>


                        <div class="col-lg-3 mb-3">
                            <x-text-input name="pincode" type="text" value="{{ old('pincode', $event->pincode ?? '') }}"
                                placeholder="Postal / ZIP Code" label="Postal / ZIP Code" icon="bi-pin-map" autocomplete="off"
                                :required="false" />
                        </div>

                        <div class="col-lg-6 mb-3">
                            <x-text-input name="address" type="text" value="{{ old('address', $event->address ?? '') }}"
                                placeholder="Enter Address" label="Address" icon="bi-house" autocomplete="off"
                                :required="false" />
                        </div>
						
						
                        <!--div class="col-lg-12 mb-3">
                            <label for="event_description" class="form-label"><i class="bi bi-file-text me-1"></i> Event
                                Description <span class="text-danger">*</span></label>
                            <textarea name="event_description" id="event_description" class="form-control" rows="3"
                                placeholder="Enter event description" required>{{ old('event_description', $event->event_description ?? '') }}</textarea>
                            @error('event_description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div-->
						
						
						{{-- Content --}}
						<div class="col-lg-12 mb-3">
							<label for="cktext" class="form-label">
								<i class="bi bi-file-text me-1"></i>
								Content <span class="text-danger">*</span>
							</label>

							<textarea name="event_description" id="cktext" class="form-control">{!! old('event_description', $event->event_description ?? '') !!}</textarea>

							@error('event_description')
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
						
					

                        @if (isset($event))
                            <div class="col-lg-6 mb-3">
                                <x-select-input name="is_active" icon="bi-check2-circle" label="Select Status"
                                    selected="{{ old('is_active', (int) $event->is_active) }}" :options="statusoptions()"
                                    placeholder="Select Status" :required="true" />
                            </div>
                        @endif

                        <div class="col-12 my-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <button type="submit" class="cms-btn rounded-2"><i class="bi bi-check-circle me-1"></i>
                                    {{ isset($event) ? 'Update Event' : 'Submit Event' }}</button>
                                <button type="reset" class="border-btn rounded-2"><i
                                        class="bi bi-arrow-counterclockwise me-1"></i> Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/v1/ckeditor5-48.5.0/ckeditor5/ckeditor5.css') }}">
<style>
    .ck-editor {
        width: 100%;
    }

    .ck-editor__editable,
    .ck.ck-editor__main > .ck-editor__editable,
    .ck-content {
        min-height: 200px;
    }

    .ck.ck-toolbar {
        border-radius: 6px 6px 0 0;
    }

    .ck.ck-editor__main > .ck-editor__editable {
        border-radius: 0 0 6px 6px;
    }

    .ck-content img {
        max-width: 100%;
        height: auto;
    }

    .ck-content table {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/v1/ckeditor5-48.5.0/ckeditor5/ckeditor5.umd.js') }}"></script>
<script>
(() => {
    'use strict';

    const {
        ClassicEditor,
        Essentials,
        Paragraph,
        Heading,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Subscript,
        Superscript,
        Code,
        RemoveFormat,
        Link,
        AutoLink,
        List,
        ListProperties,
        TodoList,
        Indent,
        IndentBlock,
        Alignment,
        BlockQuote,
        Font,
        Highlight,
        HorizontalLine,
        SpecialCharacters,
        SpecialCharactersEssentials,
        Table,
        TableToolbar,
        MediaEmbed,
        SourceEditing,
        FindAndReplace,
        CodeBlock,
        PasteFromOffice,
        Image,
        ImageToolbar,
        ImageCaption,
        ImageStyle,
        ImageResize
    } = CKEDITOR;

    let editor = null;

    ClassicEditor.create(document.querySelector('#cktext'), {
        licenseKey: 'GPL',
        plugins: [
            Essentials, Paragraph, Heading, Bold, Italic, Underline, Strikethrough,
            Subscript, Superscript, Code, RemoveFormat, Link, AutoLink, List,
            ListProperties, TodoList, Indent, IndentBlock, Alignment, BlockQuote,
            Font, Highlight, HorizontalLine, SpecialCharacters,
            SpecialCharactersEssentials, Table, TableToolbar, MediaEmbed,
            SourceEditing, FindAndReplace, CodeBlock, PasteFromOffice, Image,
            ImageToolbar, ImageCaption, ImageStyle, ImageResize
        ],
        toolbar: [
            'sourceEditing', '|', 'undo', 'redo', '|', 'heading', '|',
            'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'subscript', 'superscript', 'code', '|', 'removeFormat', '|',
            'link', '|', 'bulletedList', 'numberedList', 'todoList', '|',
            'outdent', 'indent', '|', 'alignment', '|', 'blockQuote', '|',
            'codeBlock', '|', 'horizontalLine', '|', 'specialCharacters', '|',
            'insertTable', '|', 'mediaEmbed', '|', 'findAndReplace'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif'
            ],
            supportAllValues: true
        },
        fontSize: {
            options: [8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 20, 22, 24, 26, 28, 30, 32, 36, 40, 48, 60, 72],
            supportAllValues: true
        },
        fontColor: {
            colors: [
                { color: '#000000', label: 'Black' },
                { color: '#FFFFFF', label: 'White' },
                { color: '#FF0000', label: 'Red' },
                { color: '#008000', label: 'Green' },
                { color: '#0000FF', label: 'Blue' },
                { color: '#FFFF00', label: 'Yellow' },
                { color: '#FFA500', label: 'Orange' },
                { color: '#800080', label: 'Purple' },
                { color: '#808080', label: 'Gray' },
                { color: '#A52A2A', label: 'Brown' }
            ],
            columns: 5
        },
        fontBackgroundColor: {
            colors: [
                { color: '#FFFF00', label: 'Yellow' },
                { color: '#00FF00', label: 'Green' },
                { color: '#00FFFF', label: 'Cyan' },
                { color: '#FF00FF', label: 'Magenta' },
                { color: '#FFFFFF', label: 'White' },
                { color: '#000000', label: 'Black' },
                { color: '#FF0000', label: 'Red' },
                { color: '#0000FF', label: 'Blue' }
            ],
            columns: 4
        },
        highlight: {
            options: [
                { model: 'yellowMarker', class: 'marker-yellow', title: 'Yellow Marker', color: '#FFFF00', type: 'marker' },
                { model: 'greenMarker', class: 'marker-green', title: 'Green Marker', color: '#63E6BE', type: 'marker' },
                { model: 'pinkMarker', class: 'marker-pink', title: 'Pink Marker', color: '#FCC2D7', type: 'marker' },
                { model: 'blueMarker', class: 'marker-blue', title: 'Blue Marker', color: '#A5D8FF', type: 'marker' },
                { model: 'redPen', class: 'pen-red', title: 'Red Pen', color: '#FF0000', type: 'pen' }
            ]
        },
        link: {
            decorators: {
                openInNewTab: {
                    mode: 'manual',
                    label: 'Open in a new tab',
                    attributes: { target: '_blank', rel: 'noopener noreferrer' }
                }
            }
        },
        list: {
            properties: { styles: true, startIndex: true, reversed: true }
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
        },
        image: {
            toolbar: [
                'imageTextAlternative', 'toggleImageCaption',
                'imageStyle:inline', 'imageStyle:block', 'imageStyle:side'
            ]
        }
    })
    .then(newEditor => {
        editor = newEditor;
    })
    .catch(error => {
        console.error('CKEditor 5 initialization error:', error);
    });

    // Sync CKEditor content into event_description before form submission.
    const eventForm = document.querySelector('#event-form');
    if (eventForm) {
        eventForm.addEventListener('submit', function () {
            if (editor) {
                document.querySelector('#cktext').value = editor.getData();
            }
        });

        eventForm.addEventListener('reset', function () {
            setTimeout(function () {
                if (editor) {
                    editor.setData(@json(old('event_description', $event->event_description ?? '')));
                }
            }, 100);
        });
    }

    // Keep the end date/time from being earlier than the start date/time.
    const startDateInput = document.querySelector('[name="event_date"]');
    const startTimeInput = document.querySelector('[name="event_time"]');
    const endDateInput = document.querySelector('[name="event_end_date"]');
    const endTimeInput = document.querySelector('[name="event_end_time"]');
    const eventFormForDateValidation = document.querySelector('#event-form');

    function validateEventDateTime() {
        if (!startDateInput || !startTimeInput || !endDateInput || !endTimeInput) return;

        endDateInput.min = startDateInput.value || '';
        endTimeInput.min = (endDateInput.value && endDateInput.value === startDateInput.value)
            ? (startTimeInput.value || '')
            : '';

        endTimeInput.setCustomValidity('');

        if (startDateInput.value && startTimeInput.value &&
            endDateInput.value && endTimeInput.value) {
            const startDateTime = startDateInput.value + 'T' + startTimeInput.value;
            const endDateTime = endDateInput.value + 'T' + endTimeInput.value;

            if (endDateTime <= startDateTime) {
                endTimeInput.setCustomValidity(
                    'Event end date and time must be later than the event start date and time.'
                );
            }
        }
    }

    [startDateInput, startTimeInput, endDateInput, endTimeInput].forEach(function (field) {
        if (field) {
            field.addEventListener('change', validateEventDateTime);
            field.addEventListener('input', validateEventDateTime);
        }
    });

    if (eventFormForDateValidation) {
        eventFormForDateValidation.addEventListener('submit', function (event) {
            validateEventDateTime();
            if (!eventFormForDateValidation.checkValidity()) {
                event.preventDefault();
                eventFormForDateValidation.reportValidity();
            }
        });
    }

    validateEventDateTime();

    // Existing country/region dropdown behavior.
    $(function () {
        const country = $('#country_id');
        const region = $('#region_id');
        const regionsUrl = @json(route('event.states'));
        const selectedRegionId = @json(old('region_id', $event->region_id ?? ''));

        function loadRegions(countryId, selectedId = '') {
            region.empty().append(new Option(
                countryId ? 'Loading regions...' : 'Select Region / State / Province',
                ''
            )).prop('disabled', true);

            if (!countryId) {
                region.empty().append(new Option('Select Region / State / Province', ''));
                return;
            }

            $.get(regionsUrl, { country_id: countryId })
                .done(function (rows) {
                    region.empty().append(new Option('Select Region / State / Province', ''));

                    $.each(rows, function (_, row) {
                        const option = new Option(row.name, row.id);
                        if (String(row.id) === String(selectedId)) {
                            option.selected = true;
                        }
                        region.append(option);
                    });

                    region.prop('disabled', false);
                })
                .fail(function () {
                    region.empty()
                        .append(new Option('Unable to load regions', ''))
                        .prop('disabled', true);
                });
        }

        country.on('change', function () {
            loadRegions($(this).val());
        });

        const initialCountryId = @json(old('country_id', $countryId));
        if (initialCountryId) {
            loadRegions(initialCountryId, selectedRegionId);
        }
    });
})();
</script>
@endpush
