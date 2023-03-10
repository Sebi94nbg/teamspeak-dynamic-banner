@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Templates') }} <a href="{{ route('template.add') }}" class="btn btn-primary">Add</a></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (count($templates) > 0)
                    <table id="templates" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Alias</th>
                                <th>Template</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                            <tr>
                                <td>{{ $template->alias }}</td>
                                <td>
                                    <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" style="max-height: 200px;" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                                </td>
                                <td>
                                    <a href="{{ route('template.edit', ['template_id' => $template->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('template.delete', ['template_id' => $template->id]) }}">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p>You do not have any templates uploaded yet. Add your first template.</p>
                    @endif
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
