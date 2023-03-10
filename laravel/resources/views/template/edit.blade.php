@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __("Edit the template.") }}

                    <form method="POST" action="{{ route('template.delete', ['template_id' => $template->id]) }}">
                        @method('delete')
                        @csrf
                        <button type="submit" class="btn btn-danger">Delete</a>
                    </form>
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

                    <form method="POST" action="{{ route('template.update', ['template_id' => $template->id]) }}" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                        @method('patch')
                        @csrf

                        <div class="mb-3">
                            <label for="validationAlias" class="form-label">Alias</label>
                            <input class="form-control" id="validationAlias" type="text" name="alias" value="{{ old('alias', $template->alias) }}" placeholder="e.g. Simple Template or Event Template" aria-describedby="aliasHelp" required>
                            <div id="aliasHelp" class="form-text">An alias for the template. (Only an information for you.)</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid alias.") }}</div>
                        </div>

                        <div class="mb-3">
                            <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                        </div>

                        <div class="mb-3">
                            <label for="validationFile" class="form-label">Template</label>
                            <input class="form-control" id="validationFile" type="file" accept="image/png, image/jpeg" name="file" value="{{ old('file') }}" aria-describedby="fileHelp">
                            <div id="fileHelp" class="form-text">The template file. (min. 468x60px (Width x Height), max. 1024x300px (Width x Height), PNG/JPEG, max. 5 MB)</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid file.") }}</div>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('templates') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                    <script>
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
                        })()
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
