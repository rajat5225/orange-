<?php

namespace App\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model {
	protected $table = 'bookings';

	public function user() {
		return $this->belongsTo(User::class, 'user_id');
	}

	public function driver() {
		return $this->belongsTo(User::class, 'driver_id');
	}

	public function vehicle() {
		return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
	}

	public function coupon_code() {
		return $this->belongsTo(CouponCode::class, 'coupon_code_id');
	}

	public function reviews() {
		return $this->hasMany(Rating::class);
	}

	public function booking_support() {
		return $this->hasMany(BookingSupport::class);
	}

	public function fetchRides($city, $state, $country, $request) {
		DB::enableQueryLog();
		$query = Booking::with(['driver', 'vehicle', 'coupon_code', 'user']);

		if ($city != "all") {
			$query->whereHas('user', function ($q) use ($city) {
				$q->where('city', $city);
			});
		} else {
			if ($state != "all") {
				$query->whereHas('user', function ($q) use ($state) {
					$q->where('state', $state);
				});
			} else {
				if ($country != "all") {
					$query->whereHas('user', function ($q) use ($country) {
						$q->where('country', $country);
					});
				}
			}
		}
		if (isset($request->from_date)) {
			$query->where('created_at', '>=', date("Y-m-d", strtotime($request->from_date)));
		}
		if (isset($request->end_date)) {
			$query->where('created_at', '<=', date("Y-m-d", strtotime($request->end_date)));
		}
		if (isset($request->user)) {
			$query->where('user_id', '=', $request->user);
		}
		if (isset($request->driver)) {
			$query->where('driver_id', '=', $request->driver);
		}
		if (isset($request->booking_status)) {
			$query->where('booking_status', '=', $request->booking_status);
		}

		$rides = $query->where('status', '!=', 'DL')->orderBy('created_at', 'desc')->get();
		// print_r(DB::getQueryLog());
		return $rides;
	}
}
