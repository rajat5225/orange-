<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model {
	protected $table = 'coupon_codes';

	public function bookings() {
		return $this->hasMany(Booking::class, "coupon_code_id");
	}

	public function userCouponCode() {
		return $this->belongsToMany('App\Model\User', 'user_coupon_code');
	}

	public function fetchCodes($city, $state, $country) {
		$query = CouponCode::where('id', '!=', 0);

		if ($city != "all") {
			$query->where('city', $city);
		} else {
			if ($state != "all") {
				$query->where('state', $state);
			} else {
				if ($country != "all") {
					$query->where('country', $country);
				}
			}
		}

		$vehicles = $query->where('status', '!=', 'DL')->orderBy('created_at', 'desc')->get();
		return $vehicles;
	}
}
