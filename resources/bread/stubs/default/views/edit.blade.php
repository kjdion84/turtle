@extends('turtle::layouts.modal')

@section('title', 'Edit bread_model_string')
@section('content')
    <form method="POST" action="{{ route('bread_model_variables.edit', $bread_model_variable->id) }}" novalidate>
        {{ method_field('PATCH') }}
        {{ csrf_field() }}

        <div class="modal-body">
            <!-- bread_input_edit -->
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Edit</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
@endsection