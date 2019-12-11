<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Model\City;
use App\Model\CMS;
use App\Model\DocumentType;
use App\Model\FAQQuestion;
use App\Model\OTP;
use App\Model\State;
use App\Model\User;
use App\Model\UserDocument;
use App\Model\UserRole;
use App\Model\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//controller to display website content
class WebsiteController extends Controller {
	/**
	 * Display home page
	 *
	 * @return view
	 */
	public function index() {
		return view('website.home');
	}

	/**
	 * Display about us page
	 *
	 * @return view
	 */
	public function about() {
		$page = CMS::where('slug', 'about-us')->first();
		return view('website.about_us', compact('page'));
	}
	public function contact() {

		return view('website.contact');
	}
	public function pageContent(Request $request) {
		$has_valid_slug = $request->segments(1);
		$page = CMS::where('slug', $has_valid_slug[0])->first();
		return view('website.common', compact('page'));
	}

	/**
	 * Display faq page
	 *
	 * @return view
	 */
	public function faq() {
		$page = CMS::where('slug', 'faq')->first();
		$faqs = FAQQuestion::where('status', 'AC')->get();
		return view('website.faq', compact('page', 'faqs'));
	}

	/**
	 * Display terms & conditions page
	 *
	 * @return view
	 */
	public function terms() {
		$page = CMS::where('slug', 'terms-and-conditions')->first();
		return view('website.terms', compact('page'));
	}
	public function refund() {
		$page = CMS::where('slug', 'refund-and-cancellation')->first();
		return view('website.terms', compact('page'));
	}

	public function privacy() {
		$page = CMS::where('slug', 'privacy-policy')->first();
		return view('website.privacy', compact('page'));
	}

	/**
	 * Display user agreement page
	 *
	 * @return view
	 */
	public function agreement() {
		$page = CMS::where('slug', 'user-agreement')->first();
		return view('website.user_agreement', compact('page'));
	}

	/**
	 * Display thanks page
	 *
	 * @return view
	 */
	public function thanks() {
		return view('website.thanks');
	}

	/**
	 * Display driver signup page
	 *
	 * @return view
	 */
	public function driverSignup(Request $request) {
		$states = State::whereHas('country', function ($query) {
			$query->where('name', 'India');
		})->pluck('name');

		if ($request->isMethod('post')) {

			$validate = Validator($request->all(), [
				'firstname' => 'required|string',
				'lastname' => 'required|string',
				'email' => 'required|email',
				'password' => 'required|confirmed',
				'state' => 'required',
				'city' => 'required',
				'reg_num' => 'required|string',
				'num_plate' => 'required|string',
				'vehicle_type' => 'required',
				'vehicle_manufacturer' => 'required|string',
				'vehicle_model' => 'required|string',
				'vehicle_color' => 'required|string',
				'profile' => 'required|mimes:jpeg,png,jpg,gif,svg',
				'license' => 'required|mimes:jpeg,png,jpg,gif,svg,doc,docx,pdf',
				'voter' => 'required|mimes:jpeg,png,jpg,gif,svg,doc,docx,pdf',
				'aadhar' => 'required|mimes:jpeg,png,jpg,gif,svg,doc,docx,pdf',
				'rc' => 'required|mimes:jpeg,png,jpg,gif,svg,doc,docx,pdf',
				'insurance' => 'required|mimes:jpeg,png,jpg,gif,svg,doc,docx,pdf',
			]);

			$attr = [
				'firstname' => 'First Name',
				'lastname' => 'Last Name',
				'email' => 'Email Address',
				'password' => 'Password',
				'state' => 'State',
				'city' => 'City',
				'reg_num' => 'Vehicle Registration Number',
				'num_plate' => 'Vehicle Number Plate',
				'vehicle_type' => 'Vehicle',
				'vehicle_manufacturer' => 'Vehicle Manufacturer',
				'vehicle_model' => 'Vehicle Model',
				'vehicle_color' => 'Vehicle Color',
				'profile' => 'Profile Picture',
				'license' => 'License',
				'voter' => 'Voter ID Card',
				'aadhar' => 'Aadhar Card',
				'rc' => 'RC',
				'insurance' => 'Insurance',
			];

			$validate->setAttributeNames($attr);
			if ($validate->fails()) {
				return redirect()->route('driverSignup')->withInput($request->all())->withErrors($validate);
			} else {
				// dd($request->all());Session::get('mobile_number')
				$driver = new User;
				$driver->name = $request->firstname . " " . $request->lastname;
				$driver->email = $request->email;
				$driver->mobile_number = Session::get('mobile_number');
				$driver->password = bcrypt($request->password);
				$driver->state = $request->state;
				$driver->city = $request->city;
				$driver->registration_number = $request->reg_num;
				$driver->number_plate = $request->num_plate;
				$driver->vehicle_manufacturer = $request->vehicle_manufacturer;
				$driver->vehicle_model = $request->vehicle_model;
				$driver->vehicle_type_id = $request->vehicle_type;
				$driver->vehicle_color = $request->vehicle_color;
				$driver->referral_code = Helper::generateNumber("users", "referral_code");
				$driver->created_at = date('Y-m-d H:i:s');

				$driver->identity_verification = 1;
				$driver->vehicle_verification = 1;
				$driver->document_verification = 1;

				$image = $request->file('profile');
				$profile = time() . $image->getClientOriginalName();
				$image->move(public_path('uploads/profiles'), $profile);
				$newprofile = time() . $profile;

				copy(public_path('uploads/profiles/') . $profile, public_path('uploads/profiles/') . $newprofile);
				unlink(public_path('uploads/profiles/' . $profile));

				$driver->profile_picture = $newprofile;

				if ($driver->save()) {
					$user_role = new UserRole();
					$user_role->role_id = 3;
					$user_role->user_id = $driver->id;
					$user_role->save();
					$doctype = DocumentType::where('status', 'AC')->pluck('id', 'document_type');

					$image = $request->file('license');
					$license = time() . $image->getClientOriginalName();
					$image->move(public_path('uploads/documents/' . $driver->id), $license);
					$newlicense = time() . $license;
					copy(public_path('uploads/documents/' . $driver->id . '/') . $license, public_path('uploads/documents/' . $driver->id . '/') . $newlicense);
					unlink(public_path('uploads/documents/' . $driver->id . '/' . $license));

					$userdoc = new UserDocument;
					$userdoc->user_id = $driver->id;
					$userdoc->document_type_id = $doctype['Driving License'];
					$userdoc->document_name = $newlicense;
					$userdoc->created_at = date('Y-m-d H:i:s');
					$userdoc->save();

					$image = $request->file('voter');
					$voter = time() . $image->getClientOriginalName();
					$image->move(public_path('uploads/documents/' . $driver->id), $voter);
					$newvoter = time() . $voter;
					copy(public_path('uploads/documents/' . $driver->id . '/') . $voter, public_path('uploads/documents/' . $driver->id . '/') . $newvoter);
					unlink(public_path('uploads/documents/' . $driver->id . '/' . $voter));

					$userdoc = new UserDocument;
					$userdoc->user_id = $driver->id;
					$userdoc->document_type_id = $doctype['ID Proof'];
					$userdoc->document_name = $newlicense;
					$userdoc->created_at = date('Y-m-d H:i:s');
					$userdoc->save();

					$image = $request->file('aadhar');
					$aadhar = time() . $image->getClientOriginalName();
					$image->move(public_path('uploads/documents/' . $driver->id), $aadhar);
					$newaadhar = time() . $aadhar;
					copy(public_path('uploads/documents/' . $driver->id . '/') . $aadhar, public_path('uploads/documents/' . $driver->id . '/') . $newaadhar);
					unlink(public_path('uploads/documents/' . $driver->id . '/' . $aadhar));

					$userdoc = new UserDocument;
					$userdoc->user_id = $driver->id;
					$userdoc->document_type_id = $doctype['Address Proof'];
					$userdoc->document_name = $newlicense;
					$userdoc->created_at = date('Y-m-d H:i:s');
					$userdoc->save();

					$image = $request->file('rc');
					$rc = time() . $image->getClientOriginalName();
					$image->move(public_path('uploads/documents/' . $driver->id), $rc);
					$newrc = time() . $rc;
					copy(public_path('uploads/documents/' . $driver->id . '/') . $rc, public_path('uploads/documents/' . $driver->id . '/') . $newrc);
					unlink(public_path('uploads/documents/' . $driver->id . '/' . $rc));

					$userdoc = new UserDocument;
					$userdoc->user_id = $driver->id;
					$userdoc->document_type_id = $doctype['RC'];
					$userdoc->document_name = $newlicense;
					$userdoc->created_at = date('Y-m-d H:i:s');
					$userdoc->save();

					$image = $request->file('insurance');
					$insurance = time() . $image->getClientOriginalName();
					$image->move(public_path('uploads/documents/' . $driver->id), $insurance);
					$newinsurance = time() . $insurance;
					copy(public_path('uploads/documents/' . $driver->id . '/') . $insurance, public_path('uploads/documents/' . $driver->id . '/') . $newinsurance);
					unlink(public_path('uploads/documents/' . $driver->id . '/' . $insurance));

					$userdoc = new UserDocument;
					$userdoc->user_id = $driver->id;
					$userdoc->document_type_id = $doctype['Insurance'];
					$userdoc->document_name = $newlicense;
					$userdoc->created_at = date('Y-m-d H:i:s');
					$userdoc->save();

					return redirect()->route('thanks');
				} else {
					$request->session()->flash('error', 'Some error occurred while registeration. Please try again later.');
					return redirect()->route('driverSignup')->withInput($request->all());
				}
			}
		}
		return view('website.driver_signup', compact('states'));
	}

