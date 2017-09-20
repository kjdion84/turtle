@extends('turtle::layouts.app')

@section('title', 'Users')
@section('content')
    <div class="container">
        @can('Create Users')<button type="button" class="btn btn-primary btn-icon float-right" data-modal="{{ route('users.create') }}" data-toggle="tooltip" title="Create"><i class="fa fa-plus"></i></button>@endcan

        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <table id="users_datatable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th class="actions">Actions</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#users_datatable').DataTable({
                ajax: '{{ route('users.datatable') }}',
                columns: [
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'roles' },
                    {
                        render: function (data, type, full) {
                            var actions = '';

                            @can('View Activities')actions += ' <a href="{{ route('users.activity', ':id') }}" class="btn btn-primary btn-icon" data-toggle="tooltip" title="Activity"><i class="fa fa-history"></i></a> ';@endcan
                            @can('Update Users')
                                actions += ' <button type="button" class="btn btn-primary btn-icon" data-modal="{{ route('users.update', ':id') }}" data-toggle="tooltip" title="Update"><i class="fa fa-pencil"></i></button> ';
                                actions += ' <button type="button" class="btn btn-primary btn-icon" data-modal="{{ route('users.password', ':id') }}" data-toggle="tooltip" title="Password"><i class="fa fa-lock"></i></button> ';
                            @endcan
                            @can('Delete Users')actions += ' <button type="button" class="btn btn-danger btn-icon" data-modal="{{ route('delete', ['route' => 'users.delete', 'id' => ':id']) }}" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></button> ';@endcan

                            return actions.replace(/:id/g, full.id);
                        }
                    }
                ]
            });
        });
    </script>
@endpush