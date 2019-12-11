<?php

namespace App\Http\Controllers;
use App\Model\City;
use App\Model\Country;
use App\Model\State;
use Illuminate\Http\Request;
use Session;

class StateCityController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}

	/**
	 * fetch cities from state selected
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function fetch(Request $request) {
		Session::put('globalState', $request->state);

		$cities = City::whereHas('state', function ($query) use ($request) {
			$query->where('name', $request->state)->where("status", "AC");
		})->pluck('name');

		$configData = [
			'states' => State::whereHas('country', function ($query) use ($request) {
				$query->where('name', 'India')->where("status", "AC");
			})->pluck('name'),
			'cities' => $cities,
		];
		config(['statecity' => $configData]);
		Session::put('globalCity', 'all');

		echo json_encode($cities);
	}
	/**
	 * fetch states from country selected
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function fetchStates(Request $request) {
		Session::put('globalCountry', $request->country);

		$currency = Country::where('name', $request->country)->first()->currency_symbol;
		Session::put('currency', $currency);

		$states = State::whereHas('country', function ($query) use ($request) {
			$query->where('name', $request->country)->where("status", "AC");
		})->pluck('name');

		$configData = [
			'countries' => Country::pluck('name'),
			'states' => $states,
			'cities' => [],
		];
		config(['statecity' => $configData]);
		Session::put('globalState', 'all');

		echo json_encode($states);
	}

	/**
	 * set city session
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function setSession(Request $request) {
		Session::put('globalCity', $request->city);

		echo Session::get('globalCity');
	}
}
