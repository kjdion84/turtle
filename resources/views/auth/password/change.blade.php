@extends('turtle::layouts.app')

@section('title', 'Change Password')
@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                @yield('title')
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.change') }}" novalidate>
                    {{ method_field('PATCH') }}
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Change</button>
                </form>
            </div>
        </div>
    </div>
@endsection