@extends('turtle::layouts.app')

@section('title', 'Email Password Reset Link')
@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                @yield('title')
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>

                    @if (config('turtle.recaptcha.site_key'))
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="{{ config('turtle.recaptcha.site_key') }}"></div>
                            <script src="https://www.google.com/recaptcha/api.js"></script>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">Email Password Reset Link</button>
                </form>
            </div>
        </div>
    </div>
@endsection