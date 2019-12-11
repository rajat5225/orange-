<?php
namespace App\Http\Controllers;
use App\Model\Booking;
use App\Model\ShareRide;
use App\Model\User;
use App\Model\UserView;
use Auth;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Mail;

class LoginController extends Controller {

	public function index(Request $request) {
		if (Auth::check()) {
			if (Auth::user()->user_role[0]->role == 'admin') {
				DB::enableQueryLog();
				$city = Session::get('globalCity');
				$state = Session::get('globalState');
				$country = Session::get('globalCountry');
				$user = UserView::where("role", "user");
				$driver = UserView::where("role", "driver");
				$driverList = UserView::where("role", "driver");
				$booking = Booking::whereIn("booking_status", array(4, 5, 7));
				$shareRide = ShareRide::join("bookings", "bookings.id", "=", "share_rides.booking_id");
				$finished = Booking::whereIn("booking_status", array(5, 7));
				$cancelled = Booking::where("booking_status", 4);
				$scheduled = Booking::where("booking_status", 6);
				$driverBooking = UserView::select("v_users.user_id", "v_users.name", "v_users.email", "v_users.mobile_number", "bookings.id", "bookings.id", DB::raw("count(1) as total_bookings"))->join("bookings", "bookings.driver_id", "=", "v_users.user_id")->whereIn("bookings.booking_status", array(5, 7))->groupBy("bookings.driver_id")->orderBy("total_bookings", "desc");
				if ($city != "all") {
					$user->where("city", $city);
					$driver->where("city", $city);
					$driverList->where("city", $city);
					$booking->where("city", $city);
					$shareRide->where("bookings.city", $city);
					$finished->where("city", $city);
					$cancelled->where("city", $city);
					$scheduled->where("city", $city);
					$driverBooking->where("bookings.city", $city);
				} else {
					if ($state != "all") {
						$user->where("state", $state);
						$driver->where("state", $state);
						$driverList->where("state", $state);
						$booking->where("state", $state);
						$shareRide->where("bookings.state", $state);
						$finished->where("state", $state);
						$cancelled->where("state", $state);
						$scheduled->where("state", $state);
						$driverBooking->where("bookings.state", $state);
					} else {
						if ($country != "all") {
							$user->where("country", $country);
							$driver->where("country", $country);
							$driverList->where("country", $country);
							$booking->where("country", $country);
							$shareRide->where("bookings.country", $country);
							$finished->where("country", $country);
							$cancelled->where("country", $country);
							$scheduled->where("country", $country);
							$driverBooking->where("bookings.country", $country);
						}
					}
				}
				// echo "<pre>";
				// print_r(DB::getQueryLog());
				$user = $user->count();
				$driver = $driver->count();
				$driverList = $driverList->orderBy("user_id", "desc")->limit(10)->get();
				$booking = $booking->count();
				$shareRide = $shareRide->count();
				$finished = $finished->count();
				$cancelled = $cancelled->count();
				$scheduled = $scheduled->count();
				$driverBooking = $driverBooking->limit(10)->get();
				return view('dashboard', compact('user', 'driver', 'booking', 'driverList', 'shareRide', 'finished', 'cancelled', 'scheduled', 'driverBooking'));
			} else {
				$request->session()->flash('error', "You don't have permission to access this panel");
				return view('index');
			}
		}
		return view('index');
	}

	public function login(Request $request) {

		$credentials = ['email' => $request->post('val-email'), 'password' => $request->post('val-pass')];

		$udetail = User::with('user_role')->where('email', $request->post('val-email'))->first();

		if (isset($udetail->user_role) && count($udetail->user_role) > 0) {
			$remember = ($request->post('val-remember') == "on") ? true : false;
			if ($udetail->user_role[0]->role == 'admin') {
				if (Auth::attempt($credentials, $remember)) {
					Session::put('globalCity', 'all');
					Session::put('globalState', 'all');
					Session::put('globalCountry', 'all');
					Session::put('lastlogin', time());
					Session::put('currency', config('constants.currency'));

					$request->session()->flash('success', 'You are logged In.');
					return redirect()->route('index');
				} else {
					$request->session()->flash('error', 'Fill correct credentials');
					return redirect()->route('index');
				}
			} else {
				$request->session()->flash('error', "You don't have permission to access this panel");
				return redirect()->route('index');
			}
		} else {
			$request->session()->flash('error', 'Email ID does not exists.');
			return redirect()->route('index');
		}
	}

	public function sendPasswordMail(Request $request) {
		if ($request->isMethod('post')) {
			$user = User::where('email', $request->post('val-email'))->first();

			if ($user) {
				$user->forgot_key = base64_encode($user->email);
				$user->save();
				$sender_id = Config::get('constants.MAIL_SENDER_ID');
				$data = array('resetpassword' => route('resetGet') . '?id=' . base64_encode($user->email));
				Mail::send('emails.forgot_password', $data, function ($message) use ($user, $sender_id) {
					$message->to($user->email, $user->name)->subject('Forgot Password');
					$message->from($sender_id, 'NXG Charge');
				});
				$request->session()->flash('success', 'Password Reset Link sent on your mail id.');
				return redirect()->route('index');
			} else {
				$request->session()->flash('danger', 'Email ID does not exists.');
				return view('forgot');
			}
		}
		return view('forgot');
	}

	public function resetPassword(Request $request) {
		if ($request->isMethod('post')) {
			$user = User::where('email', $request->post('val-email'))->first();

			if ($user) {

				if ($request->post('val-password') == $request->post('val-confirm-password')) {
					$user->password = bcrypt($request->post('val-password'));
					$user->forgot_key = "";
					if ($user->save()) {
						$request->session()->flash('success', 'Password changed successfully.');
						return redirect()->route('index');
					} else {
						$request->session()->flash('danger', 'Password not changed! Try again later.');
						return view('reset');
					}
				} else {
					$request->session()->flash('danger', 'Passwords do not match.');
					return view('reset');
				}
			} else {
				$request->session()->flash('danger', 'Email ID does not exists.');
				return view('reset');
			}
		}
		$id = $request->get('id');
		if (isset($id) && $id != "") {
			$user = User::where('email', base64_decode($request->get('id')))->first();

			if ($user) {
				if ($user->forgot_key != "" && $user->forgot_key == base64_encode($user->email)) {
					$email = base64_decode($request->get('id'));
					return view('reset', compact('email'));
				} else {
					$request->session()->flash('danger', 'Reset Link expired.');
					return view('forgot');
				}

			} else {
				$request->session()->flash('danger', 'Email ID does not exists.');
				return view('forgot');
			}
		} else {
			$request->session()->flash('danger', 'Email ID does not exists.');
			return view('forgot');
		}
	}

}