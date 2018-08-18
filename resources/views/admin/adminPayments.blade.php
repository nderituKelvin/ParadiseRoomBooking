@extends('theLayouts.mainLayout')

@section('head')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('bootstrap/assets/img/favicons/android-chrome-192x192.png') }}">
    <title>Admin | Payments Report</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar-top-fixed.css') }}" rel="stylesheet">
@endsection

@section('body')
    @include('admin.includes.navbar')
    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="{{ asset('bootstrap/assets/img/favicons/android-chrome-512x512.png') }}" alt="" width="72" height="72">
            <h2>Payments</h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                Show data of last:
                <a href="{{ route('adminViewPayments', ['range' => 'day']) }}" class="btn btn-sm btn-primary"> Day</a>
                <a href="{{ route('adminViewPayments', ['range' => 'week']) }}" class="btn btn-sm btn-primary"> Week</a>
                <a href="{{ route('adminViewPayments', ['range' => 'month']) }}" class="btn btn-sm btn-primary"> Month</a>
                <a href="{{ route('adminViewPayments', ['range' => 'year']) }}" class="btn btn-sm btn-primary"> Year</a>
            </div>
            <br>
            <br>
        </div>
        <table class="table table-hover table-striped table-bordered table-responsive-lg">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">User</th>
                <th scope="col">Credit</th>
                <th scope="col">Debit</th>
                <th scope="col">Description</th>
                <th scope="col">Paid For</th>
                <th scope="col">When</th>
            </tr>
            </thead>
            <tbody>
            @foreach($payments as $payment)
                <tr class="@if($payment->credit == 0) bg-primary @endif">
                    <th>{{ $payment->receiptno }}</th>
                    <th>{{ \App\User::where('id', $payment->user)->first()->name }}</th>
                    <th>{{ $payment->credit }}</th>
                    <th>{{ $payment->debit }}</th>
                    <th>{{ $payment->description }}</th>
                    <th>{{ $payment->paidfor }}</th>
                    <th>{{ \Carbon\Carbon::parse($payment->created_at)->diffForHumans() }}</th>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $payments->links() }}
        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; {{ date("Y") }}</p>
        </footer>
    </div>
@endsection

@section('scripts')
    {{--<script>window.jQuery || document.write('<script src="{{ asset('bootstrap/assets/js/vendor/jquery-slim.min.js') }}"><\/script>')</script>--}}
    {{--<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>--}}
    {{--<script src="{{ asset('bootstrap/assets/js/vendor/popper.min.js') }}"></script>--}}
@endsection