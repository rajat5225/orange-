<?php

namespace App\Http\Controllers;
use App\Library\Helper;
use App\Library\ResponseMessages;
use App\Library\SendMail;
use App\Model\Booking;
use App\Model\BookingRoute;
use App\Model\BookingSupport;
use App\Model\City;
use App\Model\Complement;
use App\Model\Country;
use App\Model\CouponCode;
use App\Model\CronJobLog;
use App\Model\DocumentType;
use App\Model\FavLocation;
use App\Model\OTP;
use App\Model\Rating;
use App\Model\ReferrerUser;
use App\Model\ShareRide;
use App\Model\State;
use App\Model\SupportSubject;
use App\Model\TrustedContact;
use App\Model\User;
use App\Model\UserCouponCode;
use App\Model\UserDocument;
use App\Model\UserDriver;
use App\Model\UserRole;
use App\Model\UserView;
use App\Model\VehicleType;
use App\Model\Wallet;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController extends MyController {

	// function called to check mobile number exist or not
	public function isMobileNumberExist(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("mobile_number", "user_type"));
		try {
			// check mobile number exist or not
			if (!$user = UserView::where("mobile_number", $request->mobile_number)->first()) {
				// generate random number
				$otp_number = rand(1111, 9999);
				// Call send_sms to send sms otp
				$this->send_sms($request->mobile_number, $otp_number);
				$otp = new OTP();
				$otp->mobile_number = $request->mobile_number;
				$otp->otp = $otp_number;
				$otp->save();
				$this->response = array(
					"status" => 218,
					"message" => ResponseMessages::getStatusCodeMessages(11),
					"otp" => $otp_number,
					"mobile_number" => $request->mobile_number,
				);
			} else {
				// check user role is not same as given role
				if ($request->user_type == $user->role) {
					$this->response = array(
						"status" => 217,
						"message" => ResponseMessages::getStatusCodeMessages(217),
						"mobile_number" => $request->mobile_number,
					);
				} else {
					// if role is driver then give driver error message
					if ($user->role == "driver") {
						$this->response = array(
							"status" => 320,
							"message" => ResponseMessages::getStatusCodeMessages(320),
						);
					} else {
						$this->response = array(
							"status" => 319,
							"message" => ResponseMessages::getStatusCodeMessages(319),
						);
					}
				}
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		$this->shut_down();
	}

	// function called if user wants to reset password
	public function forgotPassword(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("mobile_number", "user_type"));
		try {
			// check mobile number exist or not
			if ($user = UserView::where("mobile_number", $request->mobile_number)->first()) {
				// check user role is not same as given role
				if ($request->user_type == $user->role) {
					// generate random number
					$otp_number = rand(1111, 9999);
					// Call send_sms to send sms otp
					$this->send_sms_forgot($request->mobile_number, $otp_number);
					$otp = new OTP();
					$otp->mobile_number = $request->mobile_number;
					$otp->otp = $otp_number;
					$otp->save();
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(11),
						"otp" => $otp_number,
						"mobile_number" => $request->mobile_number,
					);
				} else {
					// if role is driver then give driver error message
					if ($user->role == "driver") {
						$this->response = array(
							"status" => 242,
							"message" => ResponseMessages::getStatusCodeMessages(242),
						);
					} else {
						$this->response = array(
							"status" => 241,
							"message" => ResponseMessages::getStatusCodeMessages(241),
						);
					}
				}
			} else {
				$this->response = array(
					"status" => 239,
					"message" => ResponseMessages::getStatusCodeMessages(239),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to login
	public function login(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("mobile_number", "password", "user_type", "device_id", "device_token", "device_type"));
		// try {
		// check mobile number or password exist
		if (Auth::attempt(["mobile_number" => $request->mobile_number, "password" => $request->password])) {
			// check user role same as given role
			if (Auth::user()->status == 'AC') {
				if (Auth::user()->user_role[0]->role == $request->user_type) {
					$user = Auth::user();
					$user->device_id = $request->device_id;
					$user->device_token = $request->device_token;
					$user->device_type = $request->device_type;
					$user->save();
					// get user data
					// DB::enableQueryLog();
					$userData = User::select("users.*", "vehicle_types.vehicle_type", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"), DB::raw("(select currency_symbol from countries where countries.name = users.country) as currency_symbol"))->leftJoin("vehicle_types", "users.vehicle_type_id", "=", "vehicle_types.id")->where("users.id", Auth::user()->id)->first();
					// print_r(DB::getQueryLog());
					// die;
					// check if role is driver
					if (Auth::user()->user_role[0]->role == "driver") {
						// check all verification has done
						if (Auth::user()->identity_verification == 1 && Auth::user()->vehicle_verification == 1 && Auth::user()->document_verification == 1) {
							// check user if verfied or not
							if (Auth::user()->is_verified) {
								$this->response = [
									'status' => 200,
									'message' => ResponseMessages::getStatusCodeMessages(107),
									'refer_message' => $this->getReferMessage(Auth::user()->id),
									'data' => $userData,
								];
							} else {
								$this->response = array(
									"status" => 106,
									"message" => ResponseMessages::getStatusCodeMessages(106),
								);
							}
						} elseif (Auth::user()->identity_verification == 0) {
							// check identity_verification is verified
							$this->response = [
								'status' => 311,
								'message' => ResponseMessages::getStatusCodeMessages(311),
								'data' => $userData,
							];
						} elseif (Auth::user()->vehicle_verification == 0) {
							// check vehicle_verification is verified
							$this->response = [
								'status' => 312,
								'message' => ResponseMessages::getStatusCodeMessages(312),
								'data' => $userData,
							];
						} elseif (Auth::user()->document_verification == 0) {
							// check document_verification is verified
							// get all documents ids for required
							$documentType = DocumentType::select(DB::raw("group_concat(id) as ids"))->where("status", "AC")->get();
							// get all user document ids
							$userDocumentType = UserDocument::select(DB::raw("group_concat(distinct document_type_id) as ids"))->where("user_id", Auth::user()->id)->where("status", "AC")->get();
							// get difference from both user and required documents
							$diff = (array_diff(explode(",", $documentType[0]->ids), explode(",", $userDocumentType[0]->ids)));
							// check if difference is greater than 0
							if (count($diff) > 0) {
								$this->response = [
									'status' => 236,
									'message' => ResponseMessages::getStatusCodeMessages(236),
									'data' => $userData,
								];
							} else {
								$this->response = [
									'status' => 313,
									'message' => ResponseMessages::getStatusCodeMessages(313),
									'data' => $userData,
								];
							}
						} else {
							$this->response = [
								'status' => 314,
								'message' => ResponseMessages::getStatusCodeMessages(314),
								'data' => $userData,
							];
						}
					} else {
						// check user is verified or not
						if (Auth::user()->is_verified) {
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(107),
								'refer_message' => $this->getReferMessage(Auth::user()->id),
								'data' => $userData,
							);
						} else {
							$this->response = array(
								"status" => 106,
								"message" => ResponseMessages::getStatusCodeMessages(106),
							);
						}
					}
				} else {
					// check role is driver or not
					if (Auth::user()->user_role[0]->role == "driver") {
						$this->response = array(
							"status" => 320,
							"message" => ResponseMessages::getStatusCodeMessages(320),
						);
					} else {
						$this->response = array(
							"status" => 319,
							"message" => ResponseMessages::getStatusCodeMessages(319),
						);
					}
				}
			} else {
				$this->response = array(
					"status" => 320,
					"message" => ResponseMessages::getStatusCodeMessages(216),
				);
			}
		} else {
			$this->response = array(
				"status" => 108,
				"message" => ResponseMessages::getStatusCodeMessages(108),
			);
		}
		// } catch (\Exception $ex) {
		// 	$this->response = array(
		// 		"status" => 501,
		// 		"message" => ResponseMessages::getStatusCodeMessages(501),
		// 	);
		// }

		$this->shut_down();
	}

	// function called to
	public function userRegister(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("name", "email", "mobile_number", "password", "country", "state", "city", "device_id", "device_token", "device_type"));
		try {
			// check moobile number exist or not
			if (!User::where("mobile_number", $request->mobile_number)->first()) {
				$filename = "";
				// check profile_picture key exist or not
				if ($request->hasfile('profile_picture')) {
					$file = $request->file('profile_picture');
					$extension = $file->getClientOriginalExtension();
					$filename = time() . '.' . $extension;
					$file->move('uploads/profiles/', $filename);
				}
				$user = new User();
				$user->referral_code = Helper::generateNumber("users", "referral_code");
				$user->name = $request->name;
				$user->email = $request->email;
				$user->mobile_number = $request->mobile_number;
				$user->password = bcrypt($request->password);
				$user->device_id = $request->device_id;
				$user->device_token = $request->device_token;
				$user->device_type = $request->device_type;
				$user->country = $request->country;
				$user->state = $request->state;
				$user->city = $request->city;
				$user->profile_picture = $filename;
				$user->forgot_key = "";
				$user->is_verified = 1;
				$user->save();
				$user_role = new UserRole();
				$user_role->role_id = 2;
				$user_role->user_id = $user->id;
				$user_role->save();

				// when user registered than send an welcome email to user
				//SendMail::sendWelcomeMail("Welcome to NXG Charge", $user, null, "emails.user_registration");
				// check user has added referral code while registering
				if ($this->getBusRuleRef("refer_user") == 1 && isset($request->referral_code)) {
					if ($prntUser = User::select("id", "wallet_amount")->where("referral_code", $request->referral_code)->first()) {
						$referrerAmount = $this->getBusRuleRef("referrer_amount");
						// create relation between user and referral user
						$ReferrerUser = new ReferrerUser();
						$user->referral_code = Helper::generateNumber("referrer_users", "ref_code");
						$ReferrerUser->user_id = $user->id;
						$ReferrerUser->referred_user_id = $prntUser->id;
						$ReferrerUser->amount = $referrerAmount;
						$ReferrerUser->save();
						// add amount to wallet of registered user
						$wallet = new Wallet();
						$wallet->user_id = $user->id;
						$wallet->referrer_user_id = $ReferrerUser->id;
						$wallet->amount = $referrerAmount;
						$wallet->payment_mode = 'credit';
						$wallet->type = "refer";
						$wallet->save();

						// add amount to wallet of registered user
						$wallet = new Wallet();
						$wallet->user_id = $prntUser->id;
						$wallet->referrer_user_id = $ReferrerUser->id;
						$wallet->amount = $referrerAmount;
						$wallet->payment_mode = 'credit';
						$wallet->type = "refer";
						$wallet->save();

						$user->wallet_amount = $referrerAmount;
						$user->save();
						$prntUser->wallet_amount = $prntUser->wallet_amount + $referrerAmount;
						$prntUser->save();
					}
				}

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'refer_message' => $this->getReferMessage($user->id),
					"data" => User::select("*", DB::raw("(select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id) as rating"), DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"))->where("id", $user->id)->first(),
				);
			} else {
				$this->response = array(
					"status" => 105,
					"message" => ResponseMessages::getStatusCodeMessages(105),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to register driver
	public function driverRegister(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("name", "email", "mobile_number", "password", "country", "state", "city", "device_id", "device_token", "device_type", "profile_picture"));
		try {
			// check mobile_number is exist or not
			if (!User::where("mobile_number", $request->mobile_number)->first()) {
				$filename = "";
				// check profile_picture key exist or not
				if ($request->hasfile('profile_picture')) {
					$file = $request->file('profile_picture');
					$extension = $file->getClientOriginalExtension();
					$filename = time() . '.' . $extension;
					$file->move('uploads/profiles/', $filename);
				}
				$user = new User();
				$user->referral_code = Helper::generateNumber("users", "referral_code");
				$user->name = $request->name;
				$user->email = $request->email;
				$user->mobile_number = $request->mobile_number;
				$user->password = bcrypt($request->password);
				$user->device_id = $request->device_id;
				$user->device_token = $request->device_token;
				$user->device_type = $request->device_type;
				$user->country = $request->country;
				$user->state = $request->state;
				$user->city = $request->city;
				$user->profile_picture = $filename;
				$user->identity_verification = 1;
				$user->forgot_key = "";
				$user->is_verified = 1; // To be remove, harry
				$user->save();
				$user_role = new UserRole();
				$user_role->role_id = 3;
				$user_role->user_id = $user->id;
				$user_role->save();
				// send welcome to to driver when registered
				//SendMail::sendWelcomeMail("Welcome to NXG Charge", $user, null, "emails.driver_registration");

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => User::select("*", DB::raw("(select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id) as rating"), DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"))->where("id", $user->id)->first(),
				);
			} else {
				$this->response = array(
					"status" => 105,
					"message" => ResponseMessages::getStatusCodeMessages(105),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to upload driver documents
	public function uploadDocuments(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "document_type_id", "document"));
		try {
			// check user exist or not
			if ($user = User::find($request->user_id)) {
				$filename = "";
				// check document key exist or not
				if ($request->hasfile('document')) {
					$file = $request->file('document');
					$extension = $file->getClientOriginalExtension();
					$filename = time() . '.' . $extension;
					$file->move("uploads/documents/$request->user_id/", $filename);
				}
				// if already uploaded than status change to DL
				if ($UserDocument = UserDocument::where('document_type_id', $request->document_type_id)->where('user_id', $request->user_id)->first()) {
					$UserDocument->status = "DL";
					$UserDocument->save();
				}
				// insert new user document entry in DB
				$UserDocument = new UserDocument();
				$UserDocument->document_type_id = $request->document_type_id;
				$UserDocument->user_id = $user->id;
				if ($filename != "") {
					$UserDocument->document_name = $filename;
				}
				$UserDocument->save();
				if ($UserDocumentA = UserDocument::select(DB::raw("group_concat(DISTINCT document_type_id) as ids"))->where('user_id', $request->user_id)->where("status", "AC")->first()) {
					$DocumentTypeB = DocumentType::select(DB::raw("group_concat(id) as ids"))->where("status", "AC")->first();
					// print_r($UserDocumentA->ids);
					// echo "#";
					// print_r($DocumentTypeB->ids);
					// echo "*";
					$diff = count(array_diff(explode(",", $DocumentTypeB->ids), explode(",", $UserDocumentA->ids)));
					if ($diff == 0) {
						$user->document_verification = 1;
						$user->save();
					}
				}

				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => UserDocument::where("user_id", $user->id)->where("status", "!=", "DL")->get(),
				);
			} else {
				$this->response = array(
					"status" => 214,
					"message" => ResponseMessages::getStatusCodeMessages(214),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to register vehicle
	public function vehicleRegister(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "registration_number", "number_plate", "vehicle_manufacturer", "vehicle_model", "vehicle_type_id", "vehicle_color", "device_id", "device_token", "device_type"));
		try {
			// cehck user exist or not
			if ($user = User::find($request->user_id)) {
				// check vehicle type is already exist or not
				if ($request->vehicle_type_id != 0 || $request->vehicle_type_id != "") {
					$user->registration_number = $request->registration_number;
					$user->number_plate = $request->number_plate;
					$user->vehicle_manufacturer = $request->vehicle_manufacturer;
					$user->vehicle_model = $request->vehicle_model;
					$user->vehicle_type_id = $request->vehicle_type_id;
					$user->vehicle_color = $request->vehicle_color;
					$user->vehicle_verification = 1;
					$user->save();
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(200),
						"data" => User::select("*", DB::raw("(select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id) as rating"), DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"))->where("id", $request->user_id)->first(),
					);
				} else {
					$this->response = array(
						"status" => 222,
						"message" => ResponseMessages::getStatusCodeMessages(222),
					);
				}
			} else {
				$this->response = array(
					"status" => 214,
					"message" => ResponseMessages::getStatusCodeMessages(214),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get user documents
	public function getUserDocuments(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// get user documents if they have status AC
			$userDocumentType = UserDocument::select("document_type_id")->where("user_id", $request->user_id)->where("status", "AC")->get();
			// check user documents exist or not
			if ($userDocumentType->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $userDocumentType,
				);
			} else {
				$this->response = array(
					"status" => 237,
					"message" => ResponseMessages::getStatusCodeMessages(237),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}
	// function called to get user Information
	public function getBookingInfo(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// get user documents if they have status AC
			//DB::enableQueryLog();
			$user = User::select('name', DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"))->where(['id' => $request->user_id, 'status' => 'AC'])->first();
			$driver_booking = Booking::select(DB::raw('COUNT(bookings.id) as total_rides'), DB::raw('ROUND(((SUM(bookings.total_without_tax)*vehicle_types.driver_charge)/100),2) as total_earning'))
				->join('vehicle_types', 'vehicle_types.id', '=', 'bookings.vehicle_type_id')->
				where("driver_id", $request->user_id)->where("bookings.status", "AC")->first();
			$total_complements = Rating::select(DB::raw('COUNT(ratings.id) as total_complements'), DB::raw('round(AVG(ratings.rating)) as total_ratings'))->
				where("parent_id", $request->user_id)->where("ratings.status", "AC")->first();
			$recent_complements = Rating::select(DB::raw('count(ratings.id) as total'), 'complements.name', 'bookings.booking_code', DB::raw("CONCAT('" . url("uploads/complements") . "/', complements.image) image"))->
				join('bookings', 'bookings.id', '=', 'ratings.booking_id')->
				join('complements', 'ratings.complement_id', '=', 'complements.id')->
				where("ratings.parent_id", $request->user_id)
				->where("ratings.complement_id", '!=', NULL)->where("ratings.status", "AC")->orderBy('ratings.id', 'desc')->groupBy('ratings.complement_id')->get();
			//print_r(DB::getQueryLog());
			// check user documents exist or not
			if ($driver_booking->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'user_data' => $user,
					"booking" => $driver_booking,
					"Complements" => $total_complements,
					'Recent_complements' => $recent_complements,
				);
			} else {
				$this->response = array(
					"status" => 13,
					"message" => ResponseMessages::getStatusCodeMessages(13),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}
	// function called to get driver all Compliments
	public function getCompliments(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// get user documents if they have status AC
			//DB::enableQueryLog();

			$all_compliments = Rating::select('complements.name', 'bookings.booking_code', DB::raw('DATE_FORMAT(ratings.created_at, "%Y-%m-%d") as date'), DB::raw("CONCAT('" . url("uploads/complements") . "/', complements.image) image"))->
				join('bookings', 'bookings.id', '=', 'ratings.booking_id')->
				join('complements', 'ratings.complement_id', '=', 'complements.id')->
				where("ratings.parent_id", $request->user_id)
				->where("ratings.complement_id", '!=', NULL)->where("ratings.status", "AC")->orderBy('ratings.id', 'desc')->get();
			//print_r(DB::getQueryLog());
			// check user documents exist or not
			if ($all_compliments->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'data' => $all_compliments,
				);
			} else {
				$this->response = array(
					"status" => 15,
					"message" => ResponseMessages::getStatusCodeMessages(15),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}
	public function getAllBookingInfo(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", 'date'));
		try {
			// get user documents if they have status AC
			//DB::enableQueryLog();
			$driver_booking = Booking::select(DB::raw('COUNT(bookings.id) as total_rides'), DB::raw("(select count(id) from bookings b where payment_mode ='cash' and driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')) as cash_count"), DB::raw("(select count(id) from bookings b where payment_mode ='wallet' and driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')) as wallet_count"))->
				where("driver_id", $request->user_id)->where(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m")'), $request->date)->first();
			// $bookings = Booking::select(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as date'),DB::raw('GROUP_CONCAT(concat(`booking_code`,":",`payment_mode`,":",`total`) separator ",") as booking'))->
			// where("driver_id", $request->user_id)->where("bookings.status", "AC")->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'))->orderBy(DB::raw('YEAR(created_at)','DESC'))->orderBy(DB::raw('MONTH(created_at)'),'DESC')->orderBy(DB::raw('DATE(created_at)'),'DESC')->get();
			$bookings = Booking::select(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m-%d") as date'), 'bookings.booking_code', 'bookings.total', DB::raw("IF ((select count(bookings.id) from bookings where bookings.payment_mode = 'cash' and bookings.driver_id='$request->user_id' and bookings.status='AC') > 0,'cash','wallet') as type"))
				->join('vehicle_types', 'vehicle_types.id', '=', 'bookings.vehicle_type_id')->
				where("bookings.driver_id", $request->user_id)->where("bookings.status", "AC")->where(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m")'), $request->date)->orderBy(DB::raw('YEAR(bookings.created_at)', 'ASC'))->orderBy(DB::raw('MONTH(bookings.created_at)'), 'ASC')->get();
			if ($driver_booking->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'booking' => $driver_booking,
					'data' => $bookings,
				);
			} else {
				$this->response = array(
					"status" => 13,
					"message" => ResponseMessages::getStatusCodeMessages(13),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}
	public function getAllBookingMonthly(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// get user documents if they have status AC
			// DB::enableQueryLog();
			// $driver_booking = Booking::select(DB::raw('DATE_FORMAT(created_at, "%m-%Y") as date'),DB::raw('COUNT(bookings.id) as total_rides'),DB::raw("(select count(id) from bookings where payment_mode ='cash' and driver_id='$request->user_id') as cash_count"),DB::raw("(select count(id) from bookings where payment_mode ='wallet' and driver_id='$request->user_id') as wallet_count"))->
			// where("driver_id", $request->user_id)->where("bookings.status", "AC")->first();
			$bookings = Booking::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'), DB::raw("(select count(id) from bookings b where driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')) as total_rides"), DB::raw("(select count(id) from bookings b where payment_mode ='cash' and driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')) as cash_count"), DB::raw("(select count(id) from bookings b where payment_mode ='wallet' and driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')) as wallet_count"))->
				where("driver_id", $request->user_id)->where("bookings.status", "AC")->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))->orderBy(DB::raw('YEAR(created_at)', 'ASC'))->orderBy(DB::raw('MONTH(created_at)'), 'ASC')->get();

			//dd($bookings);

			// print_r(DB::getQueryLog());
			//dd();
			// check user documents exist or not
			if ($bookings->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'data' => $bookings,
				);
			} else {
				$this->response = array(
					"status" => 13,
					"message" => ResponseMessages::getStatusCodeMessages(13),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}
	public function getMonthlyEarning(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// get user documents if they have status AC
			// DB::enableQueryLog();
			$bookings = Booking::select(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m-%d") as date'), DB::raw("(select sum(total_without_tax) from bookings b where b.payment_mode ='cash' and b.driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')) as cash_total"), DB::raw("COALESCE((select sum(total_without_tax) from bookings b where payment_mode ='wallet' and driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')),0) as wallet_total"), DB::raw('SUM(bookings.total_without_tax) as total_earning'), DB::raw('ROUND(((SUM(bookings.total_without_tax)*vehicle_types.driver_charge)/100),2) as admin_total'))
				->join('vehicle_types', 'vehicle_types.id', '=', 'bookings.vehicle_type_id')->
				where("bookings.driver_id", $request->user_id)->where("bookings.status", "AC")->groupBy(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m")'))->orderBy(DB::raw('YEAR(bookings.created_at)', 'ASC'))->orderBy(DB::raw('MONTH(bookings.created_at)'), 'ASC')->get();
			// print_r(DB::getQueryLog());

			// check user documents exist or not
			if ($bookings->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'data' => $bookings,
				);
			} else {
				$this->response = array(
					"status" => 13,
					"message" => ResponseMessages::getStatusCodeMessages(13),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}
	public function getMonthwiseEarning(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "date"));
		try {
			// get user documents if they have status AC
			// DB::enableQueryLog();
			$bookings = Booking::select(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m-%d") as date'), DB::raw("(select sum(total_without_tax) from bookings b where payment_mode ='cash' and driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')) as cash_total"), DB::raw("COALESCE((select sum(total_without_tax) from bookings b where payment_mode ='wallet' and driver_id='$request->user_id' and DATE_FORMAT(bookings.created_at, '%Y-%m') = DATE_FORMAT(b.created_at, '%Y-%m')),0) as wallet_total"), DB::raw('SUM(bookings.total_without_tax) as total_earning'), DB::raw('ROUND(((SUM(bookings.total_without_tax)*vehicle_types.driver_charge)/100),2) as admin_total'))
				->join('vehicle_types', 'vehicle_types.id', '=', 'bookings.vehicle_type_id')->
				where("bookings.driver_id", $request->user_id)->where("bookings.status", "AC")->where(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m")'), $request->date)->first();
			$allearning = Booking::select(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m-%d") as date'), 'bookings.booking_code', 'bookings.total_without_tax', DB::raw("IF ((select count(bookings.id) from bookings where bookings.payment_mode = 'cash' and bookings.driver_id='$request->user_id' and bookings.status='AC') > 0,'cash','wallet') as type"), DB::raw('ROUND(((bookings.total_without_tax*vehicle_types.driver_charge)/100),2) as total'))
				->join('vehicle_types', 'vehicle_types.id', '=', 'bookings.vehicle_type_id')->
				where("bookings.driver_id", $request->user_id)->where("bookings.status", "AC")->where(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m")'), $request->date)->orderBy(DB::raw('YEAR(bookings.created_at)', 'ASC'))->orderBy(DB::raw('MONTH(bookings.created_at)'), 'ASC')->get();

			// print_r(DB::getQueryLog());

			// check user documents exist or not
			if ($bookings->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'data' => $bookings,
					'earning' => $allearning,
				);
			} else {
				$this->response = array(
					"status" => 13,
					"message" => ResponseMessages::getStatusCodeMessages(13),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to update user/driver profile
	public function updateProfile(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// cehck user exist or not
			if ($user = User::find($request->user_id)) {
				$filename = "";
				// check profile_picture key exist or not
				if ($request->hasfile('profile_picture')) {
					$file = $request->file('profile_picture');
					$extension = $file->getClientOriginalExtension();
					$filename = time() . '.' . $extension;
					$file->move('uploads/profiles/', $filename);
				}
				$user->name = $request->name;
				if ($filename != "") {
					$user->profile_picture = $filename;
				}
				$user->save();
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'refer_message' => $this->getReferMessage($request->user_id),
					"data" => User::select("users.*", "vehicle_types.vehicle_type", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"))->leftJoin("vehicle_types", "users.vehicle_type_id", "=", "vehicle_types.id")->where("users.id", $request->user_id)->first(),
				);
			} else {
				$this->response = array(
					"status" => 214,
					"message" => ResponseMessages::getStatusCodeMessages(214),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get Country list
	public function countryList() {
		try {
			// get all country
			$Country = Country::select("id", "name")->where("status", "AC")->get();
			if ($Country->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $Country,
				);
			} else {
				$this->response = array(
					"status" => 154,
					"message" => ResponseMessages::getStatusCodeMessages(154),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get state list
	public function stateList(Request $request) {
		try {
			// check keys are exist
			$this->checkKeys(array_keys($request->all()), array("country_id"));
			// get state list based on country
			$CountryStates = State::select("id", "name")->where("country_id", $request->country_id)->where("status", "AC")->get();
			if ($CountryStates->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $CountryStates,
				);
			} else {
				$this->response = array(
					"status" => 219,
					"message" => ResponseMessages::getStatusCodeMessages(219),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get city list
	public function cityList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("state_id"));
		try {
			// get city list based on state
			$StateCities = City::select("id", "name")->where("state_id", $request->state_id)->where("status", "AC")->get();
			if ($StateCities->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $StateCities,
				);
			} else {
				$this->response = array(
					"status" => 220,
					"message" => ResponseMessages::getStatusCodeMessages(220),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get cabs list
	public function cabList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("country", "state", "city", "pickup_latitude", "pickup_longitude"));
		try {
			// get cab list based on user's state , city, pickup_latitude and pickup_longitude
			$VehicleType = $this->cabsList($request->country, $request->state, $request->city, $request->pickup_latitude, $request->pickup_longitude);
			// check VehicleType variavle null or not
			if (isset($VehicleType)) {
				if (count($VehicleType) > 0) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(200),
						"data" => $VehicleType,
					);
				} else {
					$this->response = array(
						"status" => 212,
						"message" => ResponseMessages::getStatusCodeMessages(212),
					);
				}
			} else {
				$this->response = array(
					"status" => 212,
					"message" => ResponseMessages::getStatusCodeMessages(212),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get driver list
	public function driverList(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("country", "state", "city", "latitude", "longitude"));
		try {
			// get driver list based on user's state , city, latitude and longitude
			$driver = $this->driversList($request->country, $request->state, $request->city, $request->latitude, $request->longitude);
			// check driver variable null or not
			if (isset($driver)) {
				if (count($driver) > 0) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(200),
						"data" => $driver,
					);
				} else {
					$this->response = array(
						"status" => 212,
						"message" => ResponseMessages::getStatusCodeMessages(212),
					);
				}
			} else {
				$this->response = array(
					"status" => 212,
					"message" => ResponseMessages::getStatusCodeMessages(212),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get required document type
	public function getDocumentType() {
		try {
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => DocumentType::select("id", "document_type")->where("status", "AC")->get(),
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get vehicle type
	public function getVehicleType(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("country", "state", "city", "user_id"));
		try {
			// checkUserActive function called to cehck user active or not
			$this->checkUserActive($request->user_id);
			// get vehicles list based on state, city
			if ($VehicleType = VehicleType::select("*", DB::raw("CONCAT('" . url("uploads/vehicles") . "/', image) image"), DB::raw("CONCAT('" . url("uploads/vehicles") . "/', gray_image) gray_image"), DB::raw("CONCAT('" . url("uploads/vehicles") . "/', aerial_image) aerial_image"))->where("country", $request->country)->where("state", $request->state)->where("city", $request->city)->where("status", "AC")->get()) {
				if ($VehicleType->count() > 0) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(200),
						"data" => $VehicleType,
					);
				} else {
					$this->response = array(
						"status" => 148,
						"message" => ResponseMessages::getStatusCodeMessages(148),
					);
				}
			} else {
				$this->response = array(
					"status" => 212,
					"message" => ResponseMessages::getStatusCodeMessages(212),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to update location of user
	public function updateLocation(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "latitude", "longitude", "device_id"));
		try {
			// check single signon of driver
			$this->checkSingleSignOn($request->user_id, $request->device_id);
			// update driver's latitude and longitude
			if (User::where("id", $request->user_id)->update(["latitude" => $request->latitude, "longitude" => $request->longitude])) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
				);
			} else {
				$this->response = array(
					"status" => 502,
					"message" => ResponseMessages::getStatusCodeMessages(502),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to change password
	public function changePassword(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "password", "new_password", "device_id"));
		try {
			// check single signon of driver
			$this->checkSingleSignOn($request->user_id, $request->device_id);
			// check user id or password correct or not
			if (Auth::attempt(["id" => $request->user_id, "password" => $request->password])) {
				// check password is not same as old password that is in DB
				if (!Hash::check($request->new_password, Auth::user()->password)) {
					Auth::user()->update(["password" => bcrypt($request->new_password)]);
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(200),
					);
				} else {
					$this->response = array(
						"status" => 324,
						"message" => ResponseMessages::getStatusCodeMessages(324),
					);
				}
			} else {
				$this->response = array(
					"status" => 215,
					"message" => ResponseMessages::getStatusCodeMessages(215),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to update password
	public function updatePassword(Request $request) {
		date_default_timezone_set("Asia/Kolkata");
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("mobile_number", "password", "re_password"));
		try {
			// password should be same as repeat password
			if ($request->password == $request->re_password) {
				// check mobile number exist or not
				if ($user = User::where(["mobile_number" => $request->mobile_number])->first()) {
					// password has not same as old password
					if (!Hash::check($request->password, $user->password)) {
						$user->update(["password" => bcrypt($request->password)]);
						$this->response = array(
							"status" => 200,
							"message" => ResponseMessages::getStatusCodeMessages(240),
						);
					} else {
						$this->response = array(
							"status" => 324,
							"message" => ResponseMessages::getStatusCodeMessages(324),
						);
					}
				} else {
					$this->response = array(
						"status" => 239,
						"message" => ResponseMessages::getStatusCodeMessages(239),
					);
				}
			} else {
				$this->response = array(
					"status" => 238,
					"message" => ResponseMessages::getStatusCodeMessages(238),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to update driver's profile visibility
	public function profileVisible(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "is_visible", "device_id"));
		try {
			// check single signon of driver
			$this->checkSingleSignOn($request->user_id, $request->device_id);
			// check user active or not
			$this->checkUserActive($request->user_id);
			// get user data
			$user = User::find($request->user_id);
			$user->is_visible = $request->is_visible;
			$user->save();
			// check visible is on
			if ($request->is_visible == 1) {
				$message = ResponseMessages::getStatusCodeMessages(306);
			} else {
				$message = ResponseMessages::getStatusCodeMessages(307);
			}
			$this->response = array(
				"status" => 200,
				"message" => $message,
				"data" => $user,
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to logout user
	public function logout(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// check user exist or not
			if ($user = User::find($request->user_id)) {
				$user->is_visible = 0;
				$user->device_token = "";
				$user->save();
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(10),
				);
			} else {
				$this->response = array(
					"status" => 214,
					"message" => ResponseMessages::getStatusCodeMessages(214),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get user detail
	public function getUserDetail(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "user_type", "device_id"));
		try {
			// check user active or not
			$this->checkUserActive($request->user_id);
			if ($request->user_type == "driver") {
				// check single signon of driver
				$this->checkSingleSignOn($request->user_id, $request->device_id);
			}
			// check user exist or not
			if ($user = User::find($request->user_id)) {
				$driver_status = "";
				$booking_detail = "";
				// get users last booking
				$last_booking = $this->getLastBooking($request->user_id, $request->user_type);
				// get application information data like app version
				$app_info = $this->getAppInfo();
				// check user type is user
				if ($request->user_type == "user") {
					// get driver status
					$driver_status = $this->driverStatus($request->user_id, "user");
					// get user status
					$user_status = $this->userStatus($request->user_id);
				} else {
					// get driver status
					$driver_status = $this->userStatus($request->user_id);
					// get user status
					$user_status = $this->driverStatus($request->user_id, "driver");
					// get booking detail
					$booking_detail = $this->bookingDetail($request->user_id);
				}
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"user_detail" => $user_status,
					"driver_detail" => $driver_status,
					"last_booking" => $last_booking,
					"booking_detail" => $booking_detail,
					"app_info" => $app_info,
					'refer_message' => $this->getReferMessage($request->user_id),
				);
			} else {
				$this->response = array(
					"status" => 214,
					"message" => ResponseMessages::getStatusCodeMessages(214),
					"data" => $user,
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get user or driver rating
	public function getUserRating(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "user_type", "device_id"));
		try {
			// check user active or not
			$this->checkUserActive($request->user_id);
			if ($request->user_type == "driver") {
				// check single signon of driver
				$this->checkSingleSignOn($request->user_id, $request->device_id);
			}
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => UserView::select("rating")->where("user_id", $request->user_id)->first()->rating,
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to update user rating
	public function userRating(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "user_type", "device_id", "parent_id", "booking_id", "rating"));
		try {
			// check user active or not
			$this->checkUserActive($request->user_id);
			if ($request->user_type == "driver") {
				// check single signon of driver
				$this->checkSingleSignOn($request->user_id, $request->device_id);
			}
			// update user rating based on booking
			if (!$user_rating = Rating::where("booking_id", $request->booking_id)->where("user_id", $request->user_id)->first()) {
				$rating = new Rating();
				$rating->user_id = $request->user_id;
				$rating->parent_id = $request->parent_id;
				$rating->booking_id = $request->booking_id;
				$rating->complement_id = $request->complement_id;
				$rating->rating = $request->rating;
				$rating->comment = ($request->comment) ? $request->comment : "";
				$rating->created_at = date("Y-m-d H:i:s");
				$rating->save();
			} else {
				$user_rating->user_id = $request->user_id;
				$user_rating->parent_id = $request->parent_id;
				$user_rating->booking_id = $request->booking_id;
				$user_rating->complement_id = $request->complement_id;
				$user_rating->rating = $request->rating;
				$user_rating->comment = $request->comment;
				$user_rating->save();
			}
			// update user status to 9
			$user = User::find($request->user_id);
			$user->user_status = 9;
			$user->save();

			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(12),
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get fare estimate
	public function getFareEstimate(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "vehicle_type_id", "pickup_latitude", "pickup_longitude", "dropoff_latitude", "dropoff_longitude", "distance", "time"));
		try {
			// check user active or not
			$this->checkUserActive($request->user_id);
			$dist = ceil($request->distance / 1000); // In KM
			$time = ceil($request->time / 60); // In Minutes
			// get vehicle data based on given vehicle id
			if ($vehicleTypes = VehicleType::select("*", DB::raw("CONCAT('" . url("uploads/vehicles") . "/', vehicle_types.image) image"))->where("id", $request->vehicle_type_id)->first()) {
				$fareCharge = ($dist * $vehicleTypes->price) + $vehicleTypes->base_fare;
				$vehicleTypes->waiting_time = $vehicleTypes->waiting_charge;
				$vehicleTypes->estimate_arrival_time = "$time";
				$vehicleTypes->fare_charge = "$fareCharge";
				$vehicleTypes->pickup_latitude = $request->pickup_latitude;
				$vehicleTypes->pickup_longitude = $request->pickup_longitude;
				$vehicleTypes->dropoff_latitude = $request->dropoff_latitude;
				$vehicleTypes->dropoff_longitude = $request->dropoff_longitude;
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $vehicleTypes,
				);
			} else {
				$this->response = array(
					"status" => 148,
					"message" => ResponseMessages::getStatusCodeMessages(148),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get user's trausted contacts
	public function getTrustedContacts(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// get trusted contacts based on user's id and status
			$trusted = TrustedContact::where("user_id", $request->user_id)->where("status", 'AC')->get();
			// check if trusted contact exist
			if ($trusted->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $trusted,
				);
			} else {
				$this->response = array(
					"status" => 226,
					"message" => ResponseMessages::getStatusCodeMessages(226),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to add user's trusted contact
	public function addTrustedContacts(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "name", "mobile_number"));
		try {
			// get how many trusted contacts added by user
			$trusted = TrustedContact::where("user_id", $request->user_id)->where("status", 'AC')->get();
			// check user's trusted contacts has reached limit
			if ($trusted->count() < $this->getBusRuleRef("trusted_contacts_limit")) {
				// check givedn trusted contact already added or not
				if (!TrustedContact::where("user_id", $request->user_id)->where("status", 'AC')->where("mobile_number", $request->mobile_number)->first()) {
					$TrustedContact = new TrustedContact();
					$TrustedContact->user_id = $request->user_id;
					$TrustedContact->name = $request->name;
					$TrustedContact->mobile_number = $request->mobile_number;
					$TrustedContact->save();
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(225),
						"data" => TrustedContact::where("user_id", $request->user_id)->where("status", 'AC')->get(),
					);
				} else {
					$this->response = array(
						"status" => 224,
						"message" => ResponseMessages::getStatusCodeMessages(224),
						"data" => TrustedContact::where("user_id", $request->user_id)->where("status", 'AC')->get(),
					);
				}
			} else {
				$this->response = array(
					"status" => 223,
					"message" => "Limit exceeded. You can add " . $this->getBusRuleRef("trusted_contacts_limit") . " contacts.",
					"data" => TrustedContact::where("user_id", $request->user_id)->where("status", 'AC')->get(),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to delete trusted contact
	public function deleteContact(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("trusted_id", "user_id"));
		try {
			// check user's trusted contact status to DL
			$trusted = TrustedContact::where("id", $request->trusted_id)->update(["status" => "DL"]);
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(229),
				"data" => TrustedContact::where("user_id", $request->user_id)->where("status", 'AC')->get(),
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get complements list
	public function getComplements(Request $request) {
		try {
			// get complements list data
			$Complement = Complement::select("*", DB::raw("CONCAT('" . url("uploads/complements") . "/', image) image"))->where("status", 'AC')->get();
			if ($Complement->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $Complement,
				);
			} else {
				$this->response = array(
					"status" => 230,
					"message" => ResponseMessages::getStatusCodeMessages(230),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to support subject
	public function getSupportSubject(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// check user active or not
			$this->checkUserActive($request->user_id);
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => SupportSubject::where("status", "AC")->get(),
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to booking support
	public function getBookingSupport(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// get booking support list based on given user
			//DB::enableQueryLog();
			$support = BookingSupport::select("booking_supports.id", "booking_supports.support_code", "bookings.booking_code", "booking_supports.comment", "booking_supports.status", "users.name", "support_subject.subject")->join("bookings", "bookings.id", "=", "booking_supports.booking_id")->join("users", "users.id", "=", "bookings.user_id")->join("support_subject", "support_subject.id", "=", "booking_supports.subject_id")->orderBy("booking_supports.id", "desc")->get();
			// print_r(DB::getQueryLog());
			if ($support->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $support,
				);
			} else {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),

				);

			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to booking support
	public function bookingSupport(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("booking_id", "user_id", "subject_id", "comment"));
		try {
			// add new booking support on booking
			$BookingSupport = new BookingSupport();
			$BookingSupport->support_code = Helper::generateNumber("booking_supports", "support_code");
			$BookingSupport->booking_id = $request->booking_id;
			$BookingSupport->subject_id = $request->subject_id;
			$BookingSupport->comment = $request->comment;
			$BookingSupport->created_at = date("Y-m-d H:i:s");
			$BookingSupport->save();
			// DB::enableQueryLog();
			// get support list based on user
			$support = BookingSupport::select("booking_supports.id", "booking_supports.support_code", "bookings.booking_code", "booking_supports.comment", "booking_supports.status", "users.name", "support_subject.subject")->join("bookings", "bookings.id", "=", "booking_supports.booking_id")->join("users", "users.id", "=", "bookings.user_id")->join("support_subject", "support_subject.id", "=", "booking_supports.subject_id")->orderBy("booking_supports.id", "desc")->get();
			// print_r(DB::getQueryLog());
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => $support,
			);

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get favourite list
	public function getFavLocation(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => FavLocation::where('user_id', $request->user_id)->where('status', 'AC')->get(),
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to add favourite locations
	public function addFavLocation(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "latitude", "longitude", "address", "label"));
		try {
			// status change to DL if given favourite location already has with given label
			FavLocation::where(["user_id" => $request->user_id, "label" => $request->label])->update(["status" => "DL"]);
			$FavLocation = new FavLocation();
			$FavLocation->user_id = $request->user_id;
			$FavLocation->latitude = $request->latitude;
			$FavLocation->longitude = $request->longitude;
			$FavLocation->address = $request->address;
			$FavLocation->label = $request->label;
			$FavLocation->created_at = date("Y-m-d H:i:s");
			$FavLocation->save();
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => FavLocation::where('user_id', $request->user_id)->where('status', 'AC')->get(),
			);

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to delete favourite location
	public function deleteFavLocation(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("favourite_id", "user_id"));
		try {
			// check favourite lication exist or not
			if ($FavLocation = FavLocation::where(["id" => $request->favourite_id, "user_id" => $request->user_id])->where('status', 'AC')->first()) {
				$FavLocation->status = "DL";
				$FavLocation->save();
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(149),
					"data" => FavLocation::where('user_id', $request->user_id)->where('status', 'AC')->get(),
				);
			} else {
				$this->response = array(
					"status" => 151,
					"message" => ResponseMessages::getStatusCodeMessages(151),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to delete user document
	public function deleteUserDocument(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_document_id", "user_id"));
		try {
			// check user document exist or not
			if ($UserDocument = UserDocument::where(["document_type_id" => $request->user_document_id, "user_id" => $request->user_id])->where('status', 'AC')->first()) {
				$UserDocument->status = "DL";
				$UserDocument->save();
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(149),
					"data" => UserDocument::where('user_id', $request->user_id)->where('status', 'AC')->get(),
				);
			} else {
				$this->response = array(
					"status" => 150,
					"message" => ResponseMessages::getStatusCodeMessages(150),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get coupon codes
	public function getCouponCodes(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "country", "state", "city"));
		try {
			// get current data
			$today = date("Y-m-d");
			// coupon code exist based on current date is exist coupon code's end date and state or city
			$CouponCode = CouponCode::select("id", "coupon_code", "title", "description", "terms", "start_date", "end_date", "discount_value")->where("end_date", ">=", $today)->where("country", $request->country)->where("state", $request->state)->where("city", $request->city)->where("status", 'AC')->get();
			if ($CouponCode->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $CouponCode,
				);
			} else {
				$this->response = array(
					"status" => 227,
					"message" => ResponseMessages::getStatusCodeMessages(227),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to check coupon code exist or not
	public function isCouponCodeExist(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "coupon_code", "fare_amount", "country", "state", "city"));
		try {
			DB::enableQueryLog();
			$today = date("Y-m-d");
			// get user's state and city
			$user = User::select("country", "state", "city")->where("id", $request->user_id)->first();
			// get user's coupon code data
			$userCouponCode = UserCouponCode::select('coupon_codes.id')->join("coupon_codes", "coupon_codes.id", "=", "user_coupon_code.coupon_code_id")->where(["user_coupon_code.user_id" => $request->user_id, "coupon_codes.coupon_code" => $request->coupon_code])->get();
			$used = $userCouponCode->count();
			// print_r(DB::getQueryLog());
			// check coupon code exist based on parameter coupon code's no_of_applies, end_date, state, city
			if ($couponCode = CouponCode::where(['coupon_code' => $request->coupon_code, 'country' => $request->country, 'state' => $request->state, 'city' => $request->city])->where("no_of_applies", ">", $used)->where("end_date", ">=", $today)->first()) {
				// print_r(DB::getQueryLog());
				// check fare amount is greater than minimum amount
				if ($request->fare_amount >= $couponCode->min_amount) {
					// check coupon code's discount type is usage
					if ($couponCode->discount_type == "usage") {
						$booking = Booking::select("id")->where("user_id", $request->user_id)->where("booking_status", 7)->count();
						if ($used < $couponCode->no_of_applies) {
							// check minimum rides
							if ($booking >= $couponCode->min_rides) {
								$UserCouponCode = new UserCouponCode();
								$UserCouponCode->user_id = $request->user_id;
								$UserCouponCode->coupon_code_id = $couponCode->id;
								$UserCouponCode->save();
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(233),
									"coupon_code_id" => $couponCode->id,
									"coupon_code" => $request->coupon_code,
								);
							} else {
								$this->response = array(
									"status" => 232,
									"message" => ResponseMessages::getStatusCodeMessages(232),
								);
							}
						} else {
							$this->response = array(
								"status" => 232,
								"message" => ResponseMessages::getStatusCodeMessages(248),
							);
						}
					} elseif ($couponCode->discount_type == "rides") {
						// DB::enableQueryLog();
						$oldCouponCode = CouponCode::select("user_coupon_code.id", "user_coupon_code.coupon_code_id", "coupon_codes.start_date", "coupon_codes.end_date", "coupon_codes.no_of_rides", DB::raw("(select count(1) from bookings where user_id = user_coupon_code.user_id and DATE_FORMAT(created_at,'%Y%m%d') BETWEEN coupon_codes.start_date and coupon_codes.end_date and coupon_code_id = coupon_codes.id) as total"))->join("user_coupon_code", "user_coupon_code.coupon_code_id", "=", "coupon_codes.id")->where(['coupon_codes.discount_type' => "rides"])->where("end_date", ">=", $today)->where("user_coupon_code.user_id", $request->user_id)->first();
						// echo "MTG=";
						// print_r(DB::getQueryLog());
						if ($oldCouponCode) {
							if ($oldCouponCode->total == $oldCouponCode->no_of_rides) {
								$UserCouponCode = new UserCouponCode();
								$UserCouponCode->user_id = $request->user_id;
								$UserCouponCode->coupon_code_id = $couponCode->id;
								$UserCouponCode->save();
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(233),
									"coupon_code_id" => $couponCode->id,
									"coupon_code" => $request->coupon_code,
								);
							} else {
								$this->response = array(
									"status" => 232,
									"message" => ResponseMessages::getStatusCodeMessages(249),
								);
							}
						} else {
							if ($used < $couponCode->no_of_applies) {
								$UserCouponCode = new UserCouponCode();
								$UserCouponCode->user_id = $request->user_id;
								$UserCouponCode->coupon_code_id = $couponCode->id;
								$UserCouponCode->save();
								$this->response = array(
									"status" => 200,
									"message" => ResponseMessages::getStatusCodeMessages(233),
									"coupon_code_id" => $couponCode->id,
									"coupon_code" => $request->coupon_code,
								);
							} else {
								$this->response = array(
									"status" => 232,
									"message" => ResponseMessages::getStatusCodeMessages(248),
								);
							}
						}

					} else {
						if ($used < $couponCode->no_of_applies) {
							$UserCouponCode = new UserCouponCode();
							$UserCouponCode->user_id = $request->user_id;
							$UserCouponCode->coupon_code_id = $couponCode->id;
							$UserCouponCode->save();
							$this->response = array(
								"status" => 200,
								"message" => ResponseMessages::getStatusCodeMessages(233),
								"coupon_code_id" => $couponCode->id,
								"coupon_code" => $request->coupon_code,
							);
						} else {
							$this->response = array(
								"status" => 232,
								"message" => ResponseMessages::getStatusCodeMessages(248),
							);
						}
					}
				} else {
					$this->response = array(
						"status" => 232,
						"message" => ResponseMessages::getStatusCodeMessages(247),
					);
				}
			} else {
				$this->response = array(
					"status" => 232,
					"message" => ResponseMessages::getStatusCodeMessages(232),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to share rides
	public function shareRide(Request $request) {
		date_default_timezone_set("Asia/Kolkata");
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("booking_id", "share_type"));
		//try {
		// get id, user_id, trusted_contacts, sos from booking
		if ($booking = Booking::select('id', "name", 'user_id', 'trusted_contacts', 'sos', 'mobile_number')->where("id", $request->booking_id)->first()) {
			$driverDetail = $this->driverBookingDetail($request->booking_id);
			// check share_type is sos or not
			if ($request->share_type == "sos") {
				$booking->sos = '1';
			} else {
				$booking->trusted_contacts = '1';
			}
			$booking->save();
			// get trusted contacts based on user
			$contacts = TrustedContact::where('user_id', $booking->user_id)->where("status", "AC")->get();
			$msg = "$booking->name ($booking->mobile_number) has shared his NXG Charge with you. $booking->name is riding with $driverDetail->name in a $driverDetail->vehicle_model ($driverDetail->number_plate), Driver Mob No - $driverDetail->mobile_number.";
			// $msg = "$booking->name ($booking->mobile_number) has shared his NXG Charge with you. $booking->name is riding with $driverDetail->name in a $driverDetail->vehicle_model ($driverDetail->number_plate), Driver Mob No - $driverDetail->mobile_number.";

			if ($contacts->count() > 0) {
				foreach ($contacts as $contact) {
					if (!ShareRide::where("trusted_contact_id", $contact->id)->where("booking_id", $booking->id)->first()) {
						$shareRide = new ShareRide();
						$shareRide->trusted_contact_id = $contact->id;
						$shareRide->booking_id = $booking->id;
						$shareRide->created_at = date("Y-m-d H:i:s");
						$shareRide->save();
						SendMail::sendSMS(array($contact->mobile_number), $msg);
						if ($user = User::where(["mobile_number" => $contact->mobile_number])->where("device_token", "!=", "")->first()) {
							$user->share_ride_count = $user->share_ride_count + 1;
							$user->save();
							$item = (object) array("device_type" => $user->device_type, "device_token" => $user->device_token);
							$msgarray = array(
								'title' => 'Ride Shared',
								'msg' => $msg,
								'type' => 'shredRide',
							);
							$fcmData = array(
								'message' => $msgarray['msg'],
								'body' => $msgarray['title'],
							);
							$this->sendFirebaseNotification($item, $msgarray, $fcmData);
						}
					}
				}
				if ($request->share_type == "sos") {
					$user = UserView::where(["role" => "admin"])->where("device_token", "!=", "")->first();
					$item = (object) array("device_type" => $user->device_type, "device_token" => $user->device_token);
					// print_r($item);
					$msgarray = array(
						'title' => 'Ride Shared',
						'msg' => $msg,
						'type' => 'shredRide',
					);
					$fcmData = array(
						'message' => $msgarray['msg'],
						'body' => $msgarray['title'],
					);
					$this->sendFirebaseNotification($item, $msgarray, $fcmData);
				}
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(234),
				);
			} else {
				$this->response = array(
					"status" => 304,
					"message" => ResponseMessages::getStatusCodeMessages(152),
				);
			}
		} else {
			$this->response = array(
				"status" => 304,
				"message" => ResponseMessages::getStatusCodeMessages(304),
			);
		}
		// } catch (\Exception $ex) {
		// 	$this->response = array(
		// 		"status" => 501,
		// 		"message" => ResponseMessages::getStatusCodeMessages(501),
		// 	);
		// }

		$this->shut_down();
	}

	// function called to get shared rides
	public function getSharedRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "user_type"));
		try {
			// check user is active or not
			$this->checkUserActive($request->user_id);
			// DB::enableQueryLog();
			// check user tye is admin or not
			if ($request->user_type == "admin") {
				// get share rides list based on booking, user, trusted contacts and sos
				$share_rides = User::select("user.id as user_id", "user.name", "user.mobile_number", "driver.id as driver_id", DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) as profile_picture"), "driver.name as driver_name", "driver.mobile_number as driver_mobile_number", "driver.number_plate", DB::raw("CONCAT('" . url("uploads/profiles") . "/', driver.profile_picture) as driver_profile_picture"), "vehicle_types.vehicle_type as driver_vehicle_type", "bookings.pickup_address", "bookings.dropoff_address", "bookings.booking_status", "bookings.created_at", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude")->join("trusted_contacts", "trusted_contacts.mobile_number", "=", "users.mobile_number")->join("share_rides", "trusted_contacts.id", "=", "share_rides.trusted_contact_id")->join("bookings", "bookings.id", "=", "share_rides.booking_id")->join("users as user", "user.id", "=", "bookings.user_id")->join("users as driver", "driver.id", "=", "bookings.driver_id")->join("vehicle_types", "vehicle_types.id", "=", "driver.vehicle_type_id")->where("bookings.sos", '1')->orderBy("bookings.id", "desc")->get();
			} elseif ($request->user_type == "user") {
				// get share rides list based on booking, user, trusted contacts
				$share_rides = User::select("user.id as user_id", "user.name", "user.mobile_number", "driver.id as driver_id", DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) as profile_picture"), "driver.name as driver_name", "driver.mobile_number as driver_mobile_number", "driver.number_plate", DB::raw("CONCAT('" . url("uploads/profiles") . "/', driver.profile_picture) as driver_profile_picture"), "vehicle_types.vehicle_type as driver_vehicle_type", "bookings.pickup_address", "bookings.dropoff_address", "bookings.booking_status", "bookings.created_at", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude")->join("trusted_contacts", "trusted_contacts.mobile_number", "=", "users.mobile_number")->join("share_rides", "trusted_contacts.id", "=", "share_rides.trusted_contact_id")->join("bookings", "bookings.id", "=", "share_rides.booking_id")->join("users as user", "user.id", "=", "bookings.user_id")->join("users as driver", "driver.id", "=", "bookings.driver_id")->join("vehicle_types", "vehicle_types.id", "=", "driver.vehicle_type_id")->where(function ($q) {
					$q->where("bookings.sos", '1')->orWhere("bookings.trusted_contacts", '1');
				})->where("users.id", $request->user_id)->orderBy("bookings.id", "desc")->get();

				User::where("id", $request->user_id)->update(['share_ride_count' => 0]);
			}
			// print_r(DB::getQueryLog());
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => $share_rides,
			);

		} catch (\Exception $ex) {
			// return message when exception get
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get wallet history
	public function getWalletHistory(Request $request) {
		date_default_timezone_set("Asia/Kolkata");
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// check user is active or not
			$this->checkUserActive($request->user_id);
			// get wallet history
			$wallet = Wallet::with(['getBooking' => function ($q) {
				$q->select("id", "booking_code", "booking_status", "total", "created_at");
			},
				'getTransaction' => function ($q) {
					$q->select("id", "transaction_code", "amount", "transaction_status", "created_at");
				},
				'getReferrer' => function ($q) {
					$q->select("id", "ref_code", "amount", "created_at", "status");
				},
			])->where("user_id", $request->user_id)->orderBy("created_at", "desc")->get();

			if ($wallet->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $wallet,
				);
			} else {
				$this->response = array(
					"status" => 235,
					"message" => ResponseMessages::getStatusCodeMessages(235),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to get wallet balance
	public function getWalletBalance(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			// check user is active or not
			$this->checkUserActive($request->user_id);
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(200),
				"data" => User::select("wallet_amount")->where("id", $request->user_id)->first()->wallet_amount,
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		$this->shut_down();
	}

	########################## Ride API ######################

	// function called to
	public function testUser(Request $request) {
		// dd(CouponCode::with(["userCouponCode" => function ($q) {
		// 	$q->where("user_id", 64);
		// }])->withCount(["bookings" => function ($q) {
		// 	$q->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y%m%d')"), array("coupon_codes.start_date", "coupon_codes.end_date"));
		// }])->get()->toArray());
		$today = date("Y-m-d");
		// DB::enableQueryLog();
		$couponCode = CouponCode::select("user_coupon_code.id", "coupon_codes.start_date", "coupon_codes.end_date", DB::raw("(select count(1) from bookings where user_id = user_coupon_code.user_id and DATE_FORMAT(created_at,'%Y%m%d') BETWEEN coupon_codes.start_date and coupon_codes.end_date) as total"))->join("user_coupon_code", "user_coupon_code.coupon_code_id", "=", "coupon_codes.id")->where(['coupon_codes.discount_type' => "rides"])->where("end_date", ">=", $today)->where("user_coupon_code.user_id", 64)->first();
		print_r(DB::getQueryLog());
		die;

		//$this->shut_down();
	}

	// function called to book a ride
	public function bookRide(Request $request) {
		// print_r($request->all());
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "name", "mobile_number", "device_token", "country", "state", "city", "vehicle_type_id", "pickup_latitude", "pickup_longitude", "dropoff_latitude", "dropoff_longitude", "pickup_address", "dropoff_address", "polyline", "payment_mode", "coupon_code_id"));
		try {
			// check user is active or not
			$this->checkUserActive($request->user_id);
			if ($request->payment_mode == "wallet") {
				$walletBalance = User::select("wallet_amount")->where("id", $request->user_id)->first()->wallet_amount;
				$minimumWalletBalanceRequired = $this->getBusRuleRef("minimum_wallet_balance");
				if ($walletBalance < $minimumWalletBalanceRequired) {
					$this->response = array(
						"status" => 153,
						"message" => ResponseMessages::getStatusCodeMessages(153),
					);
					$this->shut_down();
					exit;
				}
			}
			$current = date("Y-m-d H:i:s");
			$from = date("Y-m-d H:i:s", strtotime('-1 hours'));
			$to = date("Y-m-d H:i:s", strtotime('+1 hours'));
			$today = date("Y-m-d");

			$schedule = 0;
			$bookingStatus = 8;
			// DB::enableQueryLog();
			// print_r(DB::getQueryLog());
			// get booking based on booking status is 6 or 8
			if ($booking = Booking::select("booking_status")->where("user_id", $request->user_id)->where("schedule", 1)->where("scheduled_dateTime", ">=", $from)->where("scheduled_dateTime", "<=", $to)->whereIn("booking_status", array(6, 8))->first()) {
				$schedule = 1;
				$bookingStatus = $booking->booking_status;
			}

			// check schedule is inactive
			if ($schedule == 0) {
				$rideBooked = new Booking();
				$rideBooked->booking_code = Helper::generateNumber("bookings", "booking_code");
				$rideBooked->user_id = $request->user_id;
				$rideBooked->name = $request->name;
				$rideBooked->mobile_number = $request->mobile_number;
				$rideBooked->vehicle_type_id = $request->vehicle_type_id;
				$rideBooked->pickup_latitude = $request->pickup_latitude;
				$rideBooked->pickup_longitude = $request->pickup_longitude;
				$rideBooked->dropoff_latitude = $request->dropoff_latitude;
				$rideBooked->dropoff_longitude = $request->dropoff_longitude;
				$rideBooked->pickup_address = $request->pickup_address;
				$rideBooked->dropoff_address = $request->dropoff_address;
				$rideBooked->booking_status = $bookingStatus;
				$rideBooked->created_at = date("Y-m-d H:i:s");
				$rideBooked->device_token = $request->device_token;
				$rideBooked->polyline = $request->polyline;
				$rideBooked->payment_mode = $request->payment_mode;
				$rideBooked->coupon_code_id = $request->coupon_code_id;
				$rideBooked->country = $request->country;
				$rideBooked->state = $request->state;
				$rideBooked->city = $request->city;
				$rideBooked->save();
				// print_r($rideBooked);
				// print_r($rideBooked->toArray());
				// echo "-0-";
				// check user not applied any coupon code
				if ($request->coupon_code_id == "") {
					// echo "-1-";
					// DB::enableQueryLog();
					$couponCode = CouponCode::select("user_coupon_code.id", "user_coupon_code.coupon_code_id", "coupon_codes.start_date", "coupon_codes.end_date", "coupon_codes.no_of_rides", DB::raw("(select count(1) from bookings where user_id = user_coupon_code.user_id and DATE_FORMAT(created_at,'%Y%m%d') BETWEEN coupon_codes.start_date and coupon_codes.end_date and coupon_code_id = coupon_codes.id) as total"))->join("user_coupon_code", "user_coupon_code.coupon_code_id", "=", "coupon_codes.id")->where(['coupon_codes.discount_type' => "rides"])->where("end_date", ">=", $today)->where("user_coupon_code.user_id", $request->user_id)->get();
					// print_r(DB::getQueryLog());
					// get coupon code based on discount_type is rides
					if ($couponCode->count() > 0) {
						// print_r(DB::getQueryLog());
						foreach ($couponCode as $item) {
							if ($item->total < $item->no_of_rides) {
								Booking::where("id", $rideBooked->id)->update(["coupon_code_id" => $item->coupon_code_id]);
							}
						}
					}
				}
				// create booking route
				$BookingRoute = new BookingRoute();
				$BookingRoute->booking_id = $rideBooked->id;
				$BookingRoute->pickup_latitude = $request->pickup_latitude;
				$BookingRoute->pickup_longitude = $request->pickup_longitude;
				$BookingRoute->pickup_address = $request->pickup_address;
				$BookingRoute->dropoff_latitude = $request->dropoff_latitude;
				$BookingRoute->dropoff_longitude = $request->dropoff_longitude;
				$BookingRoute->dropoff_address = $request->dropoff_address;
				$BookingRoute->save();
				// print_r($BookingRoute->toArray());
				// get user's detail along with booking
				$bookingData = $this->userBookingDetail($rideBooked->id);

				$userBooking = User::with(['bookings' => function ($query) use ($rideBooked) {
					$query->where('id', $rideBooked->id);
				}])->where("id", $request->user_id)->first();
				// check user booking is exist or not
				if ($userBooking) {
					// get driver list
					$rideDriverList = $this->rideDriverList($request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type_id);
					// update user's status
					$this->updateUserStatus($request->user_id, 8);
					$msgarray = array(
						'title' => 'New Booking Request',
						'msg' => 'You have a new ride request',
						'type' => 'requestBooking',
					);

					$fcmData = array(
						'user_id' => $rideBooked->user_id,
						'booking_id' => $rideBooked->id,
						'latitude' => $bookingData->latitude,
						'longitude' => $bookingData->longitude,
						'vehicle_type_id' => $rideBooked->vehicle_type_id,
						'name' => $rideBooked->name,
						'mobile_number' => $rideBooked->mobile_number,
						'image' => $bookingData->profile_picture,
						'rating' => $bookingData->rating,
						'payment_method' => $rideBooked->payment_mode,
						'pickup_address' => $rideBooked->pickup_address,
						'dropoff_address' => $rideBooked->dropoff_address,
						'pickup_latitude' => $rideBooked->pickup_latitude,
						'pickup_longitude' => $rideBooked->pickup_longitude,
						'dropoff_latitude' => $rideBooked->dropoff_latitude,
						'dropoff_longitude' => $rideBooked->dropoff_longitude,
						'message' => $msgarray['msg'],
						'body' => $msgarray['title'],
					);

					if (isset($rideDriverList)) {
						if (count($rideDriverList) > 0) {
							// echo "driver";
							// echo "<pre>";
							// print_r($rideDriverList);
							// echo "</pre>";
							// echo "\n";
							foreach ($rideDriverList as $item) {
								$userDriver = new UserDriver();
								$userDriver->booking_id = $rideBooked->id;
								$userDriver->driver_id = $item->id;
								$userDriver->user_id = $rideBooked->user_id;
								$userDriver->booking_status = 8;
								$userDriver->save();
								// send notifications to drivers
								$this->sendFirebaseNotification($item, $msgarray, $fcmData);
								// dd($userDriver);
							}
							$this->response = array(
								'booking_id' => $rideBooked->id,
								"status" => 146,
								"message" => ResponseMessages::getStatusCodeMessages(146),
							);
						} else {
							$this->response = array(
								'booking_id' => $rideBooked->id,
								"status" => 146,
								"message" => ResponseMessages::getStatusCodeMessages(146),
							);
						}
					} else {
						$this->response = array(
							'booking_id' => $rideBooked->id,
							"status" => 146,
							"message" => ResponseMessages::getStatusCodeMessages(146),
						);
					}
				} else {
					$this->response = array(
						"status" => 111,
						"message" => ResponseMessages::getStatusCodeMessages(111),
					);
				}

			} else {
				if ($bookingStatus != 6) {
					$this->response = array(
						"status" => 318,
						"message" => ResponseMessages::getStatusCodeMessages(318),
					);
				} else {
					$this->response = array(
						"status" => 317,
						"message" => ResponseMessages::getStatusCodeMessages(317),
					);
				}
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to book a ride again
	public function bookRideAgain(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "booking_id", "device_token"));
		try {
			// check user is active or not
			$this->checkUserActive($request->user_id);
			// check booking exist or not
			if ($bookAgain = Booking::find($request->booking_id)) {
				$current = date("Y-m-d H:i:s");
				$from = date("Y-m-d H:i:s", strtotime('-1 hours'));
				$to = date("Y-m-d H:i:s", strtotime('+1 hours'));

				$schedule = 0;
				$bookingStatus = 8;
				$booking = Booking::select("booking_status")->where("user_id", $request->user_id)->where("schedule", 1)->where("scheduled_dateTime", ">=", $from)->where("scheduled_dateTime", "<=", $to)->whereIn("booking_status", array(6, 8))->get();
				if ($booking->count() > 0) {
					$schedule = $booking->count();
					$bookingStatus = $booking->booking_status;
				}
				if ($schedule == 0) {
					$rideBooked = new Booking();
					$rideBooked->booking_code = Helper::generateNumber("bookings", "booking_code");
					$rideBooked->user_id = $request->user_id;
					$rideBooked->name = $bookAgain->name;
					$rideBooked->mobile_number = $bookAgain->mobile_number;
					$rideBooked->vehicle_type_id = $bookAgain->vehicle_type_id;
					$rideBooked->pickup_latitude = $bookAgain->pickup_latitude;
					$rideBooked->pickup_longitude = $bookAgain->pickup_longitude;
					$rideBooked->dropoff_latitude = $bookAgain->dropoff_latitude;
					$rideBooked->dropoff_longitude = $bookAgain->dropoff_longitude;
					$rideBooked->pickup_address = $bookAgain->pickup_address;
					$rideBooked->dropoff_address = $bookAgain->dropoff_address;
					$rideBooked->booking_status = $bookingStatus;
					$rideBooked->created_at = date("Y-m-d H:i:s");
					$rideBooked->device_token = $request->device_token;
					$rideBooked->polyline = $bookAgain->polyline;
					$rideBooked->payment_mode = $bookAgain->payment_mode;
					$rideBooked->coupon_code_id = $bookAgain->coupon_code_id;
					$rideBooked->country = $bookAgain->country;
					$rideBooked->state = $bookAgain->state;
					$rideBooked->city = $bookAgain->city;
					$rideBooked->save();

					$bookingData = $this->userBookingDetail($rideBooked->id);

					$userBooking = User::with(['bookings' => function ($query) use ($rideBooked) {
						$query->where('id', $rideBooked->id);
					}])->where("id", $request->user_id)->first();

					if ($userBooking) {
						$rideDriverList = $this->rideDriverList($bookAgain->pickup_latitude, $bookAgain->pickup_longitude, $bookAgain->vehicle_type_id);
						$this->updateUserStatus($request->user_id, 8);
						$msgarray = array(
							'title' => 'New Booking Request',
							'msg' => 'You have a new ride request',
							'type' => 'requestBooking',
						);
						$fcmData = array(
							'user_id' => $rideBooked->user_id,
							'booking_id' => $rideBooked->id,
							'latitude' => $bookingData->latitude,
							'longitude' => $bookingData->longitude,
							'vehicle_type_id' => $rideBooked->vehicle_type_id,
							'name' => $rideBooked->name,
							'mobile_number' => $rideBooked->mobile_number,
							'image' => $bookingData->profile_picture,
							'rating' => $bookingData->rating,
							'payment_method' => $rideBooked->payment_mode,
							'pickup_address' => $rideBooked->pickup_address,
							'dropoff_address' => $rideBooked->dropoff_address,
							'pickup_latitude' => $rideBooked->pickup_latitude,
							'pickup_longitude' => $rideBooked->pickup_longitude,
							'dropoff_latitude' => $rideBooked->dropoff_latitude,
							'dropoff_longitude' => $rideBooked->dropoff_longitude,
							'message' => $msgarray['msg'],
							'body' => $msgarray['title'],
						);
						// print_r($fcmData);

						if (isset($rideDriverList)) {
							if (count($rideDriverList) > 0) {
								// echo "driver";
								// echo "<pre>";
								// print_r($rideDriverList);
								// echo "</pre>";
								// echo "\n";
								foreach ($rideDriverList as $item) {
									$this->updateUserStatus($item->id, 8);
									$userDriver = new UserDriver();
									$userDriver->booking_id = $rideBooked->id;
									$userDriver->driver_id = $item->id;
									$userDriver->user_id = $rideBooked->user_id;
									$userDriver->booking_status = 8;
									$userDriver->save();
									$this->sendFirebaseNotification($item, $msgarray, $fcmData);
								}
								$this->response = array(
									'booking_id' => $rideBooked->id,
									"status" => 146,
									"message" => ResponseMessages::getStatusCodeMessages(146),
								);
							} else {
								$this->response = array(
									'booking_id' => $rideBooked->id,
									"status" => 146,
									"message" => ResponseMessages::getStatusCodeMessages(146),
								);
							}
						} else {
							$this->response = array(
								'booking_id' => $rideBooked->id,
								"status" => 146,
								"message" => ResponseMessages::getStatusCodeMessages(146),
							);
						}
					} else {
						$this->response = array(
							"status" => 111,
							"message" => ResponseMessages::getStatusCodeMessages(111),
						);
					}
				} else {
					if ($bookingStatus != 6) {
						$this->response = array(
							"status" => 318,
							"message" => ResponseMessages::getStatusCodeMessages(318),
						);
					} else {
						$this->response = array(
							"status" => 317,
							"message" => ResponseMessages::getStatusCodeMessages(317),
						);
					}
				}
			} else {
				$this->response = array(
					"status" => 304,
					"message" => ResponseMessages::getStatusCodeMessages(304),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to update running ride
	public function updateRunningRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("booking_id", "dropoff_latitude", "dropoff_longitude", "dropoff_address"));
		try {
			// check booking exist or not based on booking status is not 6,4,7,8
			if ($booking = Booking::select("bookings.user_id", "bookings.id", "bookings.driver_id", "bookings.vehicle_type_id", "bookings.name", "bookings.mobile_number", "users.payment_method", "bookings.pickup_address", "bookings.dropoff_address", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude", "users.latitude", "users.longitude", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture"))->join("users", "users.id", "=", "bookings.user_id")->where("bookings.id", $request->booking_id)->where("bookings.driver_id", "!=", "")->whereNotIn("bookings.booking_status", array(6, 4, 7, 8))->first()) {
				$driver = User::select("device_token", "device_type")->where("id", $booking->driver_id)->first();
				// check dropoff_latitude, dropoff_latitude is not null
				if ($booking->dropoff_latitude != $request->dropoff_latitude && $booking->dropoff_longitude != $request->dropoff_longitude) {
					$booking->dropoff_latitude = $request->dropoff_latitude;
					$booking->dropoff_longitude = $request->dropoff_longitude;
					$booking->dropoff_address = $request->dropoff_address;
					$booking->save();

					$BookingRoute = new BookingRoute();
					$BookingRoute->booking_id = $booking->id;
					$BookingRoute->dropoff_latitude = $request->dropoff_latitude;
					$BookingRoute->dropoff_longitude = $request->dropoff_longitude;
					$BookingRoute->dropoff_address = $request->dropoff_address;
					$BookingRoute->save();

					$msgarray = array(
						'title' => 'Drop address changed',
						'msg' => 'User has changed drop address',
						'type' => 'changBookingAddress',
					);

					$fcmData = array(
						'user_id' => $booking->user_id,
						'booking_id' => $booking->id,
						'latitude' => $booking->latitude,
						'booking_route_id' => $BookingRoute->id,
						'longitude' => $booking->longitude,
						'vehicle_type_id' => $booking->vehicle_type_id,
						'name' => $booking->name,
						'mobile_number' => $booking->mobile_number,
						'image' => $booking->profile_picture,
						'rating' => $booking->rating,
						'payment_method' => $booking->payment_method,
						'pickup_address' => $booking->pickup_address,
						'dropoff_address' => $booking->dropoff_address,
						'pickup_latitude' => $booking->pickup_latitude,
						'pickup_longitude' => $booking->pickup_longitude,
						'dropoff_latitude' => $booking->dropoff_latitude,
						'dropoff_longitude' => $booking->dropoff_longitude,
						'message' => $msgarray['msg'],
						'body' => $msgarray['title'],
					);
					$this->sendFirebaseNotification($driver, $msgarray, $fcmData);
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(243),
					);
				} else {
					$this->response = array(
						"status" => 244,
						"message" => ResponseMessages::getStatusCodeMessages(244),
					);
				}
			} else {
				$this->response = array(
					"status" => 304,
					"message" => ResponseMessages::getStatusCodeMessages(304),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to
	public function updateRunningRideDriver(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("pickup_latitude", "pickup_longitude", "pickup_address", "booking_route_id"));
		try {
			$BookingRoute = BookingRoute::where("id", $request->booking_route_id)->first();
			$BookingRoute->pickup_latitude = $request->pickup_latitude;
			$BookingRoute->pickup_longitude = $request->pickup_longitude;
			$BookingRoute->pickup_address = $request->pickup_address;
			$BookingRoute->save();
			$this->response = array(
				"status" => 200,
				"message" => ResponseMessages::getStatusCodeMessages(245),
			);
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to
	public function cancelRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "booking_id", "device_token"));
		try {
			$this->checkUserActive($request->user_id);
			$cancel_charges_time = $this->getBusRuleRef("cancel_charges_time");
			$cancel_time = strtotime(date("Y-m-d H:i:s")) * 1000;
			$booking_id = $request->post('booking_id');
			$booking_status = 4;

			$Booking = Booking::select("bookings.driver_id", "bookings.user_id", "bookings.device_token", "bookings.driver_accept_time", "bookings.vehicle_type_id", "vehicle_types.waiting_charge", "vehicle_types.cancellation_charge")->join("vehicle_types", "vehicle_types.id", "=", "bookings.vehicle_type_id")->where("bookings.id", $booking_id)->first();
			if ($Booking) {
				$driver_id = $Booking->driver_id;
				$user_id = $Booking->user_id;
				$device_token = $Booking->device_token;
				$driver_accept_time = $Booking->driver_accept_time;
				$vehicle_type_id = $Booking->vehicle_type_id;
				$waiting_charge = $Booking->waiting_charge;
				if ($device_token == $request->device_token) {
					$cancelRide = $this->cancelUserRide($booking_id, $booking_status, $driver_id, $user_id, $driver_accept_time, $cancel_time, $cancel_charges_time, $vehicle_type_id, $waiting_charge);

					$msgarray = array(
						'msg' => $cancelRide->user_name . ' has cancelled this ride',
						'title' => 'Ride Cancelled',
						'type' => 'cancelRide',
					);
					$fcmData = array(
						'message' => $cancelRide->user_name . $msgarray['msg'],
						'body' => $msgarray['title'],
					);
					$notificationSend = $this->sendFirebaseNotification($cancelRide, $msgarray, $fcmData);
					$this->response = array(
						"status" => 135,
						"message" => ResponseMessages::getStatusCodeMessages(135),
					);
				} else {
					$this->response = array(
						"status" => 332,
						"message" => ResponseMessages::getStatusCodeMessages(332),
					);
				}
			} else {
				$this->response = array(
					"status" => 330,
					"message" => ResponseMessages::getStatusCodeMessages(330),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to
	public function responseRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "booking_id", "driver_id", "booking_status", "latitude", "longitude", "device_id"));
		try {
			$this->checkUserActive($request->user_id);
			// check single signon of driver
			$this->checkSingleSignOn($request->driver_id, $request->device_id);

			date_default_timezone_set("Asia/Kolkata");
			$driver_accept_time = strtotime(date("Y-m-d H:i:s")) * 1000;

			if ($booking = Booking::where(["id" => $request->booking_id, "booking_status" => 8])->first()) {
				if ($request->booking_status == 0) {
					// $UserDriver = UserDriver::where("booking_id", $request->booking_id)->first();
					UserDriver::where("booking_id", $request->booking_id)->where("driver_id", $request->driver_id)->where("user_id", $request->user_id)->update(['booking_status' => $request->booking_status]);

					$this->response = array(
						"status" => 125,
						"message" => ResponseMessages::getStatusCodeMessages(125),
					);
				} else {
					$userDriver = UserDriver::where("booking_id", $request->booking_id)->where("booking_status", $request->booking_status)->get();
					if ($userDriver->count() == 0) {
						$this->driverLocationUpdate($request->driver_id, $request->latitude, $request->longitude);
						User::where("id", $request->driver_id)->update(["latitude" => $request->latitude, "longitude" => $request->longitude]);
						Booking::where("id", $request->booking_id)->update(["driver_id" => $request->driver_id, "booking_status" => $request->booking_status, "driver_accept_time" => $driver_accept_time]);
						User::where("id", $request->driver_id)->update(["user_status" => $request->booking_status]);
						User::where("id", $request->user_id)->update(["user_status" => $request->booking_status]);
						UserDriver::where("user_id", $request->user_id)->where("driver_id", $request->driver_id)->where("booking_id", $request->booking_id)->update(["booking_status" => $request->booking_status]);
						UserDriver::where("booking_id", $request->booking_id)->update(["system_booking_status" => $request->booking_status]);

						$acceptBooking = collect(\DB::select("SELECT users.id as user_id,users.name,users.mobile_number, COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = bookings.driver_id),0) as rating, bookings.payment_mode, CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture, CONCAT('" . url("uploads/vehicles") . "/', vehicle_types.image) vehicle_image,CONCAT('" . url("uploads/vehicles/aerial") . "/', vehicle_types.aerial_image) aerial_image, users.device_token, users.device_type,users.number_plate,users.vehicle_color,users.vehicle_model,users.vehicle_type_id,users.latitude,users.longitude,bookings.id as booking_id,bookings.pickup_address,bookings.dropoff_address,bookings.pickup_latitude,bookings.pickup_longitude,bookings.dropoff_latitude,bookings.dropoff_longitude,bookings.booking_status,vehicle_types.id,vehicle_types.vehicle_type FROM users JOIN user_driver ON users.id = user_driver.driver_id JOIN vehicle_types ON users.vehicle_type_id = vehicle_types.id JOIN bookings ON users.id = bookings.driver_id WHERE bookings.id = " . $request->booking_id . ""))->first();

						if ($acceptBooking) {
							$msgarray = array(
								'msg' => ucfirst($acceptBooking->name) . ' is on his way to pick you up',
								'title' => 'Ride Accepted',
								'type' => 'acceptBooking',
							);
							$fcmData = array(
								'user_id' => $acceptBooking->user_id,
								'booking_id' => $acceptBooking->booking_id,
								'latitude' => $acceptBooking->latitude,
								'longitude' => $acceptBooking->longitude,
								'vehicle_type_id' => $acceptBooking->vehicle_type_id,
								'device_type' => $acceptBooking->device_type,
								'device_token' => $acceptBooking->device_token,
								'name' => $acceptBooking->name,
								'mobile_number' => $acceptBooking->mobile_number,
								'image' => $acceptBooking->profile_picture,
								'rating' => $acceptBooking->rating,
								'payment_method' => $acceptBooking->payment_mode,
								'number_plate' => $acceptBooking->number_plate,
								'vehicle_type' => $acceptBooking->vehicle_type,
								'vehicle_image' => $acceptBooking->vehicle_image,
								'aerial_image' => $acceptBooking->aerial_image,
								'latitude' => $acceptBooking->latitude,
								'longitude' => $acceptBooking->longitude,
								'pickup_address' => $acceptBooking->pickup_address,
								'dropoff_address' => $acceptBooking->dropoff_address,
								'pickup_latitude' => $acceptBooking->pickup_latitude,
								'pickup_longitude' => $acceptBooking->pickup_longitude,
								'dropoff_latitude' => $acceptBooking->dropoff_latitude,
								'dropoff_longitude' => $acceptBooking->dropoff_longitude,
								'booking_status' => $acceptBooking->booking_status,
								'message' => $msgarray['msg'],
								'body' => $msgarray['title'],
							);
							// print_r($fcmData);
							$userRideBooking = collect(\DB::select("SELECT users.id as user_id,users.name,users.mobile_number, COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = bookings.user_id),0) as rating, users.payment_method,CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture, bookings.device_token, users.device_type, bookings.id as booking_id, bookings.pickup_address, bookings.dropoff_address, bookings.pickup_latitude, bookings.pickup_longitude,bookings.dropoff_latitude, bookings.dropoff_longitude,bookings.booking_status FROM users JOIN bookings ON users.id = bookings.user_id WHERE bookings.id ='" . $request->booking_id . "'"))->first();
							// print_r($userRideBooking);
							$this->sendFirebaseNotification($userRideBooking, $msgarray, $fcmData);
							//X ( X) is on the way to your location in a X X X.
							$msg = "$acceptBooking->name ( $acceptBooking->mobile_number) is on the way to your location in a $acceptBooking->vehicle_color $acceptBooking->vehicle_model $acceptBooking->number_plate.";
							SendMail::sendSMS(array($booking->mobile_number), $msg);
							if ($userRideBooking) {
								$this->response = array(
									"status" => 126,
									"message" => ResponseMessages::getStatusCodeMessages(126),
									'data' => $userRideBooking,
								);
							}
						} else {
							$this->response = array(
								"status" => 221,
								"message" => ResponseMessages::getStatusCodeMessages(221),
							);
						}
					} else {
						$this->response = array(
							"status" => 127,
							"message" => ResponseMessages::getStatusCodeMessages(127),
						);
					}
				}
			} else {
				$this->response = array(
					"status" => 316,
					"message" => ResponseMessages::getStatusCodeMessages(316),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to
	public function startRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "booking_id", "device_id", "latitude", "longitude", "driver_id"));
		try {
			date_default_timezone_set("Asia/Kolkata");
			$start_time = strtotime(date("Y-m-d H:i:s")) * 1000;
			$device_id = $request->device_id;
			$booking_id = $request->booking_id;
			$driver_id = $request->driver_id;
			$user_id = $request->user_id;
			$booking_status = 3;
			$latitude = $request->latitude;
			$longitude = $request->longitude;

			// check single signon of driver
			$this->checkSingleSignOn($driver_id, $device_id);
			$this->checkRidePreviousStatus(2, $booking_id);
			$this->driverLocationUpdate($driver_id, $latitude, $longitude);

			$startRide = $this->startUserRide($start_time, $booking_id, $driver_id, $user_id, $booking_status);
			if ($startRide) {
				$msgarray = array(
					'msg' => 'You are now riding with ' . $startRide->name,
					'title' => 'Ride Started',
					'type' => 'startRide',
				);
				$fcmData = array(
					'user_id' => $startRide->user_id,
					'booking_id' => $startRide->booking_id,
					'latitude' => $startRide->latitude,
					'longitude' => $startRide->longitude,
					'vehicle_type_id' => $startRide->vehicle_type_id,
					'name' => $startRide->name,
					'mobile_number' => $startRide->mobile_number,
					'image' => $startRide->profile_picture,
					'rating' => $startRide->rating,
					'number_plate' => $startRide->number_plate,
					'vehicle_type' => $startRide->vehicle_type,
					'vehicle_image' => $startRide->vehicle_image,
					'aerial_image' => $startRide->aerial_image,
					'user_status' => $startRide->user_status,
					'totalDistance' => $startRide->totalDistance,
					'totalRideCharge' => $startRide->totalRideCharge,
					'payment_method' => $startRide->payment_mode,
					'pickup_address' => $startRide->pickup_address,
					'dropoff_address' => $startRide->dropoff_address,
					'pickup_latitude' => $startRide->pickup_latitude,
					'pickup_longitude' => $startRide->pickup_longitude,
					'dropoff_latitude' => $startRide->dropoff_latitude,
					'dropoff_longitude' => $startRide->dropoff_longitude,
					'message' => $msgarray['msg'],
					'body' => $msgarray['title'],
				);

				$startRideBooking = $this->userBookingDetail($booking_id);
				$this->sendFirebaseNotification($startRideBooking, $msgarray, $fcmData);
				if ($startRideBooking) {
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(131),
						'data' => $startRideBooking,
					);
				} else {
					$this->response = array(
						"status" => 111,
						"message" => ResponseMessages::getStatusCodeMessages(111),
						'data' => $startRideBooking,
					);
				}
			} else {
				$this->response = array(
					"status" => 111,
					"message" => ResponseMessages::getStatusCodeMessages(111),
					'data' => $startRideBooking,
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to
	public function driverArrived(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "device_id", "booking_id", "latitude", "longitude", "driver_id"));
		try {
			date_default_timezone_set("Asia/Kolkata");
			$arrived_time = strtotime(date("Y-m-d H:i:s")) * 1000;
			$user_id = $request->user_id;
			$driver_id = $request->driver_id;
			$device_id = $request->device_id;
			$booking_id = $request->booking_id;
			$booking_status = 2;
			$latitude = $request->latitude;
			$longitude = $request->longitude;

			// check single signon of driver
			$this->checkSingleSignOn($driver_id, $device_id);
			$this->checkRidePreviousStatus(1, $booking_id);
			$this->driverLocationUpdate($driver_id, $latitude, $longitude);
			$driverArrived = $this->driverArrivedAtLoc($arrived_time, $user_id, $driver_id, $booking_id, $booking_status);
			if ($driverArrived) {
				$msgarray = array(
					'msg' => ucfirst($driverArrived->name) . ' has arrived please meet him at the pickup location',
					'title' => 'Driver Arrived',
					'type' => 'driverArrived',
				);
				$fcmData = array(
					'user_id' => $driverArrived->user_id,
					'booking_id' => $driverArrived->booking_id,
					'latitude' => $driverArrived->latitude,
					'longitude' => $driverArrived->longitude,
					'vehicle_type_id' => $driverArrived->vehicle_type_id,
					'name' => $driverArrived->name,
					'mobile_number' => $driverArrived->mobile_number,
					'image' => $driverArrived->profile_picture,
					'rating' => $driverArrived->rating,
					'number_plate' => $driverArrived->number_plate,
					'vehicle_type' => $driverArrived->vehicle_type,
					'vehicle_image' => $driverArrived->vehicle_image,
					'aerial_image' => $driverArrived->aerial_image,
					'user_status' => $driverArrived->user_status,
					'totalDistance' => $driverArrived->totalDistance,
					'totalRideCharge' => $driverArrived->totalRideCharge,
					'payment_method' => $driverArrived->payment_mode,
					'pickup_address' => $driverArrived->pickup_address,
					'dropoff_address' => $driverArrived->dropoff_address,
					'pickup_latitude' => $driverArrived->pickup_latitude,
					'pickup_longitude' => $driverArrived->pickup_longitude,
					'dropoff_latitude' => $driverArrived->dropoff_latitude,
					'dropoff_longitude' => $driverArrived->dropoff_longitude,
					'message' => $msgarray['msg'],
					'body' => $msgarray['title'],
				);

				$userArrivedData = $this->userBookingDetail($booking_id);
				$this->sendFirebaseNotification($userArrivedData, $msgarray, $fcmData);
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(128),
					'data' => $userArrivedData,
				);
			} else {
				$this->response = array(
					"status" => 111,
					"message" => ResponseMessages::getStatusCodeMessages(111),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to
	public function endRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "driver_id", "booking_id", "device_id", "start_latitude", "start_longitude", "end_latitude", "end_longitude", "distance", "time", "polyline"));
		// try {
		date_default_timezone_set("Asia/Kolkata");

		$end_time = strtotime(date("Y-m-d H:i:s")) * 1000;
		$driver_id = $request->driver_id;
		$device_id = $request->device_id;
		$user_id = $request->user_id;
		$booking_id = $request->booking_id;
		$booking_status = 5;
		$start_latitude = $request->start_latitude;
		$start_longitude = $request->start_longitude;
		$end_latitude = $request->end_latitude;
		$end_longitude = $request->end_longitude;
		$distance = $request->distance;
		$time = $request->time;
		$polyline = $request->polyline;

		// check single signon of driver
		$this->checkSingleSignOn($driver_id, $device_id);
		$this->checkRidePreviousStatus(3, $booking_id);
		$this->driverLocationUpdate($driver_id, $end_latitude, $end_longitude);
		$via = "";
		$googleMapKey = $this->getBusRuleRef("google_map_key");
		$cgst = $this->getBusRuleRef("cgst");
		$sgst = $this->getBusRuleRef("sgst");
		//via:Charlestown,MA|via:Lexington,MA
		// DB::enableQueryLog();
		$BookingRoute = BookingRoute::where("booking_id", $booking_id)->where("pickup_latitude", "!=", "")->where("pickup_longitude", "!=", "")->get();
		// print_r(DB::getQueryLog());
		// print_r($BookingRoute->toArray());
		if ($BookingRoute->count() > 1) {
			for ($i = 0; $i < $BookingRoute->count(); $i++) {
				// echo $i;
				if ($i == ($BookingRoute->count() - 1)) {
					$pickup_latitude = $BookingRoute[$i]->pickup_latitude;
					$pickup_longitude = $BookingRoute[$i]->pickup_longitude;
					$dropoff_latitude = $BookingRoute[$i]->dropoff_latitude;
					$dropoff_longitude = $BookingRoute[$i]->dropoff_longitude;
					$via .= "via:$pickup_latitude,$pickup_longitude|via:$dropoff_latitude,$dropoff_longitude";
					// echo "#1-<br>";
				} else {
					$pickup_latitude = $BookingRoute[$i]->pickup_latitude;
					$pickup_longitude = $BookingRoute[$i]->pickup_longitude;
					$via .= "via:$pickup_latitude,$pickup_longitude|";
					// echo "#2-<br>";
				}
			}
		} else {
			foreach ($BookingRoute as $item) {
				$pickup_latitude = $item->pickup_latitude;
				$pickup_longitude = $item->pickup_longitude;
				$dropoff_latitude = $request->end_latitude;
				$dropoff_longitude = $request->end_longitude;
				$via .= "via:$pickup_latitude,$pickup_longitude|via:$dropoff_latitude,$dropoff_longitude";
			}
		}
		// echo $via;

		$fare = $this->getDirection($start_latitude, $start_longitude, $end_latitude, $end_longitude, $via);
		$dist = $fare['routes'][0]['legs'][0]['distance']['value'];

		$time = $fare['routes'][0]['legs'][0]['duration']['value'];
		// echo "<br>";
		$polyline = $fare['routes'][0]['overview_polyline']['points'];
		// echo "<br>";
		// print_r($fare);

		// die;
		// $dist = ceil($dist / 1000); // in km
		// $time = ceil($time / 60); //in minutes

		$data = array();
		$dist = ceil($dist / 1000); // in km
		$time = ceil($time / 60); //in minutes

		$path_image = "http://maps.googleapis.com/maps/api/staticmap?style=visibility:simplified&size=900x300&markers=size:small|color:0x000000f9|" . $start_latitude . "," . $start_longitude . "&markers=size:small|color:0x000000f9|" . $end_latitude . "," . $end_longitude . "&path=color:0x000000f9|weight:5|enc:$polyline&sensor=false&key=$googleMapKey";
		$discount = 0;
		$data["totalDistance"] = $dist;
		$endRide = $this->endUserRide($end_time, $user_id, $driver_id, $booking_id, $booking_status, $end_latitude, $end_longitude, $polyline, $path_image);

		$base_fare_charge = 0.00;
		if ($vehicleTypes = VehicleType::select("base_fare")->where("id", $endRide->vehicle_type_id)->first()) {
			$base_fare_charge = $vehicleTypes->base_fare;
		}

		// DB::enableQueryLog();
		$cancellation_charge = User::select("users.cancellation_charge")->join("bookings", "bookings.user_id", "=", "users.id")->where("users.id", $user_id)->first()->cancellation_charge;
		// print_r(DB::getQueryLog());

		$totalDistanceCharge = ceil($dist * $endRide->price);
		$totalWaitingCharge = round($endRide->waiting_charge * ($endRide->waiting_time / 60));
		// $cancellation_charge = $endRide->cancellation_charge;
		$rideCharges = ($totalDistanceCharge + $totalWaitingCharge + $base_fare_charge);
		// $data["totalRideCharge"] = $rideCharges + $cancellation_charge;
		// $data["cost"] = $rideCharges;
		// $data["cancellation_charge"] = $cancellation_charge;

		if ($bookingCalculate = Booking::select("coupon_codes.*")->join("coupon_codes", "coupon_codes.id", "=", "bookings.coupon_code_id")->where("bookings.id", $booking_id)->first()) {
			if ($bookingCalculate->amount_type == "amount") {
				$discount = $bookingCalculate->discount_value;
			} elseif ($bookingCalculate->amount_type == "percent") {
				$discount = round(($rideCharges * $bookingCalculate->discount_value) / 100);
				if ($discount > $bookingCalculate->max_amount) {
					$discount = $bookingCalculate->max_amount;
				}
			}
		}
		$rideChargesWithDiscount = $rideCharges - $discount;
		$cgst = round(($rideChargesWithDiscount * $cgst) / 100);
		$sgst = round(($rideChargesWithDiscount * $sgst) / 100);
		$total_without_tax = $rideChargesWithDiscount;
		$total_with_tax = $rideChargesWithDiscount + $cgst + $sgst;
		$rideChargesWithCancelCharge = $total_with_tax + $cancellation_charge;

		Booking::where("id", $booking_id)->update(["cost" => $rideCharges, "total" => $rideChargesWithCancelCharge, "cancellation_charge" => $cancellation_charge, "base_fare_charge" => $base_fare_charge, "distance" => $dist, "promo_deduct" => $discount, "cgst" => $cgst, "sgst" => $sgst, "total_without_tax" => $total_without_tax]);
		User::where("id", $user_id)->update(["cancellation_charge" => 0]);

		// echo "-0-";
		// echo "<br>";
		if ($endRide->payment_mode == "wallet") {
			// echo "-1-";
			// echo "<br>";
			// echo "rideChargesWithCancelCharge = $rideChargesWithCancelCharge";
			// echo "<br>";
			$users = User::where("id", $user_id)->first();
			// echo "users->wallet_amount = $users->wallet_amount";
			// echo "<br>";
			$users->wallet_amount = $users->wallet_amount - $rideChargesWithCancelCharge;
			$users->save();
			$wallet = new Wallet();
			$wallet->user_id = $user_id;
			$wallet->type = "booking";
			$wallet->amount = $users->wallet_amount;
			$wallet->payment_mode = "wallet";
			$wallet->booking_id = $booking_id;
			$wallet->save();
		}
		$rideCharges = $this->userBookingDetail($booking_id);
		$bookingDetail = Booking::where("id", $booking_id)->first();

		$msgarray = array(
			'msg' => ucfirst($endRide->name) . ' has ended this ride.Please rate the driver',
			'title' => 'Ride Ended',
			'type' => 'endRide',
		);
		$fcmData = array(
			'user_id' => $endRide->user_id,
			'booking_id' => $endRide->booking_id,
			'latitude' => $endRide->latitude,
			'longitude' => $endRide->longitude,
			'vehicle_type_id' => $endRide->vehicle_type_id,
			'name' => $endRide->name,
			'mobile_number' => $endRide->mobile_number,
			'image' => $endRide->profile_picture,
			'rating' => $endRide->rating,
			'number_plate' => $endRide->number_plate,
			'vehicle_type' => $endRide->vehicle_type,
			'vehicle_image' => $endRide->vehicle_image,
			'aerial_image' => $endRide->aerial_image,
			'user_status' => $endRide->user_status,
			'totalDistance' => "$bookingDetail->distance",
			'totalRideCharge' => "$bookingDetail->total",
			'promo_deduct' => "$bookingDetail->promo_deduct",
			'payment_method' => $endRide->payment_mode,
			'pickup_address' => $endRide->pickup_address,
			'dropoff_address' => $endRide->dropoff_address,
			'pickup_latitude' => $endRide->pickup_latitude,
			'pickup_longitude' => $endRide->pickup_longitude,
			'dropoff_latitude' => $endRide->dropoff_latitude,
			'dropoff_longitude' => $endRide->dropoff_longitude,
			'message' => $msgarray['msg'],
			'body' => $msgarray['title'],
		);
		$mailData['name'] = $bookingDetail->name;
		$mailData['mobile_number'] = $bookingDetail->mobile_number;
		$mailData['profile_picture'] = $rideCharges->profile_picture;
		$mailData['referral_code'] = $rideCharges->referral_code;
		$mailData['pickup_address'] = $bookingDetail->pickup_address;
		$mailData['dropoff_address'] = $bookingDetail->dropoff_address;
		$mailData['distance'] = $bookingDetail->distance;
		$mailData['waiting_time'] = $bookingDetail->waiting_time;
		$mailData['cost'] = $bookingDetail->cost;
		$mailData['discount'] = $bookingDetail->promo_deduct;
		$mailData['cancellation_charge'] = $bookingDetail->cancellation_charge;
		$mailData['cgst'] = $bookingDetail->cgst;
		$mailData['sgst'] = $bookingDetail->sgst;
		$mailData['igst'] = $bookingDetail->igst;
		$mailData['cgst_percent'] = $this->getBusRuleRef('cgst');
		$mailData['sgst_percent'] = $this->getBusRuleRef('sgst');
		$mailData['igst_percent'] = $this->getBusRuleRef('igst');
		$mailData['path_image'] = $bookingDetail->path_image;
		$mailData['total_without_tax'] = $total_without_tax;
		$mailData['total'] = $bookingDetail->total;
		$mailData['trip_time'] = date("H:i", (($bookingDetail->end_time) / 1000) - (($bookingDetail->driver_accept_time) / 1000));
		$mailData['payment_mode'] = $bookingDetail->payment_mode;
		$mailData['created_at'] = $bookingDetail->created_at;
		$mailData['updated_at'] = $bookingDetail->updated_at;
		$mailData['driver_name'] = $endRide->name;
		$mailData['driver_profile_picture'] = $endRide->profile_picture;
		$mailData['driver_number_plate'] = $endRide->number_plate;
		$mailData['driver_vehicle'] = $endRide->vehicle_type;
		$mailData['driver_rating'] = $endRide->rating;
		$mailData['user_rating'] = $rideCharges->rating;
		$mailData['share_amount'] = $this->getBusRuleRef('referrer_amount');

		//SendMail::sendUserInvoiceMail("NXG Charge - User Invoice", $mailData, null, "emails.invoice", $rideCharges->email);
		//SendMail::sendDriverInvoiceMail("NXG Charge - Driver Invoice", $mailData, null, "emails.driver_invoice", $endRide->email);

		$this->sendFirebaseNotification($rideCharges, $msgarray, $fcmData);

		## Start ShareRide Notification

		$sharedContacts = ShareRide::select("users.device_token", "users.device_type", "trusted_contacts.user_id")->join("trusted_contacts", "trusted_contacts.id", "=", "share_rides.trusted_contact_id")->join("users", "users.mobile_number", "trusted_contacts.mobile_number")->where("share_rides.booking_id", $booking_id)->get();
		if ($sharedContacts->count() > 0) {
			foreach ($sharedContacts as $user) {
				$msg = "This ride is now ended. Please reach out to your friend to ensure he is doing fine.";
				$item = (object) array("device_type" => $user->device_type, "device_token" => $user->device_token);
				$msgarray = array(
					'title' => 'Ride Shared End',
					'msg' => $msg,
					'type' => 'endSharedRide',
				);
				$fcmData = array(
					'message' => $msgarray['msg'],
					'body' => $msgarray['title'],
					'user_id' => $user->user_id,
				);
				$this->sendFirebaseNotification($item, $msgarray, $fcmData);
			}
		}

		## End ShareRide Notification

		$this->response = array(
			"status" => 200,
			"message" => ResponseMessages::getStatusCodeMessages(132),
			'data' => $rideCharges,
		);
		// } catch (\Exception $ex) {
		// 	$this->response = array(
		// 		"status" => 501,
		// 		"message" => ResponseMessages::getStatusCodeMessages(501),
		// 	);
		// }

		$this->shut_down();
	}

	// function called to
	public function finishRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "booking_id", "driver_id", "device_id"));
		// try {
		date_default_timezone_set("Asia/Kolkata");
		$booking_id = $request->booking_id;
		$user_id = $request->user_id;
		$driver_id = $request->driver_id;
		// check single signon of driver
		$this->checkSingleSignOn($driver_id, $request->device_id);

		$finishRide = $this->finishUserRide($booking_id, $user_id, $driver_id);
		if ($finishRide) {
			$msgarray = array(
				'msg' => 'Your ride is finished. Thanks for rating the driver',
				'title' => 'Ride Finished',
				'type' => 'finish ride',
			);

			$driverData = $this->driverBookingDetail($finishRide->booking_id);

			$fcmData = array(
				'user_id' => $driverData->user_id,
				'booking_id' => $finishRide->booking_id,
				'latitude' => $driverData->latitude,
				'longitude' => $driverData->longitude,
				'vehicle_type_id' => $driverData->vehicle_type_id,
				'name' => $driverData->name,
				'mobile_number' => $driverData->mobile_number,
				'image' => $driverData->profile_picture,
				'rating' => $driverData->rating,
				'number_plate' => $driverData->number_plate,
				'vehicle_type' => $driverData->vehicle_type,
				'vehicle_image' => $driverData->vehicle_image,
				'aerial_image' => $driverData->aerial_image,
				'user_status' => $driverData->user_status,
				'totalDistance' => $finishRide->totalDistance,
				'totalRideCharge' => $finishRide->totalRideCharge,
				'payment_method' => $finishRide->payment_mode,
				'pickup_address' => $finishRide->pickup_address,
				'dropoff_address' => $finishRide->dropoff_address,
				'pickup_latitude' => $finishRide->pickup_latitude,
				'pickup_longitude' => $finishRide->pickup_longitude,
				'dropoff_latitude' => $finishRide->dropoff_latitude,
				'dropoff_longitude' => $finishRide->dropoff_longitude,
				'message' => $msgarray['msg'],
				'body' => $msgarray['title'],
			);
			$userFinishRide = $this->userBookingDetail($booking_id);
			$this->sendFirebaseNotification($finishRide, $msgarray, $fcmData);
			if ($userFinishRide) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(145),
				);
			} else {
				$this->response = array(
					"status" => 111,
					"message" => ResponseMessages::getStatusCodeMessages(111),
				);
			}
		}
		// } catch (\Exception $ex) {
		// 	$this->response = array(
		// 		"status" => 501,
		// 		"message" => ResponseMessages::getStatusCodeMessages(501),
		// 	);
		// }

		$this->shut_down();
	}

	// function called to
	public function previousRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "user_type", "device_id"));
		try {
			if ($request->user_type == "driver") {
				// check single signon of driver
				$this->checkSingleSignOn($request->user_id, $request->device_id);
			}
			// DB::enableQueryLog();
			$rides = User::select("users.name", "users.mobile_number", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id and booking_id=bookings.id),0) as rating"), DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture"), "bookings.id as booking_id", "bookings.payment_mode as payment_method", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude", "bookings.pickup_address", "bookings.dropoff_address", "bookings.path_image", "bookings.cost", "bookings.total", "users.number_plate", "bookings.start_time as date", "vehicle_types.vehicle_type");
			if ($request->user_type == "driver") {
				$rides->join("bookings", "bookings.user_id", "=", "users.id")->where("bookings.driver_id", $request->user_id);
			} else {
				$rides->join("bookings", "bookings.driver_id", "=", "users.id")->where("bookings.user_id", $request->user_id);
			}
			$rides->join("vehicle_types", "bookings.vehicle_type_id", "=", "vehicle_types.id");
			$rides = $rides->where("bookings.booking_status", 7)->groupBy("bookings.id")->orderBy("date", "desc")->get();
			// print_r(DB::getQueryLog());

			if ($rides->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $rides,
				);
			} else {
				$this->response = array(
					"status" => 231,
					"message" => ResponseMessages::getStatusCodeMessages(231),
				);
			}

		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to
	public function upcomingRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));
		try {
			$rides = User::select("bookings.id as booking_id", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude", "bookings.pickup_address", "bookings.dropoff_address", "bookings.payment_mode", "bookings.booking_status", "vehicle_types.vehicle_type", DB::raw("UNIX_TIMESTAMP(CONVERT_TZ(`scheduled_dateTime`, '+05:30', @@session.time_zone))*1000 date"), "bookings.scheduled_dateTime as scheduled_dateTime")->join("bookings", "bookings.user_id", "=", "users.id")->where("bookings.user_id", $request->user_id)->join("vehicle_types", "bookings.vehicle_type_id", "=", "vehicle_types.id")->where("bookings.booking_status", 6)->where("bookings.schedule", 1)->groupBy("bookings.id")->orderBy("scheduled_dateTime", "desc")->get();

			if ($rides->count() > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					"data" => $rides,
				);
			} else {
				$this->response = array(
					"status" => 231,
					"message" => ResponseMessages::getStatusCodeMessages(246),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		$this->shut_down();
	}
/*
##
Schedule Ride
##
 */

	// function called to schedule ride
	public function scheduleRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "device_token", "pickup_dateTime", "pickup_latitude", "pickup_longitude", "dropoff_latitude", "dropoff_longitude", "vehicle_type_id", "pickup_address", "dropoff_address", "country", "state", "city", "name", "mobile_number", "payment_mode"));
		try {
			$this->checkUserActive($request->user_id);
			if ($request->payment_mode == "wallet") {
				$walletBalance = User::select("wallet_amount")->where("id", $request->user_id)->first()->wallet_amount;
				$minimumWalletBalanceRequired = $this->getBusRuleRef("minimum_wallet_balance");
				if ($walletBalance < $minimumWalletBalanceRequired) {
					$this->response = array(
						"status" => 153,
						"message" => ResponseMessages::getStatusCodeMessages(153),
					);
					$this->shut_down();
					exit;
				}
			}
			if (date("Y-m-d H:i:s") < date("Y-m-d H:i:s", doubleval($request->pickup_dateTime) / 1000)) {
				$user_id = $request->user_id;
				$pickup_dateTime = date("Y-m-d H:i:s", doubleval($request->pickup_dateTime) / 1000);
				$pickup_latitude = $request->pickup_latitude;
				$pickup_longitude = $request->pickup_longitude;
				$dropoff_latitude = $request->dropoff_latitude;
				$dropoff_longitude = $request->dropoff_longitude;
				$vehicle_type_id = $request->vehicle_type_id;
				$booking_status = $request->booking_status;
				$pickup_address = $request->pickup_address;
				$dropoff_address = $request->dropoff_address;
				$device_token = $request->device_token;
				$country = $request->country;
				$state = $request->state;
				$city = $request->city;
				$name = $request->name;
				$mobile_number = $request->mobile_number;

				$current = date("Y-m-d H:i:s");
				$from = date("Y-m-d H:i:s", strtotime('-1 hours', strtotime($pickup_dateTime)));
				$to = date("Y-m-d H:i:s", strtotime('+1 hours', strtotime($pickup_dateTime)));

				// $sql = $this->db->query("select count(1) as total, booking_status from bookings where user_id=$user_id and booking_status IN (6,8) and schedule = 1 and scheduled_dateTime>='$from' and scheduled_dateTime<='$to'");
				$schedule = 0;
				$bookingStatus = 6;
				if ($sql = Booking::select(DB::raw("count(1) as total"), "booking_status")->where("user_id", $user_id)->where("schedule", 1)->whereIn("booking_status", array(6, 8))->where("scheduled_dateTime", ">=", $from)->where("scheduled_dateTime", "<=", $to)->first()) {
					$schedule = $sql->total;
					$bookingStatus = $sql->booking_status;
				}
				if ($schedule == 0) {
					$booking = new Booking();
					$booking->user_id = $user_id;
					$booking->booking_code = Helper::generateNumber("bookings", "booking_code");
					$booking->device_token = $device_token;
					$booking->scheduled_dateTime = $pickup_dateTime;
					$booking->pickup_latitude = $pickup_latitude;
					$booking->pickup_longitude = $pickup_longitude;
					$booking->dropoff_latitude = $dropoff_latitude;
					$booking->dropoff_longitude = $dropoff_longitude;
					$booking->payment_mode = $request->payment_mode;
					$booking->booking_status = 6;
					$booking->vehicle_type_id = $vehicle_type_id;
					$booking->pickup_address = $pickup_address;
					$booking->dropoff_address = $dropoff_address;
					$booking->country = $country;
					$booking->state = $state;
					$booking->city = $city;
					$booking->name = $name;
					$booking->mobile_number = $mobile_number;
					$booking->schedule = 1;
					$booking->created_at = date("Y-m-d H:i:s");
					$booking->save();

					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(138),
					);
				} else {
					if ($bookingStatus != 6) {
						$this->response = array(
							"status" => 318,
							"message" => ResponseMessages::getStatusCodeMessages(318),
						);
					} else {
						$this->response = array(
							"status" => 317,
							"message" => ResponseMessages::getStatusCodeMessages(317),
						);
					}
				}
			} else {
				$this->response = array(
					"status" => 334,
					"message" => ResponseMessages::getStatusCodeMessages(334),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}

	// function called to edit scheduled ride
	public function editScheduledRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "booking_id", "device_token", "pickup_dateTime", "pickup_latitude", "pickup_longitude", "dropoff_latitude", "dropoff_longitude", "vehicle_type_id", "pickup_address", "dropoff_address", "country", "state", "city", "name", "mobile_number"));
		// try {
		$this->checkUserActive($request->user_id);
		if (date("Y-m-d H:i:s") < date("Y-m-d H:i:s", doubleval($request->pickup_dateTime) / 1000)) {
			$user_id = $request->user_id;
			$booking_id = $request->booking_id;
			$device_token = $request->device_token;
			$pickup_dateTime = date("Y-m-d H:i:s", doubleval($request->pickup_dateTime) / 1000);
			$pickup_latitude = $request->pickup_latitude;
			$pickup_longitude = $request->pickup_longitude;
			$dropoff_latitude = $request->dropoff_latitude;
			$dropoff_longitude = $request->dropoff_longitude;
			$vehicle_type_id = $request->vehicle_type_id;
			$booking_status = $request->booking_status;
			$pickup_address = $request->pickup_address;
			$dropoff_address = $request->dropoff_address;
			$country = $request->country;
			$state = $request->state;
			$city = $request->city;
			$name = $request->name;
			$mobile_number = $request->mobile_number;

			if ($booking = Booking::select("scheduled_dateTime", "device_token")->where("id", $booking_id)->first()) {
				if ($booking->device_token == $device_token) {
					$scheduled_dateTime = $booking->scheduled_dateTime;
					$change_schduled_ride_time = "+" . $this->getBusRuleRef("change_schduled_ride_time") . " minutes";
					$checkSchedule = strtotime($change_schduled_ride_time, strtotime(date("Y-m-d H:i:s")));
					$scheduled_dateTime = strtotime($scheduled_dateTime);
					if ($checkSchedule <= $scheduled_dateTime) {
						$current = date("Y-m-d H:i:s");
						$from = date("Y-m-d H:i:s", strtotime('-1 hours', strtotime($pickup_dateTime)));
						$to = date("Y-m-d H:i:s", strtotime('+1 hours', strtotime($pickup_dateTime)));
						// $sql = $this->db->query("select count(1) as total, booking_status from bookings where user_id=$user_id and booking_status IN (6,8) and schedule = 1 and scheduled_dateTime>='$from' and scheduled_dateTime<='$to'");

						$schedule = 0;
						$bookingStatus = 6;
						if ($sql = Booking::select(DB::raw("count(1) as total"), "booking_status")->where("user_id", $user_id)->where("schedule", 1)->whereIn("booking_status", array(6, 8))->where("scheduled_dateTime", ">=", $from)->where("id", "!=", $booking_id)->where("scheduled_dateTime", "<=", $to)->first()) {
							if ($sql->booking_status == 6 || $sql->booking_status == 8) {
								$schedule = $sql->total;
								$bookingStatus = $sql->booking_status;
							}
						}
						if ($schedule == 0) {
							$booking = Booking::find($booking_id);
							$booking->scheduled_dateTime = $pickup_dateTime;
							$booking->pickup_latitude = $pickup_latitude;
							$booking->pickup_longitude = $pickup_longitude;
							$booking->dropoff_latitude = $dropoff_latitude;
							$booking->dropoff_longitude = $dropoff_longitude;
							$booking->vehicle_type_id = $vehicle_type_id;
							$booking->pickup_address = $pickup_address;
							$booking->dropoff_address = $dropoff_address;
							$booking->country = $country;
							$booking->state = $state;
							$booking->city = $city;
							$booking->name = $name;
							$booking->mobile_number = $mobile_number;
							$booking->schedule = 1;
							$booking->created_at = date("Y-m-d H:i:s");
							$booking->save();
							$this->response = array(
								"status" => 327,
								"message" => ResponseMessages::getStatusCodeMessages(327),
							);
						} else {
							if ($bookingStatus != 6) {
								$this->response = array(
									"status" => 318,
									"message" => ResponseMessages::getStatusCodeMessages(318),
								);
							} else {
								$this->response = array(
									"status" => 317,
									"message" => ResponseMessages::getStatusCodeMessages(317),
								);
							}
						}
					} else {
						$this->response = array(
							"status" => 329,
							"message" => ResponseMessages::getStatusCodeMessages(329),
						);
					}

				} else {
					$this->response = array(
						"status" => 333,
						"message" => ResponseMessages::getStatusCodeMessages(333),
					);
				}

			} else {
				$this->response = array(
					"status" => 328,
					"message" => ResponseMessages::getStatusCodeMessages(328),
				);
			}
		} else {
			$this->response = array(
				"status" => 334,
				"message" => ResponseMessages::getStatusCodeMessages(334),
			);
		}
		// } catch (\Exception $ex) {
		// 	$this->response = array(
		// 		"status" => 501,
		// 		"message" => ResponseMessages::getStatusCodeMessages(501),
		// 	);
		// }

		$this->shut_down();
	}

	// function called to cancel scheduled ride
	public function cancelSchduledRide(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id", "device_token", "booking_id"));
		try {
			if ($booking = Booking::select("id", "driver_id", "device_token", "booking_status")->where("id", $request->booking_id)->where("booking_status", 6)->first()) {
				if ($booking->device_token == $request->device_token) {
					$booking->booking_status = 4;
					$booking->save();
					$this->response = array(
						"status" => 200,
						"message" => ResponseMessages::getStatusCodeMessages(326),
					);
				} else {
					$this->response = array(
						"status" => 333,
						"message" => ResponseMessages::getStatusCodeMessages(333),
					);
				}
			} else {
				$this->response = array(
					"status" => 330,
					"message" => ResponseMessages::getStatusCodeMessages(330),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}
		$this->shut_down();
	}

/*
##
Cron Jobs
##
 */

	// function called to auto cancel rides
	public function cronCheckRideStatus() {
		date_default_timezone_set("Asia/Kolkata");
		$time = $this->getBusRuleRef("ride_status") + 10;
		$CronJobLog = new CronJobLog();
		$CronJobLog->title = 'Ride Status';
		$CronJobLog->created_at = date("Y-m-d H:i:s");
		$CronJobLog->save();
		$booking = Booking::where("booking_status", 8)->get();
		if ($booking->count() > 0) {
			foreach ($booking as $item) {
				$currentDate = strtotime(date("Y-m-d H:i:s"));
				// echo "<br>---------";
				// echo $item->scheduled_dateTime;
				// echo "<br>---------";
				if ($item->schedule) {
					$current = $currentDate - strtotime($item->scheduled_dateTime);
				} else {
					$current = $currentDate - strtotime($item->created_at);
				}
				// echo "<br>---------";
				echo $current . ">=" . $time;
				// echo "<br><br><br><br>";
				if ($current >= $time) {
					// echo "-1-";
					Booking::where("id", $item->id)->update(["booking_status" => 4]);
					UserDriver::where("booking_id", $item->id)->update(["system_booking_status" => 4]);
					User::where("id", $item->user_id)->update(["user_status" => 9]);
					$user = User::where("id", $item->user_id)->first();
					echo "-1-";
					print_r($item->device_token);
					$user->device_token = $item->device_token;
					echo "-2-";
					print_r($user->device_token);
					$userDetail = (object) ["device_token" => $user->device_token, "device_type" => $user->device_type];
					$this->sendFirebaseNotification($userDetail, array("title" => "Booking Response", "msg" => "No Driver found, please try again", "type" => "AutoCancelBooking"), array("user_status" => $user->user_status, "message" => "No Driver found, please try again"));
				}
				// echo "<br><br><br><br>";
			}
		}

		DB::enableQueryLog();
		$driverStuck = User::select("users.id", "users.user_status")->join("bookings", "bookings.driver_id", "=", "users.id")->where(["users.user_status" => 8, "bookings.booking_status" => 7])->get();
		print_r(DB::getQueryLog());
		// print_r($driverStuck->toArray());
		if ($driverStuck->count() > 0) {
			foreach ($driverStuck as $driver) {
				User::where("id", $driver->id)->update(["user_status" => 9]);
			}
		}

		################# Second Cron Job - Check Driver In radius and visible ###############
		// echo "<pre>";
		echo "<br><br>Second Cron Job - Check Driver In radius and visible";
		echo "<br><br>";
		$result = Booking::where("booking_status", 8)->orderBy("id", "desc")->get();
		if ($result->count() > 0) {
			foreach ($result as $item) {
				$drivers = "";
				DB::enableQueryLog();
				if ($query = UserDriver::select(DB::raw("group_concat(driver_id) as drivers"))->where("booking_id", $item->id)->first()) {
					print_r(DB::getQueryLog());
					$drivers = $query->drivers;
				}
				$this->cronScheduledRequestToNextDrivers($drivers, $item->id);
			}
		}
	}
	// function called to send scheduled request rides to driver on scheduled time
	public function cronScheduledRequestToNextDrivers($drivers, $booking_id) {
		echo "<br><br>called cronScheduledRequestToNextDrivers <br>";
		print_r($drivers);
		echo "<br>";
		echo "<br>";
		date_default_timezone_set("Asia/Kolkata");
		echo $time = $this->getBusRuleRef("schedule_time") + 10;
		$CronJobLog = new CronJobLog();
		$CronJobLog->title = 'Next Scheduler Ride';
		// DB::enableQueryLog();
		$rideBooking = User::select(DB::raw("NOW()"), DB::raw("TIME_TO_SEC(TIMEDIFF(bookings.scheduled_dateTime, NOW())) as time_diff"), "bookings.*", "users.id as user_id", "users.name", "users.mobile_number", "users.payment_method", DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"), "users.latitude", "users.longitude", "users.latitude", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"))->join("bookings", "users.id", "=", "bookings.user_id")->where("bookings.booking_status", 8)->whereRaw("(TIME_TO_SEC(TIMEDIFF(bookings.scheduled_dateTime, NOW())))<=$time")->orderBy("bookings.id", "desc")->first();

		// print_r(DB::getQueryLog());

		if ($rideBooking) {
			print_r(DB::getQueryLog());
			echo $msg = "Booking Id - " . $rideBooking->id;
			$CronJobLog->description = '$msg -1-';
			if ($drivers == "") {
				$rideDriverList = $this->rideDriverList($rideBooking->pickup_latitude, $rideBooking->pickup_longitude, $rideBooking->vehicle_type_id);
			} else {
				$rideDriverList = $this->rideNextDriverList($rideBooking->pickup_latitude, $rideBooking->pickup_longitude, $rideBooking->vehicle_type_id, $drivers);
			}

			$this->updateUserStatus($rideBooking->user_id, 8);
			$msgarray = array(
				'title' => 'New Booking Request',
				'msg' => 'You have a new ride request',
				'type' => 'requestBooking',
			);

			$fcmData = array(
				'user_id' => $rideBooking->user_id,
				'booking_id' => $rideBooking->id,
				'latitude' => $rideBooking->latitude,
				'longitude' => $rideBooking->longitude,
				'vehicle_type_id' => $rideBooking->vehicle_type_id,
				'name' => $rideBooking->name,
				'mobile_number' => $rideBooking->mobile_number,
				'image' => $rideBooking->profile_picture,
				'rating' => $rideBooking->rating,
				'payment_method' => $rideBooking->payment_mode,
				'pickup_address' => $rideBooking->pickup_address,
				'dropoff_address' => $rideBooking->dropoff_address,
				'pickup_latitude' => $rideBooking->pickup_latitude,
				'pickup_longitude' => $rideBooking->pickup_longitude,
				'dropoff_latitude' => $rideBooking->dropoff_latitude,
				'dropoff_longitude' => $rideBooking->dropoff_longitude,
				'message' => $msgarray['msg'],
				'body' => $msgarray['title'],
			);

			if (isset($rideDriverList)) {
				if (count($rideDriverList) > 0) {
					foreach ($rideDriverList as $item) {
						$userDriver = new UserDriver();
						$userDriver->booking_id = $rideBooking->id;
						$userDriver->driver_id = $item->id;
						$userDriver->user_id = $rideBooking->user_id;
						$userDriver->booking_status = 8;
						$userDriver->system_booking_status = 8;
						$userDriver->save();

						$this->sendFirebaseNotification($item, $msgarray, $fcmData);
						// dd($userDriver);
					}
					$this->response = array(
						'booking_id' => $rideBooking->id,
						"status" => 146,
						"message" => ResponseMessages::getStatusCodeMessages(146),
					);
				}
			}
			User::where("id", $rideBooking->user_id)->update(["user_status" => 8]);

			Booking::where("id", $rideBooking->id)->update(["booking_status" => 8]);
			UserDriver::where("booking_id", $rideBooking->id)->update(["system_booking_status" => 8]);
		}
		$CronJobLog->created_at = date("Y-m-d H:i:s");
		$CronJobLog->save();
		echo "<br>";
		echo "Cron Job Successfully Run";
	}

	// function called to send scheduled request rides to driver on scheduled time
	public function cronScheduledRequest() {
		date_default_timezone_set("Asia/Kolkata");
		echo $time = $this->getBusRuleRef("schedule_time") + 10;
		$CronJobLog = new CronJobLog();
		$CronJobLog->title = 'Scheduler Ride';
		DB::enableQueryLog();
		if ($rideBooking = User::select(DB::raw("NOW()"), DB::raw("TIME_TO_SEC(TIMEDIFF(bookings.scheduled_dateTime, NOW())) as time_diff"), "bookings.*", "users.id as user_id", "users.name", "users.mobile_number", "users.payment_method", DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"), "users.latitude", "users.longitude", "users.latitude", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"))->join("bookings", "users.id", "=", "bookings.user_id")->where("bookings.booking_status", 6)->whereRaw("(TIME_TO_SEC(TIMEDIFF(bookings.scheduled_dateTime, NOW())))<=$time")->orderBy("bookings.id", "desc")->first()) {
			print_r(DB::getQueryLog());
			echo $msg = "Booking Id - " . $rideBooking->id;
			$CronJobLog->description = "$msg -1-";
			$rideDriverList = $this->rideDriverList($rideBooking->pickup_latitude, $rideBooking->pickup_longitude, $rideBooking->vehicle_type_id);

			$this->updateUserStatus($rideBooking->user_id, 8);
			$msgarray = array(
				'title' => 'New Booking Request',
				'msg' => 'You have a new ride request',
				'type' => 'requestBooking',
			);

			$fcmData = array(
				'user_id' => $rideBooking->user_id,
				'booking_id' => $rideBooking->id,
				'latitude' => $rideBooking->latitude,
				'longitude' => $rideBooking->longitude,
				'vehicle_type_id' => $rideBooking->vehicle_type_id,
				'name' => $rideBooking->name,
				'mobile_number' => $rideBooking->mobile_number,
				'image' => $rideBooking->profile_picture,
				'rating' => $rideBooking->rating,
				'payment_method' => $rideBooking->payment_mode,
				'pickup_address' => $rideBooking->pickup_address,
				'dropoff_address' => $rideBooking->dropoff_address,
				'pickup_latitude' => $rideBooking->pickup_latitude,
				'pickup_longitude' => $rideBooking->pickup_longitude,
				'dropoff_latitude' => $rideBooking->dropoff_latitude,
				'dropoff_longitude' => $rideBooking->dropoff_longitude,
				'message' => $msgarray['msg'],
				'body' => $msgarray['title'],
			);

			if (isset($rideDriverList)) {
				if (count($rideDriverList) > 0) {
					foreach ($rideDriverList as $item) {
						$userDriver = new UserDriver();
						$userDriver->booking_id = $rideBooking->id;
						$userDriver->driver_id = $item->id;
						$userDriver->user_id = $rideBooking->user_id;
						$userDriver->booking_status = 8;
						$userDriver->system_booking_status = 8;
						$userDriver->save();

						$this->sendFirebaseNotification($item, $msgarray, $fcmData);
						// dd($userDriver);
					}
					$this->response = array(
						'booking_id' => $rideBooking->id,
						"status" => 146,
						"message" => ResponseMessages::getStatusCodeMessages(146),
					);
				}
			}
			User::where("id", $rideBooking->user_id)->update(["user_status" => 8]);

			Booking::where("id", $rideBooking->id)->update(["booking_status" => 8]);
			UserDriver::where("booking_id", $rideBooking->id)->update(["system_booking_status" => 8]);
		}
		$CronJobLog->created_at = date("Y-m-d H:i:s");
		$CronJobLog->save();
		echo "<br>";
		echo "Cron Job Successfully Run";
	}
	// function called to count driver all Compliments
	public function getComplimentsCount(Request $request) {
		// check keys are exist
		$this->checkKeys(array_keys($request->all()), array("user_id"));

		try {
			// get user documents if they have status AC
			// DB::enableQueryLog();
			$complements = Complement::select(DB::raw('0 as count'), 'id')->pluck('count', 'id');

			$all_compliments = Rating::select(DB::raw('count(ratings.id) as total'), 'complements.id')->join('complements', 'ratings.complement_id', '=', 'complements.id')->
				where("ratings.parent_id", $request->user_id)
				->where("ratings.complement_id", '!=', NULL)->where("ratings.status", "AC")->groupBy('ratings.complement_id')->get();

			foreach ($all_compliments as $value) {
				$complements[$value->id] = $value->total;
			}
			if (count($complements) > 0) {
				$this->response = array(
					"status" => 200,
					"message" => ResponseMessages::getStatusCodeMessages(200),
					'data' => $complements,
				);
			} else {
				$this->response = array(
					"status" => 15,
					"message" => ResponseMessages::getStatusCodeMessages(15),
				);
			}
		} catch (\Exception $ex) {
			$this->response = array(
				"status" => 501,
				"message" => ResponseMessages::getStatusCodeMessages(501),
			);
		}

		$this->shut_down();
	}
}