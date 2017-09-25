@extends('turtle::layouts.app')

@section('title', 'Roles')
@section('content')
    <div class="container">
        @can('Add Roles')
            <button type="button" class="btn btn-primary btn-icon float-right" data-modal="{{ route('roles.add') }}" data-toggle="tooltip" title="Add"><i class="fa fa-plus"></i></button>
        @endcan

        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <table id="roles_datatable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Name</th>
                <th class="actions">Actions</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#roles_datatable').DataTable({
                ajax: '{{ route('roles.datatable') }}',
                columns: [
                    { data: 'name' },
                    {
                        render: function (data, type, full) {
                            var actions = '';

                            if (full.id !== '1') {
                                @can('Edit Roles')
                                    actions += ' <button type="button" class="btn btn-primary btn-icon" data-modal="{{ route('roles.edit', ':id') }}" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></button> ';
                                @endcan
                                @can('Delete Roles')
                                    actions += ' <button type="button" class="btn btn-danger btn-icon" data-modal="{{ route('delete', ['route' => 'roles.delete', 'id' => ':id']) }}" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></button> ';
                                @endcan
                            }

                            return actions.replace(/:id/g, full.id);
                        }
                    }
                ]
            });
        });
    </script>
@endpush