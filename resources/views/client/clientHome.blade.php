@extends('theLayouts.mainLayout')

@section('head')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('bootstrap/assets/img/favicons/android-chrome-192x192.png') }}">
    <title>Client | View Rooms</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar-top-fixed.css') }}" rel="stylesheet">
    <link href="{{ asset('css/blog.css') }}" rel="stylesheet">
@endsection

@section('body')
    @include('client.includes.navbar')




    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="{{ asset('bootstrap/assets/img/favicons/android-chrome-512x512.png') }}" alt="" width="72" height="72">
            <h2>View Rooms</h2>
        </div>

        @foreach($rooms as $room)
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="card flex-md-row mb-4 box-shadow h-md-250">
                        <div class="card-body d-flex flex-column align-items-start">
                            <strong class="d-inline-block mb-2 text-primary">Ksh {{ $room->ppn }} per Hour</strong>
                            <h3 class="mb-0">
                                <a class="text-dark" href="#">{{ $room->name }}</a>
                            </h3>
                            <div class="mb-1 text-muted">Capacity - {{ $room->capacity }}</div>
                            <div class="mb-1 text-muted">Theme - {{ $room->theme }}</div>
                            <p class="card-text mb-auto">{{ $room->info }}</p>
                            <a href="{{ route('clientViewRoom', ['roomid' => $room->id]) }}">View and Book Room</a>
                        </div>
                        <img class="card-img-right flex-auto d-none d-lg-block" src="{{ asset('storage/images/'.\App\Photo::where('native', 'room')->where('nativeid', $room->id)->inRandomOrder()->first()->name) }}" alt="No Images">
                    </div>
                </div>
            </div>
        @endforeach
        {{ $rooms->links() }}
    </div>


    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; {{ date("Y") }}</p>
    </footer>
    </div>




@endsection

@section('scripts')
    <script>window.jQuery || document.write('<script src="{{ asset('bootstrap/assets/js/vendor/jquery-slim.min.js') }}"><\/script>')</script>
    <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
    <script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bootstrap/assets/js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('css/holder.min.js') }}"></script>
    <script>
        Holder.addTheme('thumb', {
            bg: '#55595c',
            fg: '#eceeef',
            text: 'Thumbnail'
        });
    </script>
@endsection