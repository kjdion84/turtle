@extends('turtle::layouts.app')

@section('title', 'Login')
@section('content')
    <div class="container">
        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        @if (config('turtle.demo_mode'))
            <p class="text-danger"><b>Warning:</b> app is currently in demo mode, some features are disabled.</p>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            {{ csrf_field() }}

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>

            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" name="remember" class="form-check-input">
                    Remember
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
            <a class="btn btn-link" href="{{ route('password.email') }}">Forgot Your Password?</a>
        </form>
    </div>
@endsection