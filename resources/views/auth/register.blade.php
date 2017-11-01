@extends('turtle::layouts.app')

@section('title', 'Register')
@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                @yield('title')
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}" novalidate>
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    @if (config('turtle.recaptcha.site_key'))
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="{{ config('turtle.recaptcha.site_key') }}"></div>
                            <script src="https://www.google.com/recaptcha/api.js"></script>
                        </div>
                    @endif

                    <input type="hidden" name="timezone" id="timezone">

                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (typeof timezone === 'undefined') timezone = 'UTC';
            $('#timezone').val(timezone);
        });
    </script>
@endpush