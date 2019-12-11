<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {
	protected $table = 'countries';
	// protected $with = ['state'];

	public function state() {
		return $this->hasMany(State::class);
	}
	public static function getStates($country) {
		$states = State::whereHas('country', function ($query) use ($country) {
			$query->where('countries.name', $country);
		})->pluck('name');
		return $states;
	}
	public function fetchCountries() {
		$countries = Country::where('status', '!=', 'DL')->get();
		return $countries;
	}
}
