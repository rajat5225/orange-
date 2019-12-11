<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
	protected $table = 'ratings';

	public function user() {
		return $this->belongsTo(User::class, 'user_id');
	}

	public function parent() {
		return $this->belongsTo(User::class, 'parent_id');
	}

	public function booking() {
		return $this->belongsTo(Booking::class, 'booking_id');
	}

	public function complement() {
		return $this->belongsTo(Complement::class);
	}

	public function fetchRatings($city, $state, $country) {
		$query = Rating::with('booking');

		if ($city != "all") {
			$query->whereHas('booking.user', function ($q) use ($city) {
				$q->where('city', $city);
			});
		} else {
			if ($state != "all") {
				$query->whereHas('booking.user', function ($q) use ($state) {
					$q->where('state', $state);
				});
			} else {
				if ($country != "all") {
					$query->whereHas('booking.user', function ($q) use ($country) {
						$q->where('country', $country);
					});
				}
			}
		}

		$ratings = $query->where('status', '!=', 'DL')->orderBy('created_at', 'desc')->get();
		return $ratings;
	}
}
