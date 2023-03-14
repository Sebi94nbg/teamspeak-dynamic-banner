@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Users') }} <a href="{{ route('administration.user.add') }}" class="btn btn-primary">Add</a></div>

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

                    <table id="users" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered since</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    <a href="{{ route('administration.user.edit', ['user_id' => $user->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>

                                    @if (Auth::user()->id != $user->id)
                                    <form method="POST" action="{{ route('administration.user.delete', ['user_id' => $user->id]) }}">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></a>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $('#users').DataTable();
                });
            </script>
        </div>
    </div>
</div>
@endsection
