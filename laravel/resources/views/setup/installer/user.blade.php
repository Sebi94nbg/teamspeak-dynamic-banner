@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Installer: User') }}</div>

                <div class="card-body">
                    <p>Let's create you a user!</p>

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

                    <form method="POST" action="{{ route('setup.installer.user.create') }}" class="row g-3 needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="validationName" class="form-label">Name</label>
                            <input class="form-control" id="validationName" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. MyNickname" aria-describedby="nameHelp" required>
                            <div id="nameHelp" class="form-text">Your users display name.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid name.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationEmail" class="form-label">Email</label>
                            <input class="form-control" id="validationEmail" type="email" name="email" value="{{ old('email') }}" placeholder="e.g. max@example.com" aria-describedby="emailHelp" required>
                            <div id="emailHelp" class="form-text">Your email address.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid email.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationPassword" class="form-label">Password</label>
                            <input class="form-control" id="validationPassword" type="password" minlength="8" name="password" placeholder="e.g. myVery!S3cretP4ssw0rd" aria-describedby="passwordHelp" required>
                            <div id="passwordHelp" class="form-text">Your login password. (minimum 8 characters)</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid password.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationPasswordConfirmation" class="form-label">Confirm Password</label>
                            <input class="form-control" id="validationPasswordConfirmation" type="password" minlength="8" name="password_confirmation" placeholder="e.g. myVery!S3cretP4ssw0rd" aria-describedby="passwordConfirmationHelp" required>
                            <div id="passwordConfirmationHelp" class="form-text">Repeat your password to confirm it.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please repeat your above password correct.") }}</div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Next</button>
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
