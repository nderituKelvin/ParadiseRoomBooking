<?php

use App\Booking;
use App\Http\Controllers\FuncController;
use App\Money;
use App\Payment;
use App\Photo;
use App\Room;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

    Route::get('/login', [
        'as' => 'homeLogin',
        function(){
            return view('rootLogin');
        }
    ]);

    Route::get('/signup', [
        'as' => 'homeSignUp',
        function(){
            return view('signup');
        }
    ]);

    Route::get('logout', [
        'as' => 'logout',
        function(){
            Auth::logout();
            return redirect()->route('homeLogin')->with([
                'title' => "Goodbye",
                'message' => "",
                'status' => 'info'
            ]);
        }
    ])->middleware('auth');

    Route::get('loginn', [
        'as' => 'login',
        function(){
            $func = new FuncController();
            return $func->toRouteWithMessage('homeLogin', 'Logged Out', '', 'info');
        }
    ]);

    Route::post('postSignUp', [
        'as' => 'postSignUp',
        function(Request $request){
            $func = new FuncController();
            $client = new User();
            $client->name = $request['name'];
            $client->phone = $request['phone'];
            $client->usertype = "client";
            $client->status = "active";

            if($request['pass'] != $request['conpass']){
                return $func->backWithMessage("Sorry", "Your Passwords do Not matech", "error");
            }

            $client->password = bcrypt($request['pass']);

            if(User::where('phone', $request['phone'])->count() > 0){
                return $func->backWithMessage("Sorry", "Phone Number Exists in system", "error");
            }

            if($client->save()){
                if($request->hasFile('file')){
                    if($func->uploadImage($request->file('file'), "user", "512x512", $client->id)){
                        return $func->backWithMessage("Success", "Sign Up Successful, You may Login now", "success");
                    }else{
                        $client->delete();
                        return $func->backWithUnknownError();
                    }
                }else{
                    $client->delete();
                    return $func->backWithMessage("Failed", "Image Upload Failed", "error");
                }
            }else{
                return $func->backWithUnknownError();
            }

        }
    ]);

    Route::post('postSignIn', [
        'as' => 'postSignIn',
        function(Request $request){
            $func = new FuncController();
            if(Auth::attempt(['phone' => $request['phone'], 'password' => $request['password'],  'status' => 'active'])){
                $user = User::where('phone', $request['phone'])->first();
                if($user->usertype == "client"){
                    // Login client
                    return $func->toRouteWithMessage("clientHome", "", "Welcome Back", "info");
                }else if ($user->usertype == "admin"){
                    // Login Admin
                    return $func->toRouteWithMessage("adminHome", "", "Welcome Back Admin", "info");
                }
            }else{
                return $func->backWithMessage("Failed", "Login Failed, Please Check Your Phone and Password", "error");
            }
        }
    ]);

    Route::get('admin/home', [
        'as' => "adminHome",
        function(){
            return view("admin.adminHome");
        }
    ])->middleware('auth')->middleware('ca');

    Route::post('admin/postAddRoom', [
        'as' => 'adminAddRoom',
        function(Request $request){
            $func = new FuncController();
            $room = new Room();
            $room->name = $request['name'];
            $room->ppn = $request['charges'];
            $room->status = "active";
            $room->location = $request['location'];
            $room->theme = $request['theme'];
            $room->capacity = $request['capacity'];
            $room->size = $request['size'];
            $room->info = $request['info'];
            if($room->save()){
                return redirect()->route("adminAddRoomPhotosPage", [
                    "roomId" => $room->id
                ])->with([
                    "title" => "Add Some Photos Now",
                    "message" => "",
                    "status" => "info"
                ]);
            }else{
                return $func->backWithUnknownError();
            }
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/addphotos/{roomId}', [
        'as' => 'adminAddRoomPhotosPage',
        function($roomId){
            $func = new FuncController();
            $roomRaw = Room::where('id', $roomId);
            if($roomRaw->count() != 1){
                return $func->backWithMessage("Sorry", "Room not Found", "error");
            }else{
                return view('admin.addImagesToRoom', [
                    "roomid" => $roomId
                ]);
            }
        }
    ])->middleware('auth')->middleware('ca');

    Route::post('admin/postAddPhotos', [
        'as' => 'adminPostAddRoomPhotos',
        function(Request $request){
            $func = new FuncController();
            $roomRaw = Room::where('id', $request['roomid']);
            if(Photo::where('native', 'room')->where('nativeid', $request['roomid'])->count() >= 10){
                return $func->backWithMessage("Sorry", "Maximum Images for room reached", "warning");
            }
            if($roomRaw->count() != 1){
                return $func->backWithMessage("Not Found", "Room Not Found", "warning");
            }else{
                if($request->hasFile("file")){
                    if($func->uploadImage($request->file('file'), "room", "512x512", $request['roomid'])){
                        return $func->backWithMessage("Uploaded", "You can upload another image", "success");
                    }else{
                        return $func->backWithUnknownError();
                    }
                }else{
                    return $func->backWithMessage("No Image", "", "info");
                }
            }
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/addcredits', [
        'as' => 'adminAddCredits',
        function(){
            $func = new FuncController();
            return view('admin.adminAddCredits');
        }
    ])->middleware('auth')->middleware('ca');

    Route::post('admin/postAddCredits', [
        'as' => 'adminPostAddCredits',
        function(Request $request){
            $func = new FuncController();
            $money = new Money();
            $money->code = $request['code'];
            $money->value = $request['amount'];
            $money->status = "active";
            if($money->save()){
                return $func->backWithMessage("Added", "Saved", "success");
            }else{
                return $func->backWithUnknownError();
            }
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/rooms', [
        'as' => 'adminViewRooms',
        function(){
            $func = new FuncController();
            $rooms = Room::where('status', 'active')->orderby('id', 'desc')->paginate(5);
            return view('admin.adminViewRooms', [
                'rooms' => $rooms
            ]);
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('/', [
        'as' => "clientHome",
        function(){
            $rooms = Room::where('status', 'active')->orderby('id', 'desc')->paginate(5);
            return view("client.clientHome", [
                'rooms' => $rooms
            ]);
        }
    ]);

    Route::get('client/rooms/{roomid}', [
        'as' => 'clientViewRoom',
        function($roomid){
            $func = new FuncController();
            $roomRaw = Room::where('id', $roomid);
            if($roomRaw->count() != 1){
                return $func->backWithMessage("Room not found", "", "error");
            }else{
                $room = $roomRaw->first();
                return view('client.viewSingleRoom', [
                    'room' => $room
                ]);
            };
        }
    ]);

    Route::post('client/postBookRoom', [
        'as' => 'clientPostBookRoom',
        function(Request $request){
            $func = new FuncController();
            $roomRaw = Room::where('id', $request['roomid']);
            if($roomRaw->count() != 1){
                return $func->backWithMessage("Room not found", "", "error");
            }else{
                $room = $roomRaw->first();
            }
            $checkin = Carbon::parse($request['checkindate'] ." ".$request['checkintime']);
            $checkout = Carbon::parse($request['checkoutdate'] ." ".$request['checkouttime']);
            if($checkin <= Carbon::now()){
                return $func->backWithMessage("Failed", "You requested a date in the past", "error");
            }
            if($checkin >= $checkout){
                return $func->backWithMessage("Failed", "The Check out Date must be greater that Check in date", "error");
            }

            if($checkin->diffInHours($checkout) == 0){
                return $func->backWithMessage("Failed", "The time is too short", "error");
            }

            if($room->capacity < $request['capacity']){
                return $func->backWithMessage("Failed", "The room you requested cannot hold that capacity", "error");
            }

            $booking = new Booking();
            $booking->user = Auth::user()->getAuthIdentifier();
            $booking->checkin = $checkin;
            $booking->checkout = $checkout;
            $booking->capacity = $request['capacity'];
            $booking->room = $request['roomid'];
            $booking->status = "pending";
            $receiptno = "";

            if(Booking::where('room', $room->id)->where('status', 'pending')->orWhere('status', 'complete')->count() > 0){
                return $func->backWithMessage("Sorry", "You have already booked this room", "error");
            }
            if(Booking::where('room', $room->id)->where('checkin', '>=', Carbon::parse($booking->checkin))->where('checkout', '<=', Carbon::parse($booking->checkout))->count() > 0){
                return $func->backWithMessage("Sorry", "THis room is not available at that time", "error");
            }

            do{
                $receiptno = $func->generateRandomString(10);
            }while($receiptno == "" || Payment::where('receiptno', $receiptno)->count() == 1 );
            $booking->receipt = $receiptno;

            $diffHours = $checkin->diffInHours($checkout);
            $chargePerHour = $room->ppn;
            $charges = $chargePerHour * $diffHours;

            if($charges > $func->getClientBalance(Auth::user()->getAuthIdentifier())){
                return $func->backWithMessage("Not Enough Balance", "Your Balance is Ksh. ". $func->getClientBalance(Auth::user()->getAuthIdentifier()). "while the required balnce is Ksh. ".$charges, "error");
            }

            $payment = new Payment();
            $payment->user = Auth::user()->getAuthIdentifier();
            $payment->receiptNo = $receiptno;
            $payment->credit = $charges;
            $payment->debit = 0;
            $payment->description = "Booking for room ".$room->name. " on ". Carbon::now();
            $payment->paidfor = "Room Booking";

            if($booking->save()){
                if($payment->save()){
                    return redirect()->back()->with([
                        "title" => $payment->receiptno,
                        "message" => "You have successfully booked a room",
                        "status" => "success",
                        "booking" => $booking,
                        "payment" => $payment
                    ]);
//                    return $func->backWithMessage($payment->receiptno, "You have booked a room and your receipt number is: ". $payment->receiptno, "success");
                }else{
                    $booking->delete();
                    return $func->backWithMessage("Error perfoming transaction", "", "error");
                }
            }else{
                return $func->backWithMessage("Could not save booking", "", "warning");
            }
        }
    ])->middleware('auth')->middleware('cc');

    Route::get('client/topup', [
        'as' => 'clientTopUpBalance',
        function(){
            return view('client.topUpBalance');
        }
    ])->middleware('auth')->middleware('cc');

    Route::post('client/posttopup', [
        'as' => 'clientPostTopUp',
        function(Request $request){
            $func = new FuncController();
            $moneyRaw = Money::where('code', $request['code'])->where('status', 'active');
            if($moneyRaw->count() != 1){
                return $func->backWithMessage("Wrong MPESA Code", "", "error");
            }else{
                $money = $moneyRaw->first();
                $payment = new Payment();
                $payment->user = Auth::user()->getAuthIdentifier();
                $receiptno = "";
                do{
                    $receiptno = $func->generateRandomString(10);
                }while($receiptno == "" || Payment::where('receiptno', $receiptno)->count() == 1 );
                $payment->receiptno = $receiptno;
                $payment->credit = 0;
                $payment->debit = $money->value;
                $payment->description = "Topped Up MPESA code: ".$request['code'];
                $payment->paidfor = "Loaded credit";

                $money->status = "used";
                if($payment->save()){
                    $money->save();
                    return $func->backWithMessage("Topped Up", "", "success");
                }else{
                    return $func->backWithMessage("Failed", "", "error");
                }

            }
        }
    ])->middleware('auth')->middleware('cc');

    Route::get('client/payments', [
        'as' => 'clientViewPayments',
        function(){
            $payments = Payment::where('user', Auth::user()->getAuthIdentifier())->orderby('id', 'desc')->paginate(10);
            return view('client.clientPayment', [
                "payments" => $payments
            ]);
        }
    ])->middleware('auth')->middleware('cc');

    Route::get('client/bookings', [
        'as' => 'clientViewBookings',
        function(){
            $bookings = Booking::where('user', Auth::user()->getAuthIdentifier())->orderby('id', 'desc')->paginate(10);
            return view('client.clientBookings', [
                "bookings" => $bookings
            ]);
        }
    ])->middleware('auth')->middleware('cc');

    Route::get('client/cancelbooking/{booking}', [
        'as' => 'clientCancelBooking',
        function($bookId){
            $func = new FuncController();
            $bookRaw = Booking::where('id', $bookId)->where('status', 'pending')->where('user', Auth::user()->getAuthIdentifier());
            if($bookRaw->count() != 1){
                return $func->backWithMessage("Sorry", "Booking not found", "error");
            }
            $booking = $bookRaw->first();
            $booking->status = "canceled";
            if($booking->save()){
                $payment = Payment::where('receiptno', $booking->receipt)->first();
                $payDeb = new Payment();
                do{
                    $payDeb->receiptno = $func->generateRandomString(10);
                }while($payDeb->receiptno == "" || Payment::where('receiptno', $payDeb->receiptno)->count() == 1 );
                $payDeb->user = Auth::user()->getAuthIdentifier();
                $payDeb->credit = 0;
                $payDeb->debit = $payment->credit;
                $payDeb->description = "Refund for canceled booking of room ".Room::where('id', $booking->room)->first()->name.", ".Room::where('id', $booking->room)->first()->location;
                $payDeb->paidfor = "Refunding";
                if($payDeb->save()){
                    return $func->backWithMessage("Canceled", "Your Booking has been Canceled and money has been debited back to your account", "info");
                }else{
                    return $func->backWithMessage("Canceled", "But refund could not not be accomplished", "warning");
                }
            }else{
                return $func->backWithMessage("Failed", "System Failure", "error");
            }
        }
    ])->middleware('auth')->middleware('cc');

    Route::get('admin/bookings/{range?}', [
        'as' => 'adminViewBookings',
        function($range = 'all'){
            if($range == "all"){
                $bookings = Booking::orderby('id', 'desc')->paginate(10);
            }else if($range == 'day'){
                $bookings = Booking::orderby('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subDay())
                    ->paginate(10);
            }else if($range == 'week'){
                $bookings = Booking::orderby('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subWeek())
                    ->paginate(10);
            }else if($range == 'month'){
                $bookings = Booking::orderby('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subMonth())
                    ->paginate(10);
            }else if($range == 'year'){
                $bookings = Booking::orderby('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subYear())
                    ->paginate(10);
            }
            return view('admin.adminViewBookings', [
                'bookings' => $bookings
            ]);
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/confirmbooking/{booking}', [
        'as' => 'adminConfirmBooking',
        function($bookId){
            $func = new FuncController();
            $bookRaw = Booking::where('id', $bookId)->where('status', 'pending');
            if($bookRaw->count() != 1){
                return $func->backWithMessage("Sorry", "Booking not found", "error");
            }
            $booking = $bookRaw->first();
            $booking->status = "active";
            if($booking->save()){
                return $func->backWithMessage("Confirmed", "Booking has been Confirmed and money has been debited back to user account", "success");
            }else{
                return $func->backWithMessage("Failed", "System Failure", "error");
            }
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/cancelbooking/{booking}', [
        'as' => 'adminCancelBooking',
        function($bookId){
            $func = new FuncController();
            $bookRaw = Booking::where('id', $bookId)->where('status', 'pending');
            if($bookRaw->count() != 1){
                return $func->backWithMessage("Sorry", "Booking not found", "error");
            }
            $booking = $bookRaw->first();
            $booking->status = "canceled";
            if($booking->save()){
                $payment = Payment::where('receiptno', $booking->receipt)->first();
                $payDeb = new Payment();
                do{
                    $payDeb->receiptno = $func->generateRandomString(10);
                }while($payDeb->receiptno == "" || Payment::where('receiptno', $payDeb->receiptno)->count() == 1 );
                $payDeb->user = $booking->user;
                $payDeb->credit = 0;
                $payDeb->debit = $payment->credit;
                $payDeb->description = "Refund for canceled booking of room ".Room::where('id', $booking->room)->first()->name.", ".Room::where('id', $booking->room)->first()->location;
                $payDeb->paidfor = "Refunding";
                if($payDeb->save()){
                    return $func->backWithMessage("Canceled", "Booking has been Canceled and money has been debited back to Clients account", "info");
                }else{
                    return $func->backWithMessage("Canceled", "But refund could not not be accomplished", "warning");
                }
            }else{
                return $func->backWithMessage("Failed", "System Failure", "error");
            }
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/viewpayments/{range?}', [
        'as' => 'adminViewPayments',
        function($range = 'all'){
            if($range == 'all'){
                $payments = Payment::orderBy('id', 'desc')->paginate(10);
            }elseif ($range == 'day'){
                $payments = Payment::orderBy('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subDay())
                    ->paginate(10);
            }elseif ($range == 'week'){
                $payments = Payment::orderBy('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subWeek())
                    ->paginate(10);
            }elseif ($range == 'month'){
                $payments = Payment::orderBy('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subMonth())
                    ->paginate(10);
            }elseif ($range == 'year'){
                $payments = Payment::orderBy('id', 'desc')
                    ->where('created_at', '>', Carbon::now()->subYear())
                    ->paginate(10);
            }
            return view('admin.adminPayments', [
                'payments' => $payments
            ]);
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/deleteroom/{roomid}', [
        'as' => 'adminDeleteRoom',
        function($roomid){
            $func = new FuncController();
            $bookings = Booking::where('room', $roomid)->get();
            foreach($bookings as $booking){
                $booking->delete();
            }
            $room = Room::where('id', $roomid)->first();
            $room->delete();

            return $func->backWithMessage("Delete", "Room and all its data tree have been deleted", "info");
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/updateroom/{roomid}', [
        'as' => 'adminViewUpdateRoom',
        function($roomid){
            $func = new FuncController();
            $roomRaw = Room::where('id', $roomid);
            if($roomRaw->count() != 1){
                return $func->backWithMessage("Room Not found", "", "error");
            }
            $room = $roomRaw->first();
            return view('admin.adminUpdateRoom', [
                'room' => $room
            ]);
        }
    ])->middleware('auth')->middleware('ca');

    Route::post('admin/postupdateroom', [
        'as' => 'postUpdateRoom',
        function(Request $request){
            $func = new FuncController();
            $room = Room::where('id', $request['roomid'])->first();
            $room->name = $request['name'];
            $room->location = $request['location'];
            $room->ppn = $request['charges'];
            $room->capacity = $request['capacity'];
            $room->theme = $request['theme'];
            $room->size = $request['size'];
            $room->info = $request['info'];
            if($room->update()){
                return $func->backWithMessage("Updated", "", "success");
            }else{
                return $func->backWithMessage("Sorry, and error occurred", "", "error");
            }
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('client/updatepassword', [
        'as' => 'clientUpdatePassword',
        function(){
            return view('client.clientUpdatePassword');
        }
    ])->middleware('auth')->middleware('cc');

    Route::post('client/postUpdatePassword', [
        'as' => 'clientPostUpdatePassword',
        function(Request $request){
            $func = new FuncController();
            $user = Auth::user();

            if($request['newpass'] != $request['conpass']){
                return $func->backWithMessage("Sorry", "Your Password don't match", "error");
            }else{
                if(!Hash::check($request['password'], $user->getAuthPassword())){
                    return $func->backWithMessage("Your Password is incorrect", "", "error");
                }else{
                    $user->password = bcrypt($request['password']);
                    if($user->update()){
                        return $func->backWithMessage("Updated", "Password has been updated", "success");
                    }else{
                        return $func->backWithMessage("Sorry", "We could not update your password", "error");
                    }
                }
            }
        }
    ])->middleware('auth')->middleware('cc');

    Route::get('admin/roomandhistory/{roomid}', [
        'as' => 'adminViewRoomAndHistory',
        function($roomid){
            $func = new FuncController();
            $roomRaw = Room::where('id', $roomid);
            if($roomRaw->count() == 0){
                return $func->backWithMessage("Sorry", "Room not found", "error");
            }
            $room = $roomRaw->first();
            return view('admin.adminViewRoomData', [
                'room' => $room,
                'bookings' => Booking::where('room', $room->id)->paginate(10)
            ]);
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/viewclients', [
        'as' => 'adminViewClients',
        function(){
            $clients = User::where('usertype', 'client')->paginate(10);
            return view('admin.adminViewClients', [
                'clients' => $clients
            ]);
        }
    ])->middleware('auth')->middleware('ca');

    Route::get('admin/resetclientpassword/{client}', [
        'as' => 'resetClientPassword',
        function($client){
            $func = new FuncController();
            $userRaw = User::where('usertype', 'client')->where('id', $client);
            if($userRaw->count() != 1){
                return $func->backWithMessage("Client not found", "", "error");
            }else{
                $user = $userRaw->first();
                $user->password = bcrypt($user->phone);
                if($user->update()){
                    return $func->backWithMessage("Updated", "Password has been reset to users phone number", "success");
                }else{
                    return $func->backWithMessage("An error occurred while resetting password", "", "error");
                }
            }
        }
    ])->middleware('auth')->middleware('ca');

