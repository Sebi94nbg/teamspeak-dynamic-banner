@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __("Define the templates for this banner.") }}
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

                    <table id="templates" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                            <tr>
                                <td>
                                    @if ($template->isEnabledForBannerId($banner->id))
                                    <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded opacity-100" style="max-height: 200px;" src="{{ asset($template->file_path_drawed_text.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                                    @else
                                    <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded opacity-50" style="max-height: 200px;" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                                    @endif
                                </td>
                                <td>
                                    @if ($template->isUsedByBannerId($banner->id))
                                        @if ($template->isEnabledForBannerId($banner->id))
                                            <form method="POST" action="{{ route('banner.template.disable') }}" novalidate>
                                                @method('patch')
                                                @csrf
                                                <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                                <input type="hidden" name="template_id" value="{{ $template->id }}">

                                                <button type="submit" class="btn btn-info"><i class="fa-solid fa-toggle-on"></i></button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('banner.template.enable') }}" novalidate>
                                                @method('patch')
                                                @csrf
                                                <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                                <input type="hidden" name="template_id" value="{{ $template->id }}">

                                                <button type="submit" class="btn btn-dark"><i class="fa-solid fa-toggle-off"></i></button>
                                            </form>
                                        @endif

                                        <a href="{{ route('banner.template.configuration.edit', ['banner_id' => $banner->id, 'template_id' => $template->id]) }}" class="btn btn-info">
                                            <i class="fa-solid fa-gear"></i>
                                        </a>

                                        <form method="POST" action="{{ route('banner.template.remove') }}" novalidate>
                                            @method('delete')
                                            @csrf
                                            <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                            <input type="hidden" name="template_id" value="{{ $template->id }}">

                                            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-minus"></i></button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('banner.template.add') }}" novalidate>
                                            @csrf
                                            <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                            <input type="hidden" name="template_id" value="{{ $template->id }}">

                                            <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

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

            <script type="module">
                $(document).ready(function () {
                    $('#templates').DataTable();
                });
            </script>
        </div>
    </div>
</div>
@endsection
