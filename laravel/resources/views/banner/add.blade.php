@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __("Add a banner.") }}</div>

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

                    <form method="POST" action="{{ route('banner.save') }}" class="row g-3 needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="validationName" class="form-label">Name</label>
                            <input class="form-control" id="validationName" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. My Banner or Games Banner" aria-describedby="nameHelp" required>
                            <div id="nameHelp" class="form-text">A name for the banner configuration as identifier for you.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid alias.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationInstanceId" class="form-label">Instance</label>
                            <select class="form-select" name="instance_id" aria-describedby="instanceIdHelp" required>
                                @foreach ($instance_list as $instance)
                                @if (old('instance_id', $instance->instance_id) == $instance->id) "selected"
                                <option value="{{ $instance->id }}" selected>{{ $instance->virtualserver_name }} ({{ $instance->host }}:{{ $instance->voice_port }})</option>
                                @else
                                <option value="{{ $instance->id }}">{{ $instance->virtualserver_name }} ({{ $instance->host }}:{{ $instance->voice_port }})</option>
                                @endif
                                @endforeach
                            </select>
                            <div id="instanceIdHelp" class="form-text">Please select your instance as data source for the banner.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid instance (ID).") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationRandomRotation" class="form-check-label">Enable random template rotation</label>
                            @if (old('random_rotation'))
                            <input class="form-check-input" id="validationRandomRotation" type="checkbox" name="random_rotation" aria-describedby="randomRotationHelp" checked>
                            @else
                            <input class="form-check-input" id="validationRandomRotation" type="checkbox" name="random_rotation" aria-describedby="randomRotationHelp">
                            @endif
                            <div id="randomRotationHelp" class="form-text">When enabled, every client will see a different random template. If disabled, the same template will be shown to all clients.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("You can only enable or disable this checkbox.") }}</div>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('banners') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save</button>
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
