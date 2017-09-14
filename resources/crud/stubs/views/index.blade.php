@extends('kjdion84.turtle.layouts.app')

@section('title', 'crud_model_strings')
@section('content')
    <div class="container">
        @can('Create crud_model_strings')<button type="button" class="btn btn-primary btn-icon float-right" data-modal="{{ route('crud_model_variables.create') }}" data-toggle="tooltip" title="Create"><i class="fa fa-plus"></i></button>@endcan

        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <table id="crud_model_variables_datatable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
            <thead>
            <tr>
                <!-- crud_datatable_heading -->
                <th class="actions">Actions</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#crud_model_variables_datatable').DataTable({
                ajax: '{{ route('crud_model_variables.datatable') }}',
                columns: [
                    /* crud_datatable_column */
                    {
                        render: function (data, type, full) {
                            var actions = '';

                            @can('Update crud_model_strings')actions += ' <button type="button" class="btn btn-primary btn-icon" data-modal="{{ route('crud_model_variables.update', ':id') }}" data-toggle="tooltip" title="Update"><i class="fa fa-pencil"></i></button> ';@endcan
                            @can('Delete crud_model_strings')actions += ' <button type="button" class="btn btn-danger btn-icon" data-modal="{{ route('delete', ['route' => 'crud_model_variables.delete', 'id' => ':id']) }}" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></button> ';@endcan

                            return actions.replace(/:id/g, full.id);
                        }
                    }
                ]
            });
        });
    </script>
@endpush