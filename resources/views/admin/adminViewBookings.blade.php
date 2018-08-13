@extends('theLayouts.mainLayout')

@section('head')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('bootstrap/assets/img/favicons/android-chrome-192x192.png') }}">
    <title>Admin | View Bookings</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar-top-fixed.css') }}" rel="stylesheet">
@endsection

@section('body')
    @include('admin.includes.navbar')




    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="{{ asset('bootstrap/assets/img/favicons/android-chrome-512x512.png') }}" alt="" width="72" height="72">
            <h2>Bookings</h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                Show Data from last:
                <a href="{{ route('adminViewBookings', ['range' => 'day']) }}" class="btn btn-sm btn-primary"> Day</a>
                <a href="{{ route('adminViewBookings', ['range' => 'week']) }}" class="btn btn-sm btn-primary"> Week</a>
                <a href="{{ route('adminViewBookings', ['range' => 'month']) }}" class="btn btn-sm btn-primary"> Month</a>
                <a href="{{ route('adminViewBookings', ['range' => 'year']) }}" class="btn btn-sm btn-primary"> Year</a>
            </div>
            <br>
            <br>
            <br>
        </div>
        <table class="table table-hover table-striped table-bordered table-responsive-lg">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Charges</th>
                <th scope="col">Capacity</th>
                <th scope="col">Room</th>
                <th scope="col">Checkin</th>
                <th scope="col">Check Out</th>
                <th scope="col">Status</th>
                <th scope="col">Confirm</th>
                <th scope="col">Cancel</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bookings as $booking)
                    <tr class="
                    @if($booking->status == "pending")
                            bg-warning
                    @endif
                    @if($booking->status == "active")
                            bg-success
                    @endif
                    @if($booking->status == "done")
                            bg-info
                    @endif
                    @if($booking->status == "canceled")
                            bg-danger
                    @endif ">
                    <th>{{ $booking->receipt }}</th>
                    <th>{{ \App\Payment::where('receiptno', $booking->receipt)->first()->credit }}</th>
                    <th>{{ $booking->capacity }}</th>

                    <th>{{ \App\Room::where('id', $booking->room)->first()->name }}, {{ \App\Room::where('id', $booking->room)->first()->location }}</th>
                    <th>{{ \Carbon\Carbon::parse($booking->checkin)->format("d F, Y") }}</th>
                    <th>{{ \Carbon\Carbon::parse($booking->checkout)->format("d F, Y") }}</th>
                    <th>{{ $booking->status }}</th>

                    <th>
                        <a href="{{ route('adminConfirmBooking', ['booking' => $booking->id]) }}" class="btn btn-success btn-sm
                            @if($booking->status != "pending" || \Carbon\Carbon::now() >= \Carbon\Carbon::parse($booking->checkin) )
                                disabled
                            @endif ">
                            Confirm
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('adminCancelBooking', ['booking' => $booking->id]) }}" class="btn btn-danger btn-sm
                            @if($booking->status != "pending" ||  \Carbon\Carbon::now() >= \Carbon\Carbon::parse($booking->checkin) )
                                disabled
                            @endif ">
                            Cancel
                        </a>
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $bookings->links() }}
        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; {{ date("Y") }}</p>
        </footer>
    </div>




@endsection

@section('scripts')
    <script>window.jQuery || document.write('<script src="{{ asset('bootstrap/assets/js/vendor/jquery-slim.min.js') }}"><\/script>')</script>
    <script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bootstrap/assets/js/vendor/popper.min.js') }}"></script>
@endsection