<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
	protected $table = 'transactions';

	public function user() {
		return $this->belongsTo(User::class, 'user_id');
	}

	public function fetchTransactions($city, $state, $country, $request) {
		$query = Transaction::with(['user']);

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
		if (isset($request->transaction_status)) {
			$query->where('transaction_status', '=', $request->transaction_status);
		}

		$transactions = $query->orderBy('created_at', 'desc')->get();
		return $transactions;
	}
}
