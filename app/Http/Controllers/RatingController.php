<?php

namespace App\Http\Controllers;

use App\Model\City;
use App\Model\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RatingController extends Controller {
	public $rating;
	public $city;
	public $state;
	public $country;

	public function __construct() {
		$this->rating = new Rating;
		$this->city = Session::get('globalCity');
		$this->state = Session::get('globalState');
		$this->country = Session::get('globalCountry');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$ratings = $this->rating->fetchRatings($this->city, $this->state, $this->country);
		// dd($ratings);
		return view('ratings.index', compact('ratings'));
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
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$rating = Rating::with(['complement', 'booking'])->where('id', $id)->first();
			// dd($rating);

			if (isset($rating->id)) {
				return view('ratings.view', compact('rating'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('ratings');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('ratings');
		}
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
}
