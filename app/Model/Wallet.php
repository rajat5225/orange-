<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model {
	protected $table = 'wallets';

	public function getBooking() {
		return $this->belongsTo("App\Model\Booking", "booking_id");
	}

	public function getTransaction() {
		return $this->belongsTo("App\Model\Transaction", "transaction_id");
	}

	public function getReferrer() {
		return $this->belongsTo("App\Model\ReferrerUser", "referrer_user_id");
	}
}
