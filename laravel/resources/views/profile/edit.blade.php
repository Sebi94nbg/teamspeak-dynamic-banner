@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __("Your Profile") }}</div>

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

                    <form method="POST" action="{{ route('profile.update') }}" class="row g-3 needs-validation" novalidate>
                        @method('patch')
                        @csrf

                        <div class="mb-3">
                            <label for="validationName" class="form-label">Name</label>
                            <input class="form-control" id="validationName" type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="e.g. MyNickname" aria-describedby="nameHelp" required>
                            <div id="nameHelp" class="form-text">Your users display name.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid name.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationEmail" class="form-label">Email</label>
                            <input class="form-control" id="validationEmail" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="e.g. max@example.com" aria-describedby="emailHelp" required>
                            <div id="emailHelp" class="form-text">Your email address.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid email.") }}</div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('profile.update.password') }}" class="row g-3 needs-validation" novalidate>
                        @method('patch')
                        @csrf

                        <div class="mb-3">
                            <label for="validationCurrentPassword" class="form-label">Current Password</label>
                            <input class="form-control" id="validationCurrentPassword" type="password" minlength="8" name="current_password" placeholder="e.g. myOldPassword" aria-describedby="currentPasswordHelp" required>
                            <div id="currentPasswordHelp" class="form-text">Your current login password.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid current password.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationNewPassword" class="form-label">New Password</label>
                            <input class="form-control" id="validationNewPassword" type="password" minlength="8" name="password" placeholder="e.g. myNewPassword" aria-describedby="newPasswordHelp" required>
                            <div id="newPasswordHelp" class="form-text">Your new login password. (minimum 8 characters)</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid password.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationNewPasswordConfirmation" class="form-label">Confirm new Password</label>
                            <input class="form-control" id="validationNewPasswordConfirmation" type="password" minlength="8" name="password_confirmation" placeholder="e.g. myNewPassword" aria-describedby="newPasswordConfirmationHelp" required>
                            <div id="newPasswordConfirmationHelp" class="form-text">Repeat your new password to confirm it.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please repeat your above password correct.") }}</div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
@endsection
