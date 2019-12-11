<?php

namespace App\Http\Controllers;
use App\Library\ResponseMessages;
use App\Model\Booking;
use App\Model\BusRuleRef;
use App\Model\User;
use App\Model\UserDriver;
use App\Model\UserView;
use DB;

class MyController extends Controller {

	public $response = array(
		"status" => 500,
		"message" => "Internal server error",
	);

	// function __construct(Request $request) {
	// }
	// function __destruct() {
	// 	echo json_encode($this->response);
	// }

	function shut_down() {
		echo json_encode($this->response);
	}

	public function checkKeys($input = array(), $required = array()) {
		// print_r(!array_diff_key(array_flip($input), $required));
		$existance = implode(", ", array_diff($required, $input));
		if (!empty($existance)) {
			if (count(array_diff($required, $input)) == 1) {
				$this->response = array(
					"status" => 101,
					"message" => $existance . " key is missing",
				);
			} else {
				$this->response = array(
					"status" => 101,
					"message" => $existance . " keys are missing",
				);
			}
			$this->shut_down();
			exit;
		}
	}

	public function checkRidePreviousStatus($previousStatus, $bookingId) {
		if ($booking = Booking::select("booking_status")->where("id", $bookingId)->first()) {
			if ($previousStatus == $booking->booking_status) {
				return true;
			} else {
				$this->response = array(
					"status" => 9,
					"message" => ResponseMessages::getStatusCodeMessages(111),
				);
				$this->shut_down();
				exit;
			}
		} else {
			$this->response = array(
				"status" => 304,
				"message" => ResponseMessages::getStatusCodeMessages(304),
			);
			$this->shut_down();
			exit;
		}
	}

	public function checkUserActive($userId) {
		if ($user = User::select("status")->where("id", $userId)->first()) {
			if ($user->status == "AC") {
				return true;
			} else {
				$this->response = array(
					"status" => 216,
					"message" => ResponseMessages::getStatusCodeMessages(216),
				);
				$this->shut_down();
				exit;
			}
		} else {
			$this->response = array(
				"status" => 321,
				"message" => ResponseMessages::getStatusCodeMessages(321),
			);
			$this->shut_down();
			exit;
		}
	}

	public function checkSingleSignOn($userId, $deviceId) {
		if (!empty($userId) && !empty($deviceId)) {
			if ($user = User::select("device_id")->find($userId)) {
				if ($user->device_id == $deviceId) {
					return true;
				} else {
					$this->response = array(
						"status" => 331,
						"message" => ResponseMessages::getStatusCodeMessages(331),
					);
					$this->shut_down();
					exit;
				}
			} else {
				$this->response = array(
					"status" => 321,
					"message" => ResponseMessages::getStatusCodeMessages(321),
				);
				$this->shut_down();
				exit;
			}
		} else {
			$this->response = array(
				"status" => 101,
				"message" => ResponseMessages::getStatusCodeMessages(101),
			);
			$this->shut_down();
			exit;
		}
	}

	public function updateUserStatus($userId, $status) {
		$user = User::where("id", $userId)->update(["user_status" => $status]);
	}

	public function userStatus($userId) {
		if ($user = UserView::select("*", DB::raw("CONCAT('" . url("uploads/profiles") . "/', profile_picture) profile_picture"), DB::raw("CONCAT('" . url("uploads/vehicles/aerial") . "/', aerial_image) aerial_image"))->where("user_id", $userId)->first()) {
			return $user;
		}
	}

	public function driverStatus($userId, $type) {
		if ($type == "user") {
			if ($Booking = Booking::select("id as booking_id", "driver_id")->where("user_id", $userId)->whereNotIn("booking_status", [4, 6])->orderBy("id", "desc")->first()) {
				return $this->userStatus($Booking->driver_id);
			}
		} else {
			if ($Booking = Booking::select("id as booking_id", "driver_id", "user_id")->where("driver_id", $userId)->whereNotIn("booking_status", [4, 6])->orderBy("id", "desc")->first()) {
				return $this->userStatus($Booking->user_id);
			}
		}
	}

	public function getLastBooking($userId, $userType) {
		// DB::enableQueryLog();
		if ($userType == "user") {
			if ($Booking = Booking::where("user_id", $userId)->where("booking_status", "!=", 6)->orderBy("id", "desc")->first()) {
				// print_r(DB::getQueryLog());
				return $Booking;
			}
		} else {
			if ($Booking = Booking::where("driver_id", $userId)->where("booking_status", "!=", 6)->orderBy("id", "desc")->first()) {
				// print_r(DB::getQueryLog());
				return $Booking;
			}
		}
	}

	public function bookingDetail($driverId) {
		if ($Booking = UserDriver::select("booking_id", "booking_status")->where(["driver_id" => $driverId, "booking_status" => 8])->orderBy("booking_id", "desc")->first()) {
			return User::select("user_driver.*", "users.name", "users.email", "users.mobile_number", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), "users.payment_method", DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture"), "users.device_token", "users.device_type", "bookings.id as booking_id", "bookings.pickup_address", "bookings.dropoff_address", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.payment_mode as payment_method", "bookings.dropoff_longitude")->join("bookings", "bookings.user_id", "=", "users.id")->join("user_driver", "bookings.id", "=", "user_driver.booking_id")->where(["bookings.booking_status" => 8, "user_driver.booking_status" => 8, "bookings.id" => $Booking->booking_id])->first();
		}
		// $sql = $this->db->query("select booking_id, booking_status from user_driver where driver_id = $driverId and booking_status = 8 order by booking_id desc limit 1");
		// // echo $this->db->last_query();
		// if ($sql->num_rows() > 0) {
		// 	$row = $sql->row();
		// 	// if ($row->booking_status == 8) {
		// 	$query = $this->db->query("select b.*,u.name,u.email,u.phone_no, (SELECT ROUND(AVG(r.rating)) average_rating  from `ratings` r where r.parent_id = u.id) rating, u.payment_method, CONCAT('" . base_url() . "/uploads/', u.image) image, u.device_token, u.device_type, b.id booking_id, '' as message, '' as body from bookings b, users u, user_driver ur where b.id = $row->booking_id and b.booking_status = 8 and b.user_id = u.id and ur.booking_id = b.id and ur.booking_status = 8 limit 1");
		// 	if ($query->num_rows() > 0) {
		// 		return $query->result_array();
		// 	}
		// }
		// }
	}

	public function getBusRuleRef($rule_name) {
		if ($BusRuleRef = BusRuleRef::where("rule_name", $rule_name)->first()) {
			return $BusRuleRef->rule_value;
		}
	}

	public function getAppInfo() {
		if ($BusRuleRef = BusRuleRef::select("rule_name", "rule_value")->whereIn("rule_name", array('ios_update_driver', 'android_update_driver', 'android_url_driver', 'ios_url_driver', 'ios_version_driver', 'android_version_driver', 'ios_update_user', 'android_update_user', 'android_url_user', 'ios_url_user', 'ios_version_user', 'android_version_user', 'app_update_msg'))->get()) {
			return $BusRuleRef;
		}
	}

	public function driverLocationUpdate($driver_id, $latitude, $longitude) {
		if ($latitude != "" && $longitude != "") {
			User::where("id", $driver_id)->update(["latitude" => $latitude, "longitude" => $longitude]);
		}
	}

	public function startUserRide($start_time, $booking_id, $driver_id, $user_id, $booking_status) {
		User::where("id", $driver_id)->update(["user_status" => $booking_status]);
		User::where("id", $user_id)->update(["user_status" => $booking_status]);
		UserDriver::where("booking_id", $booking_id)->update(["system_booking_status" => $booking_status]);
		// $result = User::select("users.id as user_id", "users.name", "users.mobile_number", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), "users.payment_method", DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture"), "users.device_token", "users.device_type", "users.number_plate", "users.vehicle_type_id", "users.latitude", "users.longitude", "users.user_status", "bookings.id as booking_id", "bookings.pickup_address", "bookings.arrived_time", "bookings.dropoff_address", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude", "bookings.start_time", "vehicle_types.id", "vehicle_types.vehicle_type", "bookings.booking_status")->join("vehicle_types", "users.vehicle_type_id", "=", "vehicle_types.id")->join("bookings", "users.id", "=", "bookings.driver_id")->where("bookings.id", $booking_id)->first();

		$result = $this->driverBookingDetail($booking_id);

		Booking::where("id", $booking_id)->update(["booking_status" => $booking_status, "start_time" => $start_time, "waiting_time" => (($start_time - $result->arrived_time) / 1000)]);
		return $result;
	}

	public function driverBookingDetail($bookingId) {
		// DB::enableQueryLog();
		$result = User::select("users.id as user_id", "users.name", "users.mobile_number", "users.country", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), "users.payment_method", DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture"), DB::raw("CONCAT('" . url("uploads/vehicles/aerial") . "/', vehicle_types.aerial_image) aerial_image"), "users.device_token", "users.device_type", "users.number_plate", "users.vehicle_model", "users.vehicle_color", "users.vehicle_type_id", "users.latitude", "users.email", "users.longitude", "users.user_status", "bookings.id as booking_id", "bookings.pickup_address", "bookings.arrived_time", "bookings.base_fare_charge", "bookings.waiting_time", "bookings.dropoff_address", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude", "bookings.start_time", "bookings.booking_status", "bookings.cost", "bookings.payment_mode", "bookings.path_image", "bookings.total as totalRideCharge", "bookings.distance as totalDistance", "vehicle_types.id as vehicle_type_id", "vehicle_types.vehicle_type", "vehicle_types.driver_charge", "vehicle_types.price", "vehicle_types.distance_time", "vehicle_types.waiting_charge", "vehicle_types.cancellation_charge", "vehicle_types.per_minute")->join("vehicle_types", "users.vehicle_type_id", "=", "vehicle_types.id")->join("bookings", "users.id", "=", "bookings.driver_id")->where("bookings.id", $bookingId)->first();
		// print_r(DB::getQueryLog());
		return $result;
	}

	public function userBookingDetail($bookingId) {
		return User::select("users.id as user_id", "bookings.name", "bookings.mobile_number", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), "users.payment_method", DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture"), "bookings.device_token", "users.device_type", "users.number_plate", "users.vehicle_type_id", "users.country","users.latitude", "users.email", "users.longitude", "users.referral_code", "users.user_status", "bookings.id as booking_id", "bookings.pickup_address", "bookings.arrived_time", "bookings.base_fare_charge", "bookings.waiting_time", "bookings.dropoff_address", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude", "bookings.path_image", "bookings.start_time", "bookings.cost", "bookings.total as totalRideCharge", "bookings.distance as totalDistance", "vehicle_types.id as vehicle_type_id", "vehicle_types.vehicle_type", "vehicle_types.price", "bookings.booking_status")->leftJoin("vehicle_types", "users.vehicle_type_id", "=", "vehicle_types.id")->join("bookings", "users.id", "=", "bookings.user_id")->where("bookings.id", $bookingId)->first();
	}

	public function cancelUserRide($booking_id, $booking_status, $driver_id, $user_id, $driver_accept_time, $cancel_time, $cancel_charges_time, $vehicle_type_id, $waiting_charge) {
		// echo "fiveMinutes";
		$cancellation_charge = 0;
		if ($driver_accept_time != "") {
			$fiveMinutes = doubleVal($driver_accept_time) + (60000 * $cancel_charges_time);
			// echo "<br>";
			$fiveDate = date("Y-m-d H:i:s", $fiveMinutes / 1000);
			$cancelDate = date("Y-m-d H:i:s", $cancel_time / 1000);
			$cancellation_charge = 0;
			$waiting_chargeSec = $waiting_charge / 60;
			// echo $fiveMinutes . "<" . $cancel_time;
			// echo "<br>";
			if ($fiveMinutes < $cancel_time) {
				// echo $cancelDate;
				// echo "<br>";
				// echo "cancelDate = $cancelDate";
				// echo "<br>";
				// echo "#fiveDate = $fiveDate ";
				// echo "#Calc =";
				// echo (round(abs(strtotime($cancelDate) - strtotime($fiveDate)) / 60));
				// echo "<br>";
				// echo "#waiting_charge = $waiting_charge";
				// echo "<br>";
				// echo "#diff=";
				// echo strtotime($cancelDate) - strtotime($fiveDate);
				// echo "#abs=";
				// echo abs(strtotime($cancelDate) - strtotime($fiveDate));
				// echo "#devideBy60=";
				// echo (abs(strtotime($cancelDate) - strtotime($fiveDate)) / 60);
				// echo "<br>";

				$cancellation_charge = ceil(abs(strtotime($cancelDate) - strtotime($fiveDate)) * $waiting_chargeSec);
			}
		}
		Booking::where("id", $booking_id)->update(["booking_status" => $booking_status, "cancelled_by" => $user_id, "cancellation_charge_next_ride" => $cancellation_charge]);
		User::where("id", $driver_id)->update(["user_status" => 9]);
		// DB::enableQueryLog();
		User::where("id", $user_id)->update(["user_status" => 9, "cancellation_charge" => DB::raw("(cancellation_charge + $cancellation_charge)")]);
		// print_r(DB::getQueryLog());
		UserDriver::where("booking_id", $booking_id)->update(["system_booking_status" => $booking_status]);
		return User::select("users.id as user_id", "users.name", "u1.name as user_name", "users.mobile_number", DB::raw("COALESCE((select ROUND(AVG(ratings.rating)) from ratings where parent_id = users.id),0) as rating"), "users.payment_method", DB::raw("CONCAT('" . url("uploads/profiles") . "/', users.profile_picture) profile_picture"), "users.device_token", "users.device_type", "users.number_plate", "bookings.id as booking_id", "bookings.pickup_latitude", "bookings.pickup_longitude", "bookings.dropoff_latitude", "bookings.dropoff_longitude")->rightJoin("bookings", "users.id", "=", "bookings.driver_id")->join("users as u1", "u1.id", "=", "bookings.user_id")->where("bookings.id", $booking_id)->first();
	}

	public function driverArrivedAtLoc($arrived_time, $user_id, $driver_id, $booking_id, $booking_status) {
		Booking::where("id", $booking_id)->update(["booking_status" => $booking_status, "arrived_time" => $arrived_time]);
		User::where("id", $driver_id)->update(["user_status" => $booking_status]);
		User::where("id", $user_id)->update(["user_status" => $booking_status]);
		UserDriver::where("booking_id", $booking_id)->update(["system_booking_status" => $booking_status]);
		return $this->driverBookingDetail($booking_id);
	}

	public function endUserRide($end_time, $user_id, $driver_id, $booking_id, $booking_status, $dropoff_latitude, $dropoff_longitude, $polyline, $path_image) {
		Booking::where("id", $booking_id)->update(["booking_status" => $booking_status, "end_time" => $end_time, "path_image" => $polyline, "dropoff_latitude" => $dropoff_latitude, "dropoff_longitude" => $dropoff_longitude, "path_image" => $path_image]);
		User::where("id", $driver_id)->update(["user_status" => $booking_status]);
		User::where("id", $user_id)->update(["user_status" => $booking_status]);
		UserDriver::where("booking_id", $booking_id)->update(["system_booking_status" => $booking_status]);
		return $this->driverBookingDetail($booking_id);
	}

	public function finishUserRide($booking_id, $user_id, $driver_id) {
		Booking::where("id", $booking_id)->where("user_id", $user_id)->where("driver_id", $driver_id)->update(["booking_status" => 7]);
		User::where("id", $driver_id)->update(["user_status" => 7]);
		User::where("id", $user_id)->update(["user_status" => 7]);
		UserDriver::where("booking_id", $booking_id)->update(["system_booking_status" => 7]);
		return $this->userBookingDetail($booking_id);

		// $query = $this->db->query("UPDATE `user_driver` ud INNER JOIN bookings b ON (ud.booking_id = b.id) SET  ud.booking_status = 7, b.booking_status = 7 WHERE b.id ='" . $booking_id . "' AND b.user_id = '" . $user_id . "' AND b.driver_id = '" . $driver_id . "'");
	}

	public function rideNextDriverList($pickup_latitude, $pickup_longitude, $vehicle_type_id, $drivers) {
		$userDriver = UserDriver::select(DB::raw("GROUP_CONCAT(DISTINCT user_driver.driver_id) as driver_list"))->join('users', "users.id", "=", "user_driver.driver_id")->whereIn("users.user_status", array(0, 4, 9))->where(["system_booking_status" => 8, "user_driver.booking_status" => 8, "users.vehicle_type_id" => $vehicle_type_id])->first();
		$except_driver_list = "";
		if ($userDriver->count() > 0) {
			$except_driver_list = $userDriver->driver_list;
		}

		if ($except_driver_list == "") {
			$query = DB::select(DB::raw("SELECT (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) AS distance_in_km, users.id,users.name,users.mobile_number,users.device_token,users.device_type,users.vehicle_type_id FROM user_roles JOIN users ON user_roles.user_id=users.id WHERE user_roles.role_id = 3 AND is_verified = 1 AND users.vehicle_type_id = '" . $vehicle_type_id . "' AND users.is_visible = 1 AND users.status = 'AC' and users.id NOT IN($drivers) AND users.user_status IN (0,4,9) AND (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) <= 5 limit 50"));
		} else {
			$query = DB::select(DB::raw("SELECT (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) AS distance_in_km, users.id,users.name,users.mobile_number,users.device_token,users.device_type,users.vehicle_type_id FROM user_roles JOIN users ON user_roles.user_id=users.id WHERE user_roles.role_id = 3 AND is_verified = 1 AND users.vehicle_type_id = '" . $vehicle_type_id . "' AND users.is_visible = 1 and users.id NOT IN($drivers) AND users.id NOT IN ($except_driver_list) AND users.status = 'AC' AND users.user_status IN (0,4,9) AND (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) <= 5 limit 50"));
		}
		if (count($query) > 0) {
			return $query;
		}
	}

	public function rideDriverList($pickup_latitude, $pickup_longitude, $vehicle_type_id) {
		$userDriver = UserDriver::select(DB::raw("GROUP_CONCAT(DISTINCT user_driver.driver_id) as driver_list"))->join('users', "users.id", "=", "user_driver.driver_id")->whereIn("users.user_status", array(0, 4, 9))->where(["system_booking_status" => 8, "user_driver.booking_status" => 8, "users.vehicle_type_id" => $vehicle_type_id])->first();
		$except_driver_list = "";
		if ($userDriver->count() > 0) {
			$except_driver_list = $userDriver->driver_list;
		}

		if ($except_driver_list == "") {
			$query = DB::select(DB::raw("SELECT (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) AS distance_in_km, users.id,users.name,users.mobile_number,users.device_token,users.device_type,users.vehicle_type_id FROM user_roles JOIN users ON user_roles.user_id=users.id WHERE user_roles.role_id = 3 AND is_verified = 1 AND users.vehicle_type_id = '" . $vehicle_type_id . "' AND users.is_visible = 1 AND users.status = 'AC' AND users.user_status IN (0,4,9) AND (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) <= 5 limit 50"));
		} else {
			$query = DB::select(DB::raw("SELECT (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) AS distance_in_km, users.id,users.name,users.mobile_number,users.device_token,users.device_type,users.vehicle_type_id FROM user_roles JOIN users ON user_roles.user_id=users.id WHERE user_roles.role_id = 3 AND is_verified = 1 AND users.vehicle_type_id = '" . $vehicle_type_id . "' AND users.is_visible = 1 AND users.id NOT IN ($except_driver_list) AND users.status = 'AC' AND users.user_status IN (0,4,9) AND (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($pickup_latitude))* COS(RADIANS(users.longitude - $pickup_longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($pickup_latitude))))) <= 5 limit 50"));
		}
		if (count($query) > 0) {
			return $query;
		}
	}

	function cabsList($country, $state, $city, $latitude, $longitude) {
		// $cabs = VehicleType::select("vehicle_types.*", "users.id")->join("users", "users.vehicle_type_id", "=", "vehicle_types.id")->join("user_roles", "users.id", "=", "user_roles.user_id")->where(["vehicle_types.status" => 'AC', "vehicle_types.state" => $state, "vehicle_types.city" => $city, "users.is_visible" => 1, "user_roles.role_id" => 3, "users.is_verified" => 1, "users.status" => "AC"])->groupBy("vehicle_types.id")->get();
		// print_r($cabs->toArray());

		$query = DB::select(DB::raw("SELECT vehicle_types.*, CONCAT('" . url("uploads/vehicles") . "/', vehicle_types.image) image, users.id user_id FROM user_roles JOIN users ON user_roles.user_id=users.id RIGHT JOIN vehicle_types ON users.vehicle_type_id = vehicle_types.id WHERE vehicle_types.status='AC' and vehicle_types.country='" . $country . "' and vehicle_types.state='" . $state . "' and vehicle_types.city='" . $city . "' and users.is_visible = 1 and user_roles.role_id = 3 AND users.is_verified = 1 AND users.status = 'AC' AND (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($latitude))* COS(RADIANS(users.longitude - $longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($latitude))))) <= 10 group by vehicle_types.id order by (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($latitude))* COS(RADIANS(users.longitude - $longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($latitude))))) asc"));
		if (count($query) > 0) {
			return $query;
		}
	}
