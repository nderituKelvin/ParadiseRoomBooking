@extends('theLayouts.mainLayout')
@section('head')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('bootstrap/assets/img/favicons/android-chrome-192x192.png') }}">
    <title>Sign Up</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
@endsection



@section('body')
    <form class="form-signin" method="post" enctype="multipart/form-data" action="{{ route('postSignUp') }}">
        <img class="mb-4" src="{{ asset('bootstrap/assets/img/favicons/android-chrome-192x192.png') }}" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Confy Halls</h1>
        <h1 class="h4 mb-4 font-weight-normal">Sign Up</h1>

        <label for="inputName" class="sr-only">Name</label>
        <input type="text" id="inputName" name="name" class="form-control" placeholder="Name" required autofocus>
        <label for="inputEmail" class="sr-only">Phone</label>
        <input type="tel" id="inputUsername" name="phone" class="form-control" placeholder="Phone" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="pass" id="inputPassword" class="form-control" placeholder="Password" required>
        <label for="inputConfirmPassword" class="sr-only">Confirm Password</label>
        <input type="password" name="conpass" id="inputConfirmPassword" class="form-control" placeholder="Confirm Password" required>

        <label for="inputPhoto" class="sr-only">Profile Photo</label>
        <input type="file" name="file" id="inputPhoto" class="form-control" required>

        {{ csrf_field() }}
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign Up</button>
        <p class="mt-3 mb-5 font-weight-light"><a href="{{ route('homeLogin') }}">Sign In</a></p>
    </form>
@endsection


@section('scripts')

@endsection