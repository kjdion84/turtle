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

                    <button type="submit" class="btn btn-primary">Email Password Reset Link</button>
                </form>
            </div>
        </div>
    </div>
@endsection