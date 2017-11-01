@extends('turtle::layouts.app')

@section('title', 'Billing')
@section('content')
    <div class="container">
        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <div class="row">
            @foreach(config('turtle.billing.plans') as $id => $plan)
                <div class="col-md mb-4">
                    <div class="card">
                        <div class="card-header">
                            {{ $id }}
                        </div>
                        <div class="card-body">
                            {!! $plan['html'] !!}
                            @if(auth()->user()->billing_plan == $id && auth()->user()->billingActive())
                                <a href="#" class="btn btn-success disabled">Current Plan</a>
                            @else
                                <button type="button" class="btn btn-primary" data-modal="{{ route('billing.plan', $id) }}">Select Plan</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Payment Method
            </div>
            <div class="card-body">
                @if(auth()->user()->billingTrial())
                    Your free trial expires at {{ auth()->user()->billing_trial_ends }}, please select a plan above.
                @elseif(!auth()->user()->billingActive())
                    Your account is currently inactive, please select a plan above.
                @else
                    <h5><i class="fa fa-credit-card"></i> ************{{ auth()->user()->billing_cc_last4 }}</h5>
                    <div class="mb-2">Next charge at {{ auth()->user()->billing_period_ends }}</div>
                    <button type="button" class="btn btn-danger" data-modal="{{ route('billing.cancel') }}">Cancel Subscription</button>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Payment History
            </div>
            <div class="card-body">
                @if(auth()->user()->billing->isEmpty())
                    You have no payment history. If you recently made a payment, please refresh this page in a minute.
                @else
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Card</th>
                            <th>Billed At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (auth()->user()->billing as $billing)
                            <tr>
                                <td>{{ $billing->plan_name }}</td>
                                <td>{{ $billing->amount }}</td>
                                <td><i class="fa fa-credit-card"></i> {{ $billing->cc_last4 }}</td>
                                <td>{{ $billing->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection