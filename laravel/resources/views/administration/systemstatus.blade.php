@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __("System Status") }}</div>

                <div class="card-body">
                    <p>{{ __("This page provides you some status information to ensure, that your application is working as expected and does not have any issues.") }}</p>

                    @if ($system_status_warning_count + $system_status_danger_count == 0)
                        <div class="alert alert-success" role="alert">
                            There are no problems with your installation.
                        </div>
                    @endif

                    @if ($system_status_warning_count > 0)
                        <div class="alert alert-warning" role="alert">
                            Your installation has {{ $system_status_warning_count }} {{ \Illuminate\Support\Str::plural("warning", $system_status_warning_count) }}, which you might want to fix for the best software experience.
                        </div>
                    @endif

                    @if ($system_status_danger_count > 0)
                        <div class="alert alert-danger" role="alert">
                            Your installation has {{ $system_status_danger_count }} critical {{ \Illuminate\Support\Str::plural("issue", $system_status_danger_count) }}, which you need to fix that everything works properly.
                        </div>
                    @endif

                    <table id="system-status" class="table table-striped" style="width:100%">
                        <tbody>
                            @php
                                $previous_section_name = null;                                
                            @endphp

                            @foreach ($system_status as $section => $section_checks)
                                <tr>
                                    <td colspan="3" class="divider">
                                        <hr />
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="text-center">
                                        <b>{{ strtoupper($section) }}</b>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="divider">
                                        <hr />
                                    </td>
                                </tr>

                                @php
                                    $previous_nested_check_key = null;
                                @endphp

                                @foreach ($section_checks as $nested_checks)
                                    @foreach ($nested_checks as $subcheck)
                                        @if (isset($subcheck->current_value))
                                            <tr>
                                                <td>
                                                    {{ (isset($subcheck->name)) ? $subcheck->name : $subcheck }}
                                                    @if (isset($subcheck->required_value))
                                                    <code>{{ $subcheck->required_value }}</code>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (strtoupper($subcheck->severity) == 'INFO')
                                                    <i class="fa-solid fa-circle-info text-info"></i>
                                                    @elseif (strtoupper($subcheck->severity) == 'SUCCESS')
                                                    <i class="fa-solid fa-circle-check text-success"></i>
                                                    @elseif (strtoupper($subcheck->severity) == 'WARNING')
                                                    <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                                    @elseif (strtoupper($subcheck->severity) == 'DANGER')
                                                    <i class="fa-solid fa-circle-xmark text-danger"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ (isset($subcheck->current_value)) ? $subcheck->current_value : "NO CURRENT VALUE" }}
                                                </td>
                                            </tr>
                                        @else
                                            @if ($nested_checks->name == $previous_nested_check_key)
                                                @continue
                                            @endif

                                            @php
                                                $previous_nested_check_key = $nested_checks->name;
                                            @endphp

                                            <tr>
                                                <td>
                                                    {{ $nested_checks->name }}
                                                    @if (isset($nested_checks->required_value))
                                                    <code>{{ $nested_checks->required_value }}</code>
                                                    @endif
                                                </td>
                                                <td>
                                                    <ul class="list-unstyled">
                                                        @foreach ($nested_checks as $subcheck)
                                                        @if (! is_string($subcheck))
                                                        <li>
                                                            @if (strtoupper($subcheck->severity) == 'INFO')
                                                            <i class="fa-solid fa-circle-info text-info"></i>
                                                            @elseif (strtoupper($subcheck->severity) == 'SUCCESS')
                                                            <i class="fa-solid fa-circle-check text-success"></i>
                                                            @elseif (strtoupper($subcheck->severity) == 'WARNING')
                                                            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                                            @elseif (strtoupper($subcheck->severity) == 'DANGER')
                                                            <i class="fa-solid fa-circle-xmark text-danger"></i>
                                                            @endif
                                                        </li>
                                                        @endif
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul class="list-unstyled">
                                                        @foreach ($nested_checks as $subcheck)
                                                        @if (! is_string($subcheck))
                                                        <li>{{ (isset($subcheck->name)) ? $subcheck->name : "NO NAME" }}</li>
                                                        @endif
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach

                                @if (
                                    (strtoupper($section) != strtoupper($previous_section_name)) AND
                                    (strtoupper($section) == strtoupper("Versions"))
                                )
                                    <tr>
                                        <td>jQuery Version</td>
                                        <td>
                                            <i class="fa-solid fa-circle-info text-info"></i>
                                        </td>
                                        <td id="jquery_version"></td>
                                    </tr>
                                @endif

                                @php
                                    $previous_section_name = strtoupper($section);
                                @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <p>
                        <i>
                            Legend:
                            <i class="fa-solid fa-circle-check text-success"></i> Good (no issues),
                            <i class="fa-solid fa-triangle-exclamation text-warning"></i> Warning (limited functionality),
                            <i class="fa-solid fa-circle-xmark text-danger"></i> Misconfiguration (something will not work),
                            <i class="fa-solid fa-circle-info text-info"></i> Information (just for your information)
                        </i>
                    </p>
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $("#jquery_version").html($.fn.jquery);
                });
            </script>
        </div>
    </div>
</div>
@endsection
