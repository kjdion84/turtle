<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('turtle/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('turtle/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('turtle/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('turtle/css/turtle.css') }}">
</head>
<body{!! session('flash') ? ' data-flash-class="'.session('flash.0').'" data-flash-message="'.session('flash.1').'"' : '' !!}>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('index') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav ml-auto">
                @if(config('turtle.allow.frontend'))
                    <li class="nav-item{{ request()->is('/') ?  ' active' : '' }}"><a class="nav-link" href="{{ route('index') }}">Home</a></li>
                @endif

                @if(auth()->guest())
                    <li class="nav-item{{ request()->is('login') ?  ' active' : '' }}"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @if(config('turtle.allow.registration'))
                        <li class="nav-item{{ request()->is('register') ?  ' active' : '' }}"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endif
                @else
                    <li class="nav-item{{ request()->is('dashboard') ?  ' active' : '' }}"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <!-- crud_navbar -->
                    @canany('View Roles', 'View Users')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Manage</a>
                            <div class="dropdown-menu dropdown-menu-right">
                                @can('View Roles')<a class="dropdown-item{{ request()->is('roles') ?  ' active' : '' }}" href="{{ route('roles') }}">Roles</a>@endcan
                                @can('View Users')<a class="dropdown-item{{ request()->is('users') ?  ' active' : '' }}" href="{{ route('users') }}">Users</a>@endcan
                            </div>
                        </li>
                    @endcanany
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"><i class="fa fa-user-circle"></i> {{ auth()->user()->name }}</a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item{{ request()->is('profile') ?  ' active' : '' }}" href="{{ route('profile') }}">Update Profile</a>
                            <a class="dropdown-item{{ request()->is('password/change') ?  ' active' : '' }}" href="{{ route('password.change') }}">Change Password</a>
                            <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

@yield('content')

<footer>
    <div class="container">
        @if (config('turtle.allow.contact'))
            <a href="{{ route('contact') }}" class="pull-right text-secondary">Contact</a>
        @endif
        <span class="text-secondary">&copy;{{ date('Y', time()) }}</span> <a href="{{ route('index') }}" class="text-secondary">{{ config('app.name') }}</a>
    </div>
</footer>

<!-- Scripts -->
<script src="{{ asset('turtle/js/jquery.min.js') }}"></script>
<script src="{{ asset('turtle/js/popper.min.js') }}"></script>
<script src="{{ asset('turtle/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('turtle/js/datatables.min.js') }}"></script>
<script src="{{ asset('turtle/js/turtle.js') }}"></script>
@stack('scripts')
</body>
</html>