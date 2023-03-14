@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Banners') }} <a href="{{ route('banner.add') }}" class="btn btn-primary">Add</a></div>

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

                    @if (count($banners) > 0)
                    <table id="banners" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Instance</th>
                                <th>Templates in use</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banners as $banner)
                            <tr>
                                <td>{{ $banner->name }}</td>
                                <td>
                                    {{ $banner->instance->virtualserver_name }}
                                    <a href="{{ route('instance.edit', ['instance_id' => $banner->instance->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                </td>
                                <td>{{ $banner->templates->count() }}</td>
                                <td>
                                    <a href="{{ route('banner.edit', ['banner_id' => $banner->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="{{ route('banner.variables', ['banner_id' => $banner->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-square-root-variable"></i>
                                    </a>
                                    <a href="{{ route('banner.templates', ['banner_id' => $banner->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-images"></i>
                                    </a>
                                    <a href="{{ route('api.banner', ['banner_id' => base_convert($banner->id, 10, 35)]) }}" target="_blank" class="btn btn-info">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('banner.delete', ['banner_id' => $banner->id]) }}">
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
                    <p>You do not have any banners configured yet. Add your first banner.</p>
                    @endif
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $('#banners').DataTable();
                });
            </script>
        </div>
    </div>
</div>
@endsection