//, DB::raw("CONCAT('" . url("uploads/vehicles/aerial") . "/', aerial_image) aerial_image")
	function driversList($country, $state, $city, $latitude, $longitude) {
		$query = DB::select(DB::raw("SELECT (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($latitude))* COS(RADIANS(users.longitude - $longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($latitude))))) AS distance_in_km, users.id,users.name,users.email,users.mobile_number,users.device_token,users.device_type,users.vehicle_type_id,users.password, users.registration_number,users.number_plate,users.vehicle_manufacturer,users.vehicle_model,users.vehicle_color,users.latitude,users.longitude,users.payment_method,users.is_verified,users.is_visible,CONCAT('" . url("uploads/vehicles/aerial") . "/', aerial_image) as aerial_image FROM user_roles JOIN users ON user_roles.user_id=users.id join vehicle_types ON vehicle_types.id=vehicle_type_id WHERE users.is_visible = 1 and users.country='$country' and users.state='$state' and users.city='$city' and user_roles.role_id = 3 AND users.is_verified = 1 AND users.status = 'AC' AND (111.111 *DEGREES(ACOS(COS(RADIANS(users.latitude))* COS(RADIANS($latitude))* COS(RADIANS(users.longitude - $longitude))+ SIN(RADIANS(users.latitude)) * SIN(RADIANS($latitude))))) <= 5 limit 50"));
		if (count($query) > 0) {
			return $query;
		}
	}

	public function GetDrivingDistance($pickup_latitude, $pickup_longitude, $dropoff_latitude, $dropoff_longitude) {
		$googleMapKey = $this->getBusRuleRef("google_map_key");
		// $pickup_latitude = $this->post('pickup_latitude');
		// $pickup_longitude = $this->post('pickup_longitude');
		// $dropoff_latitude = $this->post('dropoff_latitude');
		// $dropoff_longitude = $this->post('dropoff_longitude');
		// $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyB7dSsZz15ODyMS0K_Yk-Th9_MobW4_DrE&units=imperial&origins=" . $pickup_latitude . "," . $pickup_longitude . "&destinations=" . $dropoff_latitude . "," . $dropoff_longitude . "&mode=driving";
		// 1 mi = 1.60934 km

		// $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $pickup_latitude . "," . $pickup_longitude . "&destinations=" . $dropoff_latitude . "," . $dropoff_longitude . "&mode=driving";

		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?mode=driving&transit_mode=bus&origins=$pickup_latitude,$pickup_longitude&destinations=$dropoff_latitude,$dropoff_longitude&key=$googleMapKey";
		// echo $url;
		// echo "<br><br>";
		// $url = "https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyB7dSsZz15ODyMS0K_Yk-Th9_MobW4_DrE&units=imperial&origins=" . $pickup_latitude . "," . $pickup_longitude . "&destinations=" . $dropoff_latitude . "," . $dropoff_longitude . "";

		//https://maps.googleapis.com/maps/api/distancematrix/json?key=AIzaSyB7dSsZz15ODyMS0K_Yk-Th9_MobW4_DrE&units=imperial&origins=26.876392515750748,75.81577733159065&destinations=26.9095272,75.82362169999999

		// echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		// print_r($response);
		curl_close($ch);
		return json_decode($response, true);
	}

	public function getDirection($pickup_latitude, $pickup_longitude, $dropoff_latitude, $dropoff_longitude, $via) {
		$googleMapKey = $this->getBusRuleRef("google_map_key");
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=$pickup_latitude,$pickup_longitude&destination=$dropoff_latitude,$dropoff_longitude&mode=driving&waypoints=$via&departure_time=now&key=$googleMapKey";
		// echo "<br>";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		// echo $response;
		curl_close($ch);
		return json_decode($response, true);
	}

	public function getReferMessage($user_id) {
		$user = User::select("referral_code")->where("id", $user_id)->first();
		$message = $this->getBusRuleRef("refer_share_message");
		$androidLink = $this->getBusRuleRef("android_url_user");
		$iosLink = $this->getBusRuleRef("ios_url_user");
		$referrerAmount = $this->getBusRuleRef("referrer_amount");
		// Register on NXG Charge with refer_code and earn Rs. refer_amount. Download on Android android_link or iOS ios_link
		$message = str_replace("refer_code", $user->referral_code, $message);
		$message = str_replace("refer_amount", $referrerAmount, $message);
		$message = str_replace("android_link", $androidLink, $message);
		$message = str_replace("ios_link", $iosLink, $message);
		return $message;
	}

	public function send_sms_forgot($number, $otp) {
		$apiKey = urlencode($this->getBusRuleRef("sms_key"));
		$msg = $otp . " is your verification OTP for forgot password into the NXG Charge app. Do not share this OTP with anyone.";
		// Message details
		$numbers = array($number);
		$sender = urlencode($this->getBusRuleRef("sms_sender_id"));
		$message = rawurlencode($msg);

		$numbers = implode(',', $numbers);

		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

		$username = urlencode("r3620");
		$msg_token = urlencode("d430Nu");
		$sender_id = urlencode("vlogic");
		$message = urlencode($msg);
		$mobile = urlencode($numbers);
		$ch = curl_init("http://manage.sarvsms.com/api/send_priority_sms.php?username=" . $username . "&msg_token=" . $msg_token . "&sender_id=" . $sender_id . "&message=" . $message . "&mobile=" . $mobile . "");

		// curl_setopt($ch, CURLOPT_POST, true);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		// print_r($response);

		// Process your response here
		// echo $response;
		// die("-1-");
	}

	public function send_sms($number, $otp) {
		$apiKey = urlencode($this->getBusRuleRef("sms_key"));
		$msg = $otp . " is your verification OTP for registering into the NXG Charge app. Do not share this OTP with anyone.";
		// Message details
		$numbers = array($number);
		$sender = urlencode($this->getBusRuleRef("sms_sender_id"));
		$message = rawurlencode($msg);

		$numbers = implode(',', $numbers);

		// Prepare data for POST request
		$data = array('username' => urlencode("r3620"), 'msg_token' => $apiKey, 'mobile' => $numbers, "sender_id" => $sender, "message" => $message);

		$username = urlencode("r3620");
		$msg_token = urlencode("d430Nu");
		$sender_id = urlencode("vlogic");
		$message = urlencode($msg);
		$mobile = urlencode($numbers);
		$ch = curl_init("http://manage.sarvsms.com/api/send_priority_sms.php?username=" . $username . "&msg_token=" . $msg_token . "&sender_id=" . $sender_id . "&message=" . $message . "&mobile=" . $mobile . "");
		// curl_setopt($ch, CURLOPT_POST, true);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		// print_r($response);

		// Process your response here
		// echo $response;
		// die("-1-");
	}

	public function sendFirebaseNotification($user, $msgarray, $fcmData) {
		$url = 'https://fcm.googleapis.com/fcm/send';

		$fcmApiKey = $this->getBusRuleRef("fcm_api_key");
		// echo "user";
		// echo "<pre>";
		// print_r($user);
		// echo "</pre>";
		// echo "\n";
		$fcmMsg = array(
			'title' => $msgarray['title'],
			'text' => $msgarray['msg'],
			'type' => $msgarray['type'],
			'vibrate' => 1,
			"date_time" => date("Y-m-d H:i:s"),
			'message' => $fcmData,
		);
		// echo "fcmMsg";
		// print_r($fcmMsg);
		// echo "\n";
		if ($user->device_type == "ios") {
			$fcmFields = array(
				'to' => $user->device_token,
				'priority' => 'high',
				'notification' => $fcmMsg,
				'data' => $fcmMsg,
			);
		} else {
			$fcmFields = array(
				'to' => $user->device_token,
				'priority' => 'high',
				'data' => $fcmMsg,
			);
		}
		// echo "fcmFields";
		// print_r($fcmFields);
		// echo "\n";

		$headers = array(
			'Authorization: key=' . $fcmApiKey,
			'Content-Type: application/json',
		);
		// print_r($driver->device_token);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
		$result = curl_exec($ch);
		//print_r($result);
		// print_r($user);
		if ($result === FALSE) {
			// die('Curl failed: ' . curl_error($ch));
		}
		curl_close($ch);
		// echo "\n##################################################################################################";
		// echo "\n\n\n";
		return $result;
	}

	public function sendBulkFirebaseNotification($user, $msgarray, $fcmData) {
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fcmApiKey = $this->getBusRuleRef("fcm_api_key");
		$fcmMsg = array(
			'title' => $msgarray['title'],
			'text' => $msgarray['msg'],
			'type' => $msgarray['type'],
			'vibrate' => 1,
			"date_time" => date("Y-m-d H:i:s"),
			'message' => $fcmData,
		);
		if ($user->device_type == "ios") {
			$fcmFields = array(
				'registration_ids' => $user->device_token,
				'priority' => 'high',
				'notification' => $fcmMsg,
				'data' => $fcmMsg,
			);
		} else {
			$fcmFields = array(
				'registration_ids' => $user->device_token,
				'priority' => 'high',
				'data' => $fcmMsg,
			);
		}
		$headers = array(
			'Authorization: key=' . $fcmApiKey,
			'Content-Type: application/json',
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	public function sendTestNotification() {
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fcmApiKey = $this->getBusRuleRef("fcm_api_key");
		$fcmMsg = array(
			'title' => "hello1",
			'text' => "hello2",
			'type' => "hello3",
			'vibrate' => 1,
			"date_time" => date("Y-m-d H:i:s"),
			'message' => "hello",
		);
		// if ($driver->device_type == "ios") {
		//     $fcmFields = array(
		//         'to' => "dHFXcT7m9EI:APA91bFb7b7xCJjo2GTNAEV9GpPiiqFsvTGuSh7ecjwQK2AgaMVFj4nGBp_lRmKEfhp5H6hL9CHfa-xP-MdarowP8JYxPEVOyCVYkQuZE1sxBb5WG_jDZrMjFGB4bRGraTJYpU1kzNe2",
		//         'priority' => 'high',
		//         'notification' => "hi",
		//         'data' => "hello",
		//     );
		// } else {
		$fcmFields = array(
			'to' => "dHFXcT7m9EI:APA91bFb7b7xCJjo2GTNAEV9GpPiiqFsvTGuSh7ecjwQK2AgaMVFj4nGBp_lRmKEfhp5H6hL9CHfa-xP-MdarowP8JYxPEVOyCVYkQuZE1sxBb5WG_jDZrMjFGB4bRGraTJYpU1kzNe2",
			'priority' => 'high',
			'data' => $fcmMsg,
		);
		// }

		$headers = array(
			'Authorization: key=' . $fcmApiKey,
			'Content-Type: application/json',
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
		$result = curl_exec($ch);
		print_r($result);
		echo "\n\n\n";
		if ($result === FALSE) {
			// die('Curl failed: ' . curl_error($ch));
		}
		curl_close($ch);

		return $result;
	}
}
