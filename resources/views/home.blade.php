@extends('turtle::layouts.app')

@section('title', 'Home')
@section('content')
    <div class="container">
        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <p>Welcome home!</p>
    </div>
@endsection