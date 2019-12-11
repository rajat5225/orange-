<?php

namespace App\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model {
	protected $table = 'vehicle_types';

	public function cityState() {
		return $this->belongsTo(City::class, "city", "name");
	}

	public function fetchVehicles($city, $state, $country) {
		$query = VehicleType::with('cityState');

		if ($city != "all") {
			$query->where('city', $city);
		} else {
			if ($state != "all") {
				//subquery in whereIn to fetch city names from state id fetched from state name
				/*$query->whereIn('city', function($q) use($state){
	        			$q->select('name')->from('cities')->where('state_id', function($q) use($state){
	        				$q->select('id')->from('states')->where('name', $state);
	        			});
*/
				$query->where('state', $state);
			} else {
				if ($country != 'all') {
					$query->where('country', $country);
				}

			}
		}

		$vehicles = $query->where('status', '!=', 'DL')->orderBy('created_at', 'desc');
		return $vehicles;
	}
	public function fetchVehiclesTemp($city, $state) {
		$query = VehicleType::with('cityState');

		if ($city != "all") {
			$query->where('city', $city);
		} else {
			if ($state != "all") {
				//subquery in whereIn to fetch city names from state id fetched from state name
				/*$query->whereIn('city', function($q) use($state){
	        			$q->select('name')->from('cities')->where('state_id', function($q) use($state){
	        				$q->select('id')->from('states')->where('name', $state);
	        			});
*/
				$query->where('state', $state);
			}
		}

		$vehicles = $query->where('status', 'AC')->orderBy('created_at', 'desc')->get();
		return $vehicles;
	}
	public function fetchVehiclesByCityState($city, $state) {
		DB::enableQueryLog();
		$vehicles = VehicleType::where(['state' => $state, 'city' => $city])->get();
		print_r(DB::getQueryLog());
		return $vehicles;
	}

}
