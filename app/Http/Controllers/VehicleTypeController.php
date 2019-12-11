<?php

namespace App\Http\Controllers;

use App\Model\Cab;
use App\Model\City;
use App\Model\Country;
use App\Model\State;
use App\Model\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class VehicleTypeController extends Controller {
	public $vehicle;
	public $city;
	public $state;
	public $country;
	public $columns;

	public function __construct() {
		$this->vehicle = new VehicleType;
		$this->city = Session::get('globalCity');
		$this->state = Session::get('globalState');
		$this->country = Session::get('globalCountry');
		$this->columns = [
			"vehicle_type", "price", "driver_charge", "waiting_charge", "capacity", "city", "state", "country", "status", "action",
		];
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//get vehicle types for the selected state/city
		// $vehicles = $this->vehicle->fetchVehicles($this->city, $this->state, $this->country);
		//$cabs = Cab::where('status','AC')->get();
		//$cabs[''] ='Select Vehicle';
		// dD($vehicles);
		return view('vehicle_types.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function vehiclesAjax(Request $request) {
		$request->search = $request->search['value'];
		if (isset($request->order[0]['column'])) {
			$request->order_column = $request->order[0]['column'];
			$request->order_dir = $request->order[0]['dir'];
		}
		$records = $this->vehicle->fetchVehicles($this->city, $this->state, $this->country);
		$count = $records->get();
		if (isset($request->start)) {
			$vehicles = $records->offset($request->start)->limit($request->length)->get();
		} else {
			$vehicles = $records->offset($request->start)->limit(count($count))->get();
		}
		$total = count($vehicles);
		// die();
		foreach ($vehicles as $vehicle) {
			$data = [];
			$data['vehicle_type'] = $vehicle->vehicle_type;
			$data['price'] = $vehicle->price;
			$data['driver_charge'] = $vehicle->driver_charge;
			$data['waiting_charge'] = $vehicle->waiting_charge;
			$data['capacity'] = $vehicle->capacity;
			$data['city'] = $vehicle->city;
			$data['state'] = $vehicle->state;
			$data['country'] = $vehicle->country;
			$data['status'] = ucfirst(config('constants.STATUS.' . $vehicle->status));

			$action = '';

			if ($vehicle->status == 'AC') {
				$action .= '<a href="' . route('statusVehicle', ['id' => $vehicle->id]) . '" class="toolTip" data-status="' . $vehicle->status . '" data-id="' . $vehicle->id . '" data-toggle="tooltip" data-placement="bottom" title="Deactivate"><i class="fa fa-lock" aria-hidden="true"></i></a>';
			} else {
				$action .= '<a href="' . route('statusVehicle', ['id' => $vehicle->id]) . '" class="toolTip" data-status="' . $vehicle->status . '" data-id="' . $vehicle->id . '" data-toggle="tooltip" data-placement="bottom" title="Activate"><i class="fa fa-unlock" aria-hidden="true"></i></a>';
			}
			$action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editVehicle', ['id' => $vehicle->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>
												&nbsp;&nbsp;&nbsp;<a href="' . route('viewVehicle', ['id' => $vehicle->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>
                                                &nbsp;&nbsp;&nbsp;<a href="' . route('deleteVehicle', ['id' => $vehicle->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
			$data['action'] = $action;

			$result[] = $data;
		}
		$data = json_encode([
			'data' => $result,
			'recordsTotal' => count($count),
			'recordsFiltered' => count($count),
		]);
		echo $data;

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$type = 'add';
		$url = route('addVehicle');
		$vehicle = new VehicleType;
		$cabs = Cab::where('status', 'AC')->get();

		$cities = "";
		$states = "";

		// die;
		if (Session::get('globalState') != "all") {
			$cities = State::getCities(Session::get('globalState'));

		}
		if (Session::get('globalCountry') != "all") {
			$states = Country::getStates(Session::get('globalCountry'));

		}
		return view('vehicle_types.create', compact('type', 'url', 'vehicle', 'cities', 'cabs', 'states'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	// |dimensions:max_width=100,max_height=100
	// , ['val-image.dimensions' => 'Vehicle Image should have maximum dimensions as 100x100']
	public function store(Request $request) {
		$validate = Validator($request->all(), [
			'cab_type' => 'required',
			//'val-image' => 'required|mimes:jpeg,png,jpg,gif,svg',
			'val-capacity' => 'required|digits_between:1,10|integer',
			'val-price' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			'val-driver' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			//'val-basefare' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			'val-waiting' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			// 'val-cancel' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
			'city.*' => 'required',
		]);

		$attr = [
			'cab_type' => 'Vehicle Type',
			//'val-image' => 'Vehicle Image',
			'val-capacity' => 'Capacity',
			'val-price' => 'Price',
			'val-driver' => 'Driver Charges',
			// 'val-distance' => 'Distance Time',
			'val-waiting' => 'Waiting Charges',
			// 'val-cancel' => 'Cancellation Charges',
		];

		$cab = Cab::find($request->post('cab_type'));

		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('addVehicle')->withInput($request->all())->withErrors($validate);
		} else {
			//check for globalized state and city and save vehicle for all cities(if more than one - if state is selected and city is all)
			// $state = Session::get('globalState');
			// $city = Session::get('globalCity');
			$state = $request->post('val-state');
			$city = $request->post('city');
			$country = $request->post('val-country');

			if ($state != 'all') {
				try {
					if ($city == 'all') {
						// $city = config('statecity.cities');
						$city = City::whereHas('state', function ($query) use ($state) {
							$query->where('name', $state);
						})->pluck('name');
					} else {
						$city = (array) $city;
						// $validate = VehicleType::where('city', $city[0])->where('vehicle_type', $request->post('val-name'))->where('status', '!=', 'DL')->count();
						// if ($validate > 0) {
						// 	$request->session()->flash('error', 'This vehicle was already added in the city:' . $city[0]);
						// 	return redirect()->route('vehicles');
						// }
					}

					$failCount = 0;
					$failCity = array();
					$image = $cab->image;
					//$imageName = time() . $image->getClientOriginalName();
					//$image->move(public_path('uploads/vehicles'), $imageName);

					foreach ($city as $value) {

						$validate = VehicleType::where('city', $value)->where('vehicle_type', $request->post('val-name'))->where('status', '!=', 'DL')->count();

						if ($validate <= 0) {
							//upload vehicle image in public/uploads/vehicles folder
							//$newImage = time() . $value . $image->getClientOriginalName();
							//copy(public_path('uploads/vehicles/') . $imageName, public_path('uploads/vehicles/') . $newImage);

							$vehicle = new VehicleType;
							$vehicle->vehicle_type = $cab->cab_type;
							$vehicle->capacity = $request->post('val-capacity');
							$vehicle->price = $request->post('val-price');
							$vehicle->driver_charge = $request->post('val-driver');
							// $vehicle->distance_time = $request->post('val-distance');
							$vehicle->waiting_charge = $request->post('val-waiting');
							$vehicle->base_fare = $request->post('val-basefare');
							// $vehicle->cancellation_charge = $request->post('val-cancel');
							$vehicle->status = ($request->post('val-status') == 'on') ? 'AC' : 'IN';
							$vehicle->aerial_image = $cab->aerial_image;
							$vehicle->image = $cab->image;
							$vehicle->city = $value;
							$vehicle->state = $state;
							$vehicle->country = $country;
							$vehicle->created_at = date('Y-m-d H:i:s');
							$vehicle->save();
						} else {
							$failCount++;
							$failCity[] = $value;
						}
					}

					if ($failCount > 0 && count($failCity) > 0) {
						$request->session()->flash('failCity', implode(',', $failCity));
					}

					if (count($failCity) != count($city)) {
						$request->session()->flash('success', 'Vehicle added successfully for selected State/City');
					}

					return redirect()->route('vehicles');
				} catch (Exception $e) {
					$request->session()->flash('error', 'Something went wrong. Please try again later.');
					return redirect()->route('vehicles');
				}
			} else {
				$request->session()->flash('error', 'Vehicle not stored. Please select state before adding any vehicle.');
				return redirect()->route('vehicles');
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
			$vehicle = VehicleType::where('id', $id)->first();
			if (isset($vehicle->id)) {
				return view('vehicle_types.view', compact('vehicle'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('vehicles');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('vehicles');
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
			$vehicle = VehicleType::where('id', $id)->first();
			$cabs = Cab::where('status', 'AC')->get();
			if (isset($vehicle->id)) {
				$vehicleState = City::where('name', $vehicle->city)->first()->state->name;
				$vehicleCountry = State::where('name', $vehicleState)->first()->country->name;
				$cities = State::getCities($vehicleState);
				$states = Country::getStates($vehicleCountry);
				$type = 'edit';
				$url = route('updateVehicle') . '/' . $id;
				return view('vehicle_types.create', compact('vehicle', 'type', 'url', 'vehicleState', 'vehicleCountry', 'cities', 'cabs', 'states'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('vehicles');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('vehicles');
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
			// dd($request->all());
			$vehicle = VehicleType::where('id', $id)->first();
			if (isset($vehicle->id)) {
				$rule = '';
				if ($request->post('image_exists') == 1) {
					$rule = 'sometimes|';
				}

				$validate = Validator($request->all(), [
					'cab_type' => 'required',
					//'val-image' => $rule . 'required|mimes:jpeg,png,jpg,gif,svg',
					'val-capacity' => 'required|digits_between:1,10|integer',
					'val-price' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
					'val-driver' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
					// 'val-distance' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
					'val-waiting' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
					// 'val-cancel' => 'required|max:10|regex:/^\d+(\.\d{1,2})?$/',
					'city' => ['required', Rule::unique('vehicle_types')->where(function ($query) use ($request, $vehicle) {
						return $query->where('city', $request->post('city'))
							->where('vehicle_type', $request->post('val-name'))
							->where('id', '!=', $vehicle->id)
							->where('status', '!=', 'DL');
					})],
				], ['city.unique' => $request->post('val-name') . ' has already been added in ' . $request->post('city')]);

				$attr = [
					'cab_type' => 'Vehicle Name',
					//'val-image' => 'Vehicle Image',
					'val-capacity' => 'Capacity',
					'val-price' => 'Price',
					'val-driver' => 'Driver Charges',
					// 'val-distance' => 'Distance Time',
					'val-waiting' => 'Waiting Charges',
					// 'val-cancel' => 'Cancellation Charges',
				];
				$cab = Cab::find($request->post('cab_type'));
				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editVehicle', ['id' => $id])->withInput($request->all())->withErrors($validate);
				} else {
					//check for globalized state and city and save vehicle for all cities(if more than one - if state is selected and city is all)
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

							// //upload image if new selected
							// if ($request->post('image_exists') == 0) {
							// 	//upload vehicle image in public/uploads/vehicles folder
							// 	$imageName = time() . $request->file('val-image')->getClientOriginalName();
							// 	$request->file('val-image')->move(public_path('uploads/vehicles'), $imageName);
							// } else {
							// 	$imageName = $vehicle->image;
							// }
							$iterateCount = 1;
							$failCount = 0;
							$failCity = array();
							foreach ($city as $value) {

								/*$validate = VehicleType::where('city', $value)->where('vehicle_type', $request->post('val-name'))->where('id', '!=', $vehicle->id)->where('status', '!=', 'DL')->count();

								if ($validate <= 0) {*/
								// if ($request->post('image_exists') == 0) {
								// 	$newImage = time() . $value . $request->file('val-image')->getClientOriginalName();
								// 	copy(public_path('uploads/vehicles/') . $imageName, public_path('uploads/vehicles/') . $newImage);
								// } else {
								// 	$newImage = time() . $value . $imageName;
								// 	copy(public_path('uploads/vehicles/') . $imageName, public_path('uploads/vehicles/') . $newImage);
								// }

								$validate = VehicleType::where('city', $value)->where('vehicle_type', $request->post('val-name'))->where('id', '!=', $vehicle->id)->where('status', '!=', 'DL')->count();

								if ($validate <= 0) {
									if ($iterateCount != 1 && count($city) > 1) {
										$vehicle = new VehicleType;
										$vehicle->created_at = date('Y-m-d H:i:s');

									}
								} else {
									$vehicle = VehicleType::where('city', $value)->where('vehicle_type', $request->post('val-name'))->where('status', '!=', 'DL')->first();
								}

								$vehicle->vehicle_type = $cab->cab_type;
								$vehicle->capacity = $request->post('val-capacity');
								$vehicle->price = $request->post('val-price');
								$vehicle->driver_charge = $request->post('val-driver');
								// $vehicle->distance_time = $request->post('val-distance');
								$vehicle->waiting_charge = $request->post('val-waiting');
								$vehicle->base_fare = $request->post('val-basefare');
								// $vehicle->cancellation_charge = $request->post('val-cancel');
								$vehicle->status = ($request->post('val-status') == 'on') ? 'AC' : 'IN';
								$vehicle->aerial_image = $cab->aerial_image;
								$vehicle->image = $cab->image;
								$vehicle->city = $value;
								$vehicle->state = $state;
								$vehicle->country = $country;
								$vehicle->save();
								/*} else {
									$failCount++;
									$failCity[] = $value;
								}*/
								$iterateCount++;
							}
							if ($failCount > 0 && count($failCity) > 0) {
								$request->session()->flash('failCity', implode(',', $failCity));
							}

							if (count($failCity) != count($city)) {
								$request->session()->flash('success', 'Vehicle updated successfully for selected State/City');
							}
							return redirect()->route('vehicles');
						} catch (Exception $e) {
							$request->session()->flash('error', 'Something went wrong. Please try again later.');
							return redirect()->route('vehicles');
						}
					} else {
						$request->session()->flash('error', 'Vehicle not stored. Please select state before updating/adding any vehicle.');
						return redirect()->route('vehicles');
					}
				}
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('vehicles');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('vehicles');
		}
	}

	/**
	 * update status of vehicle (active/inactive)
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateStatus(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			//update vehicle status
			$vehicle = VehicleType::find($id);

			if (isset($vehicle->id)) {
				$status = ($vehicle->status == 'AC') ? 'IN' : 'AC';
				$vehicle->status = $status;

				if ($vehicle->save()) {
					if ($vehicle->status == 'AC') {
						$request->session()->flash('success', 'Vehicle activated successfully.');
					} else {
						$request->session()->flash('success', 'Vehicle deactivated successfully.');
					}

					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update vehicle. Please try again later.');
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
	public function destroy(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$vehicle = VehicleType::find($id);
			if (isset($vehicle->id)) {
				$vehicle->status = 'DL';

				if ($vehicle->save()) {
					$request->session()->flash('success', 'Vehicle deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Some error occurred while deleting the vehicle.');
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
	 * delete image of specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function imageDelete(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$vehicle = VehicleType::find($id);
			if (isset($vehicle->id)) {
				unlink(public_path('/uploads/vehicles/' . $vehicle->image));
				$vehicle->image = null;
				$vehicle->gray_image = null;

				if ($vehicle->save()) {
					echo json_encode(["status" => 1, 'message' => 'Image deleted successfully.']);
				} else {
					echo json_encode(["status" => 0, 'message' => 'Some error occurred while deleting the image.']);
				}

			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
			}

		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}

	}

	/**
	 * add vehicles to all specified state and city
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function addCabs(Request $request) {

		$city = City::whereHas('state', function ($q) {
			$q->whereHas('country', function ($qu) {
				$qu->where('name', '!=', 'India');
			});
		})->get();
		echo 'City count: ' . count($city) . "\n" . "<br>";

		$cabs = Cab::where('status', 'AC')->get();
		$exists = 0;

		foreach ($city as $value) {
			foreach ($cabs as $cab) {
				$validate = VehicleType::where('city', $value->name)->where('state', $value->state->name)->where('country', $value->state->country->name)->where('vehicle_type', $cab->cab_type)->where('status', '!=', 'DL')->count();

				if ($validate <= 0) {
					//upload vehicle image in public/uploads/vehicles folder
					//$newImage = time() . $value . $image->getClientOriginalName();
					//copy(public_path('uploads/vehicles/') . $imageName, public_path('uploads/vehicles/') . $newImage);

					$vehicle = new VehicleType;
					$vehicle->vehicle_type = $cab->cab_type;
					$vehicle->capacity = 5;
					$vehicle->price = 20;
					$vehicle->driver_charge = 10;
					// $vehicle->distance_time = $request->post('val-distance');
					$vehicle->waiting_charge = 2;
					$vehicle->base_fare = 25;
					// $vehicle->cancellation_charge = $request->post('val-cancel');
					$vehicle->status = 'AC';
					$vehicle->aerial_image = $cab->aerial_image;
					$vehicle->image = $cab->image;
					$vehicle->city = $value->name;
					$vehicle->state = $value->state->name;
					$vehicle->country = $value->state->country->name;
					$vehicle->created_at = date('Y-m-d H:i:s');
					$vehicle->save();
				} else {
					echo 'Vehicle Exists in city: ' . $value->name . ' - ' . $cab->cab_type . "\n" . "<br>";
					$exists++;
				}
			}

		}

		echo 'Exists Total: ' . $exists . "\n";

	}
}
