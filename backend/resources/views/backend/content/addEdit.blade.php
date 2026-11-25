@extends('backend.layouts.app')

@section('title', isset($content) ? 'Edit Content' : 'Create Content')

@section('content')
<div class="container-fluid">
    <div class="row mb-3 align-items-center">
        <div class="col-12 position-relative text-center">
            <div class="d-inline-block">
                <h1 class="mb-1 fw-bold">{{ isset($content) ? 'Edit Content' : 'Create Content' }}</h1>
                <div class="d-flex justify-content-center">
                    <div class="border-bottom border-3 border-primary rounded-pill w-75"></div>
                </div>
            </div>
            <a href="{{ route('content.list') }}" class="btn btn-outline-secondary rounded-2 position-absolute top-50 end-0 translate-middle-y">
                <i class="bi bi-caret-left-fill" aria-hidden="true"></i> Back
            </a>
        </div>
    </div>

    <div class="row justify-content-between py-2">
        <div class="col-xl-12 col-12 mb-md-0 mb-3">
            <form id="content-form" action="{{ route('content.saveUpdate') }}" method="post" class="form-box">
                @csrf

                @if (isset($content))
                    <x-text-input name="id" type="hidden" value="{{ encrypt($content->id) }}" />
                @endif

                <div class="row">

                    {{-- Menu --}}
                    <div class="col-lg-4 mb-3">
                        <x-select-input
                            name="menu"
                            icon="bi-list"
                            label="Select Menu"
                            selected="{{ old('menu', $content->menu_id ?? '') }}"
                            :options="$menus->mapWithKeys(function ($menu) {
                                $title = $menu->title;
                                if ($menu->parent) {
                                    $title .= ' (' . $menu->parent->title . ')';
                                }
                                return [$menu->id => $title];
                            })->toArray()"
                            placeholder="Select Menu"
                            :required="true"
                        />
                    </div>


                     {{-- Title --}}
                     <div class="col-lg-4 mb-3">
                            <x-text-input name="title" type="text" value="{{ old('title', $content->title ?? '') }}"
                                placeholder="Title" label="Title" icon="bi-card-checklist" autocomplete="off"
                                :required="true" />
                        </div>

                    {{-- Status --}}
                    <div class="col-lg-4 mb-3">
                        <x-select-input
                            name="status"
                            icon="bi-check2-circle"
                            label="Select Status"
                            selected="{{ old('status', $content->is_active ?? 1) }}"
                            :options="statusoptions()"
                            placeholder="Select Status"
                            :required="true"
                        />
                    </div>

                    {{-- Content --}}
                    <div class="col-lg-12 mb-3">
                        <label for="cktext" class="form-label">
                            <i class="bi bi-file-text me-1"></i>
                            Content <span class="text-danger">*</span>
                        </label>

                        <textarea name="cktext" id="cktext" class="form-control">{!! old('cktext', $content->content ?? '') !!}</textarea>

                        @error('cktext')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="col-lg-12 my-4">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <button type="submit" class="cms-btn rounded-2">
                                {{ isset($content) ? 'Update' : 'Submit' }}
                            </button>
                            <button type="reset" class="border-btn rounded-2">Reset</button>
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
        min-height: 300px;
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

    /*
    |--------------------------------------------------------------------------
    | Remove Extra Spaces & Line Breaks
    |--------------------------------------------------------------------------
    */
    function cleanEditorContent(content) {
        if (!content) {
            return '';
        }

        return content
            .replace(/\r\n|\r|\n|\t/g, ' ')
            .replace(/>\s+</g, '><')
            .replace(/\s{2,}/g, ' ')
            .trim();
    }

    /*
    |--------------------------------------------------------------------------
    | CKEditor
    |--------------------------------------------------------------------------
    */
    ClassicEditor.create(document.querySelector('#cktext'), {
        licenseKey: 'GPL',

        plugins: [
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
        ],

        toolbar: [
            'sourceEditing',
            '|',
            'undo',
            'redo',
            '|',
            'heading',
            '|',
            'fontFamily',
            'fontSize',
            'fontColor',
            'fontBackgroundColor',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            '|',
            'subscript',
            'superscript',
            'code',
            '|',
            'removeFormat',
            '|',
            'link',
            '|',
            'bulletedList',
            'numberedList',
            'todoList',
            '|',
            'outdent',
            'indent',
            '|',
            'alignment',
            '|',
            'blockQuote',
            '|',
            'codeBlock',
            '|',
            'horizontalLine',
            '|',
            'specialCharacters',
            '|',
            'insertTable',
            '|',
            'mediaEmbed',
            '|',
            'findAndReplace'
        ],

        heading: {
            options: [
                {
                    model: 'paragraph',
                    title: 'Paragraph',
                    class: 'ck-heading_paragraph'
                },
                {
                    model: 'heading1',
                    view: 'h1',
                    title: 'Heading 1',
                    class: 'ck-heading_heading1'
                },
                {
                    model: 'heading2',
                    view: 'h2',
                    title: 'Heading 2',
                    class: 'ck-heading_heading2'
                },
                {
                    model: 'heading3',
                    view: 'h3',
                    title: 'Heading 3',
                    class: 'ck-heading_heading3'
                },
                {
                    model: 'heading4',
                    view: 'h4',
                    title: 'Heading 4',
                    class: 'ck-heading_heading4'
                },
                {
                    model: 'heading5',
                    view: 'h5',
                    title: 'Heading 5',
                    class: 'ck-heading_heading5'
                },
                {
                    model: 'heading6',
                    view: 'h6',
                    title: 'Heading 6',
                    class: 'ck-heading_heading6'
                }
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
            options: [
                8, 9, 10, 11, 12, 13, 14, 15, 16,
                18, 20, 22, 24, 26, 28, 30, 32,
                36, 40, 48, 60, 72
            ],
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
                {
                    model: 'yellowMarker',
                    class: 'marker-yellow',
                    title: 'Yellow Marker',
                    color: '#FFFF00',
                    type: 'marker'
                },
                {
                    model: 'greenMarker',
                    class: 'marker-green',
                    title: 'Green Marker',
                    color: '#63E6BE',
                    type: 'marker'
                },
                {
                    model: 'pinkMarker',
                    class: 'marker-pink',
                    title: 'Pink Marker',
                    color: '#FCC2D7',
                    type: 'marker'
                },
                {
                    model: 'blueMarker',
                    class: 'marker-blue',
                    title: 'Blue Marker',
                    color: '#A5D8FF',
                    type: 'marker'
                },
                {
                    model: 'redPen',
                    class: 'pen-red',
                    title: 'Red Pen',
                    color: '#FF0000',
                    type: 'pen'
                }
            ]
        },

        link: {
            decorators: {
                openInNewTab: {
                    mode: 'manual',
                    label: 'Open in a new tab',
                    attributes: {
                        target: '_blank',
                        rel: 'noopener noreferrer'
                    }
                }
            }
        },

        list: {
            properties: {
                styles: true,
                startIndex: true,
                reversed: true
            }
        },

        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells',
                'tableProperties',
                'tableCellProperties'
            ]
        },

        image: {
            toolbar: [
                'imageTextAlternative',
                'toggleImageCaption',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side'
            ]
        }
    })
    .then(newEditor => {
        editor = newEditor;
        console.log('CKEditor 5 initialized successfully.');
    })
    .catch(error => {
        console.error('CKEditor 5 initialization error:', error);
    });

    /*
    |--------------------------------------------------------------------------
    | jQuery Validation
    |--------------------------------------------------------------------------
    */
    $('#content-form').validate({
        ignore: [],
        errorClass: 'text-danger',
        errorElement: 'small',

        rules: {
            menu: {
                required: true
            },

            status: {
                required: true
            },

            cktext: {
                required: function() {
                    if (editor) {
                        $('#cktext').val(editor.getData());
                    }

                    return $('#cktext').val().trim().length > 0;
                },

                minlength: 10
            }
        },

        messages: {
            menu: {
                required: 'Please select Menu'
            },

            status: {
                required: 'Please select Status'
            },

            cktext: {
                required: 'Please enter Content',
                minlength: 'Content must be at least 10 characters'
            }
        },

        errorPlacement: function(error, element) {
            if (element.attr('name') === 'cktext') {
                error.insertAfter(element.next('.ck-editor'));
            } else {
                error.insertAfter(element);
            }
        },

        highlight: function(element) {
            $(element).addClass('is-invalid');
        },

        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        },

        /*
        |--------------------------------------------------------------------------
        | Submit
        |--------------------------------------------------------------------------
        */
        submitHandler: function(form) {
            if (editor) {
                let content = editor.getData();

                // Remove extra spaces and line breaks
                content = cleanEditorContent(content);

                $('#cktext').val(content);
            }

            form.submit();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | CKEditor Content Validation
    |--------------------------------------------------------------------------
    */
    $(document).on('input', '.ck-editor__editable', function() {
        if (editor) {
            $('#cktext').val(editor.getData());
            $('#content-form').validate().element('#cktext');
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Menu Duplicate Content Check
    |--------------------------------------------------------------------------
    */
    $('#menu').on('change', function() {
        let menuID = $(this).val();

        if (!menuID) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('content.check') }}",
            data: {
                _token: "{{ csrf_token() }}",
                menu_id: menuID,
                id: "{{ isset($content) ? encrypt($content->id) : '' }}"
            },

            success: function(response) {
                if (!response.success) {
                    Swal.fire({
                        title: 'Warning!',
                        text: response.message,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });

                    $('#menu').val('');
                }
            },

            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.message) {
                    Swal.fire({
                        title: 'Warning!',
                        text: xhr.responseJSON.message,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });

                    $('#menu').val('');
                }
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */
    $('#content-form').on('reset', function() {
        setTimeout(function() {
            if (editor) {
                editor.setData(@json($content->content ?? ''));
            }
        }, 100);
    });

})();
</script>
@endpush
