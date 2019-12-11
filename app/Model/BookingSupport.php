<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BookingSupport extends Model {
	protected $table = "booking_supports";

	public function booking() {
		return $this->belongsTo(Booking::class);
	}

	public function subject() {
		return $this->belongsTo(SupportSubject::class);
	}

	public function fetchSupports($city, $state, $country) {
		$query = BookingSupport::with(['booking', 'subject']);

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

		$rides = $query->where('status', '!=', 'DL')->orderBy('created_at', 'desc')->get();
		return $rides;
	}
}
