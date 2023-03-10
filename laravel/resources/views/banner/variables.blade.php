@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __("Available Variables.") }}
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

                    @if (! is_null($redis_connection_error))
                        <div class="alert alert-danger" role="alert">
                            {{ __('Your cache server seems to have an issue:') }}
                            <b>{{ $redis_connection_error }}</b>
                        </div>
                    @endif

                    @if (count($variables_and_values) == 0)
                        <div class="alert alert-warning" role="alert">
                            {{ __('It seems like as you do not have any data yet.') }}
                            {{ __('Please ensure, that your instance is started properly and that your cache server is reachable.') }}
                        </div>
                    @endif

                    <table id="variables" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Variable</th>
                                <th>Current Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($variables_and_values as $key => $value)
                            <tr>
                                <td><code>%{{ $key }}%</code></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $('#variables').DataTable();
                });
            </script>
        </div>
    </div>
</div>
@endsection
