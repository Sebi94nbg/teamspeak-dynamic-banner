@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __("Edit the banner configuration.") }}

                    <a href="{{ route('banner.variables', ['banner_id' => $banner_template->banner->id]) }}" target="_blank" class="btn btn-info">{{ __("Available Variables") }}</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <label for="templateWithGrid" class="form-label">Template with Grid System</label>
                            <img id="templateWithGrid" class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" src="{{ asset($banner_template->template->file_path_drawed_grid_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
                        </div>

                        <div class="col-md-6">
                            <label for="renderedTemplate" class="form-label">Rendered Template Preview</label>
                            <img id="renderedTemplate" class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" src="{{ asset($banner_template->template->file_path_drawed_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
                        </div>
                    </div>

                    <hr>

                    <div class="col-md-12">
                        <p><b>About the Grid System</b></p>
                        <p>{{ __('With the help of the grid system, you are able to faster identify, which X-Y-Coordinates you need to put your text at the correct position.') }}</p>
                        <p>{{ __('Each horizontal and vertical line of the grid system represents 25px. In the left top corner, X and Y is 0px.') }}</p>
                    </div>

                    <hr>

                    <form id="template-configuration" method="POST" action="{{ route('banner.template.configuration.upsert', ['banner_id' => $banner_template->banner_id, 'template_id' => $banner_template->template_id]) }}" class="row g-3 needs-validation" novalidate>
                        @method('patch')
                        @csrf
                        <input type="hidden" name="banner_template_id" value="{{ $banner_template->id }}">

                        @if ($banner_template->configurations->count() > 0)
                            @foreach ($banner_template->configurations as $configuration)
                                <div id="config-row-{{ $configuration->id }}">
                                    @include('banner.template_configuration', ['template' => $banner_template->template, 'configuration' => $configuration])
                                    <hr>
                                </div>
                            @endforeach
                            @php unset($configuration) @endphp

                            <div id="new-config-row-1" class="d-none">
                                @include('banner.template_configuration', ['template' => $banner_template->template])
                                <hr>
                            </div>
                        @else
                            <div id="new-config-row-1">
                                @include('banner.template_configuration', ['template' => $banner_template->template])
                                <hr>
                            </div>
                        @endif

                        <div class="mb-3">
                            <a href="{{ route('banners') }}" class="btn btn-secondary">Cancel</a>
                            <button type="button" class="btn btn-success" id="add-config-row">Add row</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                    <script type="module">
                        // On page load, remove the `required` attributes for the hidden DIV to avoid
                        // issues by the form validation when it's not filled out.
                        $(document).ready(function(){
                            $('div.d-none > div > div > input').each(function() {
                                $(this).prop("required", false);
                            });
                        });

                        // Example starter JavaScript for disabling form submissions if there are invalid fields
                        (function () {
                        'use strict'

                        // Fetch all the forms we want to apply custom Bootstrap validation styles to
                        var forms = document.querySelectorAll('.needs-validation')

                        // Loop over them and prevent submission
                        Array.prototype.slice.call(forms)
                            .forEach(function (form) {
                            form.addEventListener('submit', function (event) {
                                if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                                }

                                form.classList.add('was-validated')
                            }, false)
                            })
                        })();

                        // Add additional configuration row, if requested by the user
                        $("#add-config-row").click(function () {
                            // Find the last DIV with the ID "new-config-row-<NUMBER>"
                            var $config_row = $('[id^="new-config-row"]:last');

                            if ($config_row.prop("class").match(/d-none/g)) {
                                // Add the `required` attribute to enforce the form validation
                                $('div.d-none > div > div > input').each(function() {
                                    $(this).prop("required", true);
                                });

                                // Unhide the row
                                $config_row.removeClass("d-none");
                            } else {
                                // Otherwise clone the DIV
                                // Get the NUMBER from the DIV and increment it by one
                                var next_number = parseInt($config_row.prop("id").match(/\d+/g), 10) + 1;

                                // Clone the last DIV and replace the ID to make it unique
                                var $clone = $config_row.clone().prop('id', 'new-config-row-' + next_number);

                                // Insert the cloned DIV at the end
                                $config_row.after($clone);
                            }
                        });

                        // Remove existing, but not yet saved configuration row, if requested by the user
                        $(document).on('click', '#remove-config-row', function () {
                            $(this).closest('[id^="new-config-row"]').remove();
                        })
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
