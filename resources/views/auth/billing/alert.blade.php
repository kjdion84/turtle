@if(auth()->check() && auth()->user()->billable && config('turtle.allow.billing') && !request()->is('billing'))
    @if(auth()->user()->billingTrial())
        <div class="container mt-4">
            <div class="alert alert-warning">
                Your free trial expires at {{ auth()->user()->billing_trial_ends }}, please <a href="{{ route('billing') }}" class="alert-link">select a plan</a> to hide this alert.
            </div>
        </div>
    @elseif(!auth()->user()->billingActive())
        <div class="container mt-4">
            <div class="alert alert-danger">
                Your account is currently inactive, please <a href="{{ route('billing') }}" class="alert-link">select a plan</a> to re-activate it now.
            </div>
        </div>
    @endif
@endif