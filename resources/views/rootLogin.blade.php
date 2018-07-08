@extends('theLayouts.mainLayout')
@section('head')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('bootstrap/assets/img/favicons/android-chrome-192x192.png') }}">
    <title>Log In</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
@endsection



@section('body')
    <form class="form-signin" method="post" action="{{ route('postSignIn') }}">
        <img class="mb-4" src="{{ asset('bootstrap/assets/img/favicons/android-chrome-192x192.png') }}" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Paradise Hotel</h1>
        <h1 class="h4 mb-4 font-weight-normal">Login</h1>
        <label for="inputEmail" class="sr-only">Phone</label>
        <input type="tel" id="inputUsername" name="phone" class="form-control" placeholder="Phone" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        {{ csrf_field() }}
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-3 mb-5 font-weight-light"><a href="{{ route('homeSignUp') }}">Sign Up</a></p>
    </form>
@endsection



@section('scripts')

@endsection