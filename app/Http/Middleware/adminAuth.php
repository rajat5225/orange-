<?php

namespace App\Http\Middleware;
use App\Model\City;
use App\Model\Country;
use App\Model\State;
use Auth;
use Closure;
use Config;
use Session;

class adminAuth {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

		if (Auth::check()) {
			if (Auth::user()->user_role[0]->role != 'admin') {
				Auth::logout();
				$request->session()->flash('danger', "You don't have permission to access this panel");
				return redirect()->route('index');
			}

			//update config variable for countries, states and cities to be managed in complete admin panel
			$cities = [];
			$states = [];
			if (Session::get('globalState') != "" && Session::get('globalState') != "all") {
				$cities = City::whereHas('state', function ($query) {
					$query->where('states.name', Session::get('globalState'));
				})->where("status", "AC")->pluck('name');
			}
			if (Session::get('globalCountry') != "" && Session::get('globalCountry') != "all") {
				$states = State::whereHas('country', function ($query) {
					$query->where('countries.name', Session::get('globalCountry'));
				})->where("status", "AC")->pluck('name');
			}
			$configData = [
				'countries' => Country::pluck('name'),
				'states' => $states,
				'cities' => $cities,
			];
			config(['statecity' => $configData]);

			// $currency = BusRuleRef::where('rule_name', 'currency')->first()->rule_value;
			// config(['constants.CURRENCY' => $currency]);
			// Session::put('currency', $currency);
		}
		if (Session::get('lastlogin') && (time() - Session::get('lastlogin')) > (Config::get('session.lifetime') * 60 * 1000)) {
			$request->session()->flush();
			Auth::logout();
			$request->session()->flash('error', 'Your session timeout. Please login again.');
		}
		return $next($request);
	}
}
