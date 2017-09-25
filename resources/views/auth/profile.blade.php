@extends('turtle::layouts.app')

@section('title', 'Edit Profile')
@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                @yield('title')
            </div>
            <div class="card-body">
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

                    <button type="submit" class="btn btn-primary">Edit</button>
                </form>
            </div>
        </div>
    </div>
@endsection