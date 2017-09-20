@extends('turtle::layouts.app')

@section('title', 'Reset Password')
@section('content')
    <div class="container">
        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            {{ csrf_field() }}

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
        </form>
    </div>
@endsection