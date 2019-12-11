<?php

namespace App\Model;

use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	// protected $with = ["city"];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	public function user_role() {
		return $this->belongsToMany('App\Model\Role', 'user_roles');
	}

	public function user_doc() {
		return $this->hasMany('App\Model\UserDocument')->where('status', '!=', 'DL');
	}

	public function vehicle_type() {
		return $this->belongsTo('App\Model\VehicleType');
	}

	public function bookings() {
		return $this->hasOne("App\Model\Booking");
	}

	public function all_booking() {
		return $this->hasMany(Booking::class, 'user_id', 'id');
	}

	public function trusted_contact() {
		return $this->hasMany(TrustedContact::class, 'user_id', 'id');
	}

	public function user_reviews() {
		return $this->hasMany(Rating::class, 'user_id', 'id');
	}

	public function parent_reviews() {
		return $this->hasMany(Rating::class, 'parent_id', 'id');
	}

	public function avgRating() {

		return $this->hasMany(Rating::class, 'parent_id', 'id')->select('id', DB::raw('round(avg(rating)) as avg_rating'), 'parent_id')->groupBy('parent_id');
	}

	/**
	 * Get all of the support requests for the user.
	 */
	public function book_support() {
		return $this->hasManyThrough('App\Model\BookingSupport', 'App\Model\Booking');
	}

	public function fetchUsers($city, $state, $country, $usertype, $request) {
		DB::enableQueryLog();
		$query = User::whereHas('user_role', function ($query) use ($usertype) {
			$query->where('role', $usertype);
		})
			->with('avgRating');
		if ($city != "all") {
			$query->where('city', $city);
		} else {
			if ($state != "all") {
				//subquery in whereIn to fetch city names from state id fetched from state name
				/*$query->whereIn('city', function ($q) use ($state) {
					$q->select('name')->from('cities')->where('state_id', function ($q) use ($state) {
						$q->select('id')->from('states')->where('name', $state);
					});
				});*/
				$query->where('state', $state);
			} else {
				if ($country != "all") {
					//subquery in whereIn to fetch city names from state id fetched from state name
					/*$query->whereIn('city', function ($q) use ($state) {
					$q->select('name')->from('cities')->where('state_id', function ($q) use ($state) {
						$q->select('id')->from('states')->where('name', $state);
					});
				});*/
					$query->where('country', $country);
				}
			}
		}
		if (isset($request->from_date)) {
			$query->where('created_at', '>=', date("Y-m-d", strtotime($request->from_date)));
		}
		if (isset($request->end_date)) {
			$query->where('created_at', '<=', date("Y-m-d", strtotime($request->end_date)));
		}
		if (isset($request->user_status)) {
			$query->where('user_status', '=', $request->user_status);
		}

		$users = $query->where('status', '!=', 'DL')->orderBy('created_at', 'desc')->get();

		return $users;
	}

}
