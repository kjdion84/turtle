@extends('turtle::layouts.modal')

@section('title', 'Add bread_model_string')
@section('content')
    <form method="POST" action="{{ route('bread_model_variables.add') }}" novalidate>
        {{ csrf_field() }}

        <div class="modal-body">
            <!-- bread_input_add -->
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Add</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
@endsection