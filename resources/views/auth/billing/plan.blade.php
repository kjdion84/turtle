@extends('turtle::layouts.modal')

@section('title', $id)
@section('content')
    <form method="POST" action="{{ route('billing.plan', $id) }}" novalidate>
        {{ csrf_field() }}

        <div class="modal-body">
            <div class="form-group">
                <label for="number">Card Number</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-credit-card"></i></div>
                    <input type="number" name="number" id="number" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="exp_month">Card Expiration Date</label>
                <div class="row">
                    <div class="col">
                        <input type="number" name="exp_month" id="exp_month" class="form-control" placeholder="MM">
                    </div>
                    <div class="col">
                        <input type="number" name="exp_year" id="exp_year" class="form-control" placeholder="YY">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="cvc">Card Security Code</label>
                <input type="number" name="cvc" id="cvc" class="form-control">
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Subscribe</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
@endsection