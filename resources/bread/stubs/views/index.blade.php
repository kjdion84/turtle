@extends('turtle::layouts.app')

@section('title', 'bread_model_strings')
@section('content')
    <div class="container">
        @can('Add bread_model_strings')
            <button type="button" class="btn btn-primary btn-icon float-right" data-modal="{{ route('bread_model_variables.add') }}" data-toggle="tooltip" title="Add"><i class="fa fa-plus"></i></button>
        @endcan

        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <table id="bread_model_variables_datatable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <!-- bread_datatable_heading -->
                <th class="actions">Actions</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#bread_model_variables_datatable').DataTable({
                ajax: '{{ route('bread_model_variables.datatable') }}',
                columns: [
                    /* bread_datatable_column */
                    {
                        render: function (data, type, full) {
                            var actions = '';

                            @can('Edit bread_model_strings')
                                actions += ' <button type="button" class="btn btn-primary btn-icon" data-modal="{{ route('bread_model_variables.edit', ':id') }}" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></button> ';
                            @endcan
                            @can('Delete bread_model_strings')
                                actions += ' <button type="button" class="btn btn-danger btn-icon" data-modal="{{ route('delete', ['route' => 'bread_model_variables.delete', 'id' => ':id']) }}" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></button> ';
                            @endcan

                            return actions.replace(/:id/g, full.id);
                        }
                    }
                ]
            });
        });
    </script>
@endpush