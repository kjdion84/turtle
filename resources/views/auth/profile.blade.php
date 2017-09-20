@extends('turtle::layouts.app')

@section('title', 'Update Profile')
@section('content')
    <div class="container">
        <h1 class="display-5 mt-4 mb-4">@yield('title')</h1>

        <form method="POST" action="{{ route('profile') }}" novalidate>
            {{ method_field('PATCH') }}
            {{ csrf_field() }}

            <div class="form-group">
                <label for="name">Name</label>
                <input name="name" id="name" class="form-control" value="{{ auth()->user()->name }}">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ auth()->user()->email }}">
            </div>

            <div class="form-group">
                <label for="timezone">Timezone</label>
                <select name="timezone" id="timezone" class="form-control">
                    @foreach (timezones() as $timezone)
                        <option value="{{ $timezone['identifier'] }}"{{ $timezone['identifier'] == auth()->user()->timezone ? ' selected' : '' }}>{{ $timezone['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection