@extends('kjdion84.turtle.layouts.app')

@section('title', $user->name.' Activity')
@section('content')
    <div class="container">
        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <table id="users_activity_datatable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Log</th>
                <th>Date</th>
                <th class="actions">Actions</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#users_activity_datatable').DataTable({
                ajax: '{{ route('users.activity.datatable', $user->id) }}',
                order: [[ 1, 'desc' ]],
                columns: [
                    { data: 'log' },
                    { data: 'created_at' },
                    {
                        render: function (data, type, full) {
                            var actions = '';

                            actions += ' <button type="button" class="btn btn-primary btn-icon" data-modal="{{ route('users.activity.data', ':id') }}" data-toggle="tooltip" title="Data"><i class="fa fa-database"></i></button> ';

                            return actions.replace(/:id/g, full.id);
                        }
                    }
                ]
            });
        });
    </script>
@endpush