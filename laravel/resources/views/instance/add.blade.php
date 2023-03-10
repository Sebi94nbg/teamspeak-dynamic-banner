@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __("Add an instance.") }}</div>

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

                    <form method="POST" action="{{ route('instance.save') }}" class="row g-3 needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="validationHost" class="form-label">Host</label>
                            <input class="form-control" id="validationHost" type="text" name="host" value="{{ old('host') }}" placeholder="e.g. my.teamspeak.local or 192.168.2.87" aria-describedby="hostHelp" required>
                            <div id="hostHelp" class="form-text">The hostname, domain or IP address of your TeamSpeak server.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid domain or IP address.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationVoicePort" class="form-label">Voice Port</label>
                            <input class="form-control" id="validationVoicePort" type="number" name="voice_port" min="1" max="65535" step="1" value="{{ old('voice_port', 9987) }}" aria-describedby="voicePortHelp" required>
                            <div id="voicePortHelp" class="form-text">The Voice port of the TeamSpeak server to connect at.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid port.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationServerQueryPort" class="form-label">ServerQuery Port</label>
                            <input class="form-control" id="validationServerQueryPort" type="number" name="serverquery_port" min="1" step="1" max="65535" value="{{ old('serverquery_port', 10022) }}" aria-describedby="serverQueryPortHelp" required>
                            <div id="serverQueryPortHelp" class="form-text">The ServerQuery port of the TeamSpeak server for executing commands and gathering data.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid port.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationServerQueryUsername" class="form-label">ServerQuery Username</label>
                            <input class="form-control" id="validationServerQueryUsername" type="text" name="serverquery_username" value="{{ old('serverquery_username') }}" placeholder="e.g. serveradmin" aria-describedby="serverQueryUsernameHelp" required>
                            <div id="serverQueryUsernameHelp" class="form-text">The ServerQuery username for the authentication.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid username.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationServerQueryPassword" class="form-label">ServerQuery Password</label>
                            <input class="form-control" id="validationServerQueryPassword" type="password" name="serverquery_password" value="{{ old('serverquery_password') }}" aria-describedby="serverQueryPasswordHelp" required>
                            <div id="serverQueryPasswordHelp" class="form-text">The password of the previous defined ServerQuery username.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid password.") }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="validationClientNickname" class="form-label">Client Nickname</label>
                            <input class="form-control" id="validationClientNickname" type="text" maxlength="30" name="client_nickname" value="{{ old('client_nickname') }}" aria-describedby="clientNicknameHelp" required>
                            <div id="clientNicknameHelp" class="form-text">How this client should be named on your TeamSpeak server. (Maximum length: 30 characters)</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid nickname.") }}</div>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('instances') }}" class="btn btn-secondary">Cancel</a>
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