	/**
	 * fetch cities from state selected in driver signup form
	 *
	 * @return cities list
	 */
	public function getDriverCity(Request $request) {
		$cities = City::whereHas('state', function ($query) use ($request) {
			$query->where('name', $request->state);
		})->pluck('name');

		echo json_encode($cities);
	}

	/**
	 * fetch vehicles from state, city selected in driver signup form
	 *
	 * @return vehicles list
	 */
	public function getDriverVehicle(Request $request) {
		$vehicles = VehicleType::select('id', 'vehicle_type')->where('state', $request->state)->where('city', $request->city)->where('status', 'AC')->get();
		echo json_encode($vehicles);
	}

	/**
	 * Display mobile verification
	 *
	 * @return view
	 */
	public function mobileVerify() {
		return view('website.mobile_verify');
	}

	/**
	 * Display mobile verification
	 *
	 * @return view
	 */
	public function otpVerify(Request $request) {
		$validate = Validator($request->all(), [
			'number' => 'required|digits:10',
		]);

		$attr = [
			'number' => 'Mobile Number',
		];
		$validate->setAttributeNames($attr);

		if ($validate->fails()) {
			echo json_encode(["status" => 0, 'message' => 'Invalid Mobile Number']);
		} else {
			$user = User::where('mobile_number', $request->number)->count();
			if ($user <= 0) {
				$num = mt_rand(1000, 9999);
				if ($this->send_sms($request->number, $num) == 'success') {
					$otp = new OTP();
					// if (!isset($otp->id)) {
					// 	$otp = new OTP;
					// }
					$number = $request->number;
					$otp->mobile_number = $request->number;
					$otp->otp = $num;
					Session::put('mobile_number', $request->number);
					if ($otp->save()) {
						echo json_encode(['status' => 1, 'message' => view('website.otp_verify', compact('number'))->render(), 'number' => $request->number]);
					} else {
						echo json_encode(["status" => 0, 'message' => 'Some error occurred while verifying the mobile number. Please try again later.', 'number' => $request->number]);
					}
				} else {
					echo json_encode(["status" => 0, 'message' => 'Some error occurred while verifying the mobile number. Please try again later.', 'number' => $request->number]);
				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'This mobile number already exists.', 'number' => $request->number]);
			}

		}

	}

	/**
	 * Verify OTP
	 *
	 * @return view
	 */
	public function verifyOTP(Request $request) {
		// dd($request->all());
		$validate = Validator($request->all(), [
			'otp' => 'required|digits:4',
			'number' => 'required|digits:10',
		]);

		$attr = [
			'otp' => 'OTP',
			'number' => 'Mobile Number',
		];
		$validate->setAttributeNames($attr);
		$number = $request->number;
		if ($validate->fails()) {
			echo json_encode(["status" => 0, 'message' => 'Invalid OTP']);
		} else {
			$otp = OTP::where('mobile_number', $request->number)->where('status', 'AC')->orderBy('id', 'desc')->first();
			if (isset($otp->id)) {
				if ($otp->otp == $request->otp) {
					echo json_encode(['status' => 1]);
				} else {
					echo json_encode(["status" => 2, 'message' => view('website.otp_verify', compact('number'))->render(), 'number' => $request->number]);
				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'We have not received any OTP request from this mobile number.', 'number' => $request->number]);
			}

		}

	}

	//send otp to mobile number for verification
	public function send_sms($number, $otp) {
		$apiKey = urlencode('LSBln894RkE-fSnXtlWEXKHW0dPTM37O07RMxSyyvK');
		$msg = $otp . " is your verification OTP for registering into the NXG Charge app. Do not share this OTP with anyone.";
		// Message details
		$numbers = array($number);
		$sender = urlencode('RIGORI');
		$message = rawurlencode($msg);

		$numbers = implode(',', $numbers);

		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

		// Send the POST request with cURL
		$ch = curl_init('https://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);
		// print_r(json_decode($response));die();
		curl_close($ch);
		return json_decode($response)->status;

		// Process your response here
		// echo $response;
		// die("-1-");
	}

	public function admintemp() {
		return view('emails.admin_notify');
	}
	public function usertemp() {
		return view('emails.user_registration');
	}
	public function drivertemp() {
		return view('emails.driver_registration');
	}
	public function invoicetemp() {
		return view('emails.invoice');
	}
	public function driverinvoicetemp() {
		return view('emails.driver_invoice');
	}
}
