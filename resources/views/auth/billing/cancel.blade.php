@extends('turtle::layouts.modal')

@section('title', 'Cancel')
@section('content')
    <div class="modal-body">
        <p>Are you sure you want to cancel your subscription?</p>
    </div>

    <div class="modal-footer">
        <form method="POST" action="{{ route('billing.cancel') }}" novalidate>
            {{ csrf_field() }}
            <button type="submit" class="btn btn-danger">Yes</button>
        </form>

        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
    </div>
@endsection