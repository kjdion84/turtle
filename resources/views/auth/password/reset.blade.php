@extends('turtle::layouts.app')

@section('title', 'Reset Password')
@section('content')
    <div class="container">
        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <form method="POST" action="{{ route('password.reset') }}" novalidate>
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>
@endsection