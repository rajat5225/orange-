<?php

namespace App\Http\Controllers;

use App\Model\City;
use App\Model\Country;
use App\Model\CouponCode;
use App\Model\State;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class CouponCodeController extends Controller {
	public $code;
	public $city;
	public $state;
	public $country;

	public function __construct() {
		$this->code = new CouponCode;
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
		//get coupon codes for the selected state/city
		$codes = $this->code->fetchCodes($this->city, $this->state, $this->country);
		return view('coupon_codes.index', compact('codes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$type = 'add';
		$url = route('addCouponCode');
		$code = new CouponCode;
		return view('coupon_codes.create', compact('type', 'url', 'code'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$rule1 = '';
		$rule2 = '';
		// dd($request->post('val-minRides'));
		if ($request->post('val-type') == 'rides') {
			$rule1 = 'required|';
		}
		if ($request->post('val-type') == 'usage') {
			$rule2 = 'required|';
		}

		$validate = Validator($request->all(), [
			'val-code' => 'required|size:6|alpha_num',
			'val-title' => 'required|string|max:200',
			'val-description' => 'required|string',
			'val-couponTerms' => 'required|string',
			'val-amt' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			// 'val-maxamt' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			'val-amounttype' => 'required',
			'val-type' => 'required',
			'val-discount' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			'val-rides' => $rule1 . '|digits_between:1,10|integer|nullable',
			'val-applies' => 'required|digits_between:1,10|integer',
			'val-minRides' => $rule2 . '|digits_between:1,10|integer|nullable',
			'val-startDate' => 'required|date',
			'val-endDate' => 'required|date',
		]);

		$attr = [
			'val-code' => 'Coupon Code',
			'val-title' => 'Title',
			'val-description' => 'Description',
			'val-couponTerms' => 'Terms Of Use',
			'val-amt' => 'Minimum Amount',
			// 'val-maxamt' => 'Maximum Amount',
			'val-amounttype' => 'Amount Type',
			'val-type' => 'Discount Type',
			'val-discount' => 'Discount Value',
			'val-rides' => 'No. of Rides',
			'val-applies' => 'No. of Applies',
			'val-minRides' => 'Minimum No. of Rides for Usage',
			'val-startDate' => 'Start Date',
			'val-endDate' => 'End Date',
		];
		$validate->setAttributeNames($attr);
		// dd($validate->errors());
		if ($validate->fails()) {
			return redirect()->route('createCouponCode')->withInput($request->all())->withErrors($validate);
		} else {
			//check for globalized state and city and save coupon for all cities(if more than one - if state is selected and city is all)
			$state = Session::get('globalState');
			$city = Session::get('globalCity');
			$country = Session::get('globalCountry');

			if ($state != 'all') {
				try {
					if ($city == 'all') {
						$city = config('statecity.cities');
					} else {
						$city = (array) $city;
						$validate = CouponCode::where('city', $city)
							->where('state', $state)
							->where('coupon_code', strtoupper($request->post('val-code')))
							->where(function ($q) use ($request) {
								$q->whereBetween('start_date', [$request->post('val-startDate'), $request->post('val-endDate')])
									->orWhereBetween('end_date', [$request->post('val-startDate'), $request->post('val-endDate')]);
							})
							->where('status', '!=', 'DL')->count();
						if ($validate > 0) {
							$request->session()->flash('error', 'This code was already added in the city:' . implode(",", $city) . ' within the selected time frame.');
							return redirect()->route('createCouponCode')->withInput($request->all());
						}
					}

					$failCount = 0;
					$failCity = array();

					foreach ($city as $value) {
						DB::enableQueryLog();
						$validate = CouponCode::where('city', $value)
							->where('state', $state)
							->where('coupon_code', strtoupper($request->post('val-code')))
							->where(function ($q) use ($request) {
								$q->whereBetween('start_date', [$request->post('val-startDate'), $request->post('val-endDate')])
									->orWhereBetween('end_date', [$request->post('val-startDate'), $request->post('val-endDate')]);
							})
							->where('status', '!=', 'DL')->count();
						// dd($validate);
						if ($validate <= 0) {

							$code = new CouponCode;
							$code->coupon_code = strtoupper($request->post('val-code'));
							$code->title = $request->post('val-title');
							$code->description = $request->post('val-description');
							$code->terms = $request->post('val-couponTerms');
							$code->min_amount = $request->post('val-amt');
							$code->max_amount = $request->post('val-maxamt');
							$code->start_date = date('Y-m-d', strtotime($request->post('val-startDate')));
							$code->end_date = date('Y-m-d', strtotime($request->post('val-endDate')));
							$code->amount_type = $request->post('val-amounttype');
							$code->discount_type = $request->post('val-type');
							$code->no_of_rides = ($code->discount_type == 'rides') ? $request->post('val-rides') : 0;
							$code->min_rides = ($code->discount_type == 'usage') ? $request->post('val-minRides') : 1;
							$code->discount_value = $request->post('val-discount');
							$code->no_of_applies = ($code->discount_type != 'rides') ? $request->post('val-applies') : 1;
							$code->status = ($request->post('val-status') == 'on') ? 'AC' : 'IN';
							$code->city = $value;
							$code->state = $state;
							$code->country = $country;
							$code->created_at = date('Y-m-d H:i:s');
							$code->save();
						} else {
							$failCount++;
							$failCity[] = $value;
						}
					}

					if ($failCount > 0 && count($failCity) > 0) {
						$request->session()->flash('failCouponCity', implode(',', $failCity));
					}

					if (count($failCity) != count($city)) {
						$request->session()->flash('success', 'Coupon Code added successfully for selected State/City');
					}

					return redirect()->route('couponCodes');
				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('couponCodes');
				}
			} else {
				$request->session()->flash('error', 'Coupon Code not stored. Please select state before adding any code.');
				return redirect()->route('couponCodes');
			}

		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$code = CouponCode::with('bookings', 'bookings.user')->where('id', $id)->first();
			// dd($code->bookings);
			if (isset($code->id)) {
				return view('coupon_codes.view', compact('code'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('couponCodes');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('couponCodes');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$code = CouponCode::where('id', $id)->first();

			if (isset($code->id)) {
				if ($code->status != 'DL') {
					$cities = State::getCities($code->state);
					$states = Country::getStates($code->country);
					$type = 'edit';
					$url = route('updateCouponCode') . '/' . $id;
					return view('coupon_codes.create', compact('code', 'type', 'url', 'cities', 'states'));
				} else {
					$request->session()->flash('error', 'Invalid Data');
					return redirect()->route('couponCodes');
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('couponCodes');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('couponCodes');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id = null) {
		if (isset($id) && $id != null) {

			$code = CouponCode::where('id', $id)->first();
			if (isset($code->id)) {
				$rule1 = '';
				$rule2 = '';

				if ($request->post('val-type') == 'rides') {
					$rule1 = 'required|';
				}
				if ($request->post('val-type') == 'usage') {
					$rule2 = 'required|';
				}

				$validate = Validator($request->all(), [
					'val-code' => 'required|size:6|alpha_num',
					'val-title' => 'required|string|max:200',
					'val-description' => 'required|string',
					'val-couponTerms' => 'required|string',
					'val-amt' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
					'val-amounttype' => 'required',
					'val-type' => 'required',
					'val-discount' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
					'val-rides' => $rule1 . '|digits_between:1,10|integer|nullable',
					'val-applies' => 'required|digits_between:1,10|integer',
					'val-minRides' => $rule2 . '|digits_between:1,10|integer|nullable',
					'val-startDate' => 'required|date',
					'val-endDate' => 'required|date',
					'city' => ['required', Rule::unique('coupon_codes')->where(function ($query) use ($request, $code) {
						return $query->where('city', $request->post('city'))
							->where('state', $request->state)
							->where('coupon_code', strtoupper($request->post('val-code')))
							->where(function ($q) use ($request) {
								$q->whereBetween('start_date', [$request->post('val-startDate'), $request->post('val-endDate')])
									->orWhereBetween('end_date', [$request->post('val-startDate'), $request->post('val-endDate')]);
							})
							->where('id', '!=', $code->id)
							->where('status', '!=', 'DL');
					})],
				]);

				$attr = [
					'val-code' => 'Coupon Code',
					'val-title' => 'Title',
					'val-description' => 'Description',
					'val-couponTerms' => 'Terms Of Use',
					'val-amt' => 'Minimum Amount',
					'val-type' => 'Discount Type',
					'val-discount' => 'Discount Value',
					'val-rides' => 'No. of Rides',
					'val-applies' => 'No. of Applies',
					'val-minRides' => 'Minimum No. of Rides for Usage',
					'val-startDate' => 'Start Date',
					'val-endDate' => 'End Date',
				];
				$validate->setAttributeNames($attr);
				// dd($validate->errors());
				if ($validate->fails()) {
					return redirect()->route('editCouponCode')->withInput($request->all())->withErrors($validate);
				} else {
					//check for globalized state and city and save coupon for all cities(if more than one - if state is selected and city is all)
					$state = $request->post('val-state');
					$city = $request->post('city');
					$country = $request->post('val-country');

					if ($state != 'all') {
						try {
							if ($city == 'all') {
								$city = City::whereHas('state', function ($query) use ($state) {
									$query->where('name', $state);
								})->pluck('name');
							} else {
								$city = (array) $city;
							}
							$iterateCount = 1;
							$failCount = 0;
							$failCity = array();

							foreach ($city as $value) {
								DB::enableQueryLog();
								$validate = CouponCode::where('city', $value)
									->where('state', $state)
									->where('coupon_code', strtoupper($request->post('val-code')))
									->where(function ($q) use ($request) {
										$q->whereBetween('start_date', [$request->post('val-startDate'), $request->post('val-endDate')])
											->orWhereBetween('end_date', [$request->post('val-startDate'), $request->post('val-endDate')]);
									})
									->where('id', '!=', $code->id)
									->where('status', '!=', 'DL')->count();
								// dd($validate);
								if ($validate <= 0) {

									if ($iterateCount != 1 && count($city) > 1) {
										$code = new CouponCode;
										$code->created_at = date('Y-m-d H:i:s');

									}
									$code->coupon_code = strtoupper($request->post('val-code'));
									$code->title = $request->post('val-title');
									$code->description = $request->post('val-description');
									$code->terms = $request->post('val-couponTerms');
									$code->min_amount = $request->post('val-amt');
									$code->start_date = date('Y-m-d', strtotime($request->post('val-startDate')));
									$code->end_date = date('Y-m-d', strtotime($request->post('val-endDate')));
									$code->amount_type = $request->post('val-amounttype');
									$code->discount_type = $request->post('val-type');
									$code->no_of_rides = ($code->discount_type == 'rides') ? $request->post('val-rides') : 0;
									$code->min_rides = ($code->discount_type == 'usage') ? $request->post('val-minRides') : 1;
									$code->discount_value = $request->post('val-discount');
									$code->no_of_applies = $request->post('val-applies');
									$code->status = ($request->post('val-status') == 'on') ? 'AC' : 'IN';
									$code->city = $value;
									$code->state = $state;
									$code->country = $country;
									$code->save();
								} else {
									$failCount++;
									$failCity[] = $value;
								}
								$iterateCount++;
							}

							if ($failCount > 0 && count($failCity) > 0) {
								$request->session()->flash('failCouponCity', implode(',', $failCity));
							}

							if (count($failCity) != count($city)) {
								$request->session()->flash('success', 'Coupon Code updated successfully for selected State/City');
							}

							return redirect()->route('couponCodes');
						} catch (Exception $e) {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('couponCodes');
						}
					} else {
						$request->session()->flash('error', 'Coupon Code not stored. Please select state before updating/adding any code.');
						return redirect()->route('couponCodes');
					}

				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('couponCodes');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('couponCodes');
		}

	}

	/**
	 * update status of coupon code (active/inactive)
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateStatus(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			//update vehicle status
			$code = CouponCode::find($id);

			if (isset($code->id)) {
				$status = ($code->status == 'AC') ? 'IN' : 'AC';
				$code->status = $status;

				if ($code->save()) {
					if ($code->status == 'AC') {
						$request->session()->flash('success', 'Code activated successfully.');
					} else {
						$request->session()->flash('success', 'Code deactivated successfully.');
					}

					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update Code. Please try again later.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}

	}

	/**
	 * update status of specified resource to 'DL' from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		$id = $request->post('val-id');
		if (isset($id) && $id != null) {
			$code = CouponCode::find($id);
			if (isset($code->id)) {
				$code->status = 'DL';

				if ($code->save()) {
					$request->session()->flash('success', 'Code deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Some error occurred while deleting the Code.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}
	}
}
