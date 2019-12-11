<?php

namespace App\Http\Controllers;

use App\Model\Cab;
use Illuminate\Http\Request;

class CabController extends Controller {
	public $cab;

	public function __construct() {

		$this->cab = new Cab;

	}
	public function index() {
		//get vehicle types for the selected state/city
		$cabs = Cab::where('status', "!=", 'DL')->get();
		// dD($vehicles);
		return view('cabs.index', compact('cabs'));
	}
	public function create() {
		$type = 'add';
		$url = route('addCab');
		$vehicle = new Cab;

		return view('cabs.create', compact('type', 'url', 'vehicle'));
	}
	public function store(Request $request) {

		$validate = Validator($request->all(), [
			'val-name' => 'required',
			'val-image' => 'required|mimes:jpeg,png,jpg,gif,svg',
			'val-arialimage' => 'required|mimes:jpeg,png,jpg,gif,svg',

		]);

		$attr = [
			'val-name' => 'Cab Name',
			'val-image' => 'Cab Image',
			'val-arialimage' => 'Cab Arial Image',

		];

		$validate->setAttributeNames($attr);
		if ($validate->fails()) {
			return redirect()->route('createCab')->withInput($request->all())->withErrors($validate);

		} else {

			try {
				$image = $request->file('val-image');
				$imageName = time() . $image->getClientOriginalName();
				$image->move(public_path('uploads/vehicles'), $imageName);
				$image2 = $request->file('val-arialimage');
				$imageName2 = time() . $image->getClientOriginalName();
				$image2->move(public_path('uploads/vehicles/aerial'), $imageName2);
				$vehicle = new Cab;
				$vehicle->cab_type = $request->post('val-name');
				$vehicle->status = ($request->post('val-status') == 'on') ? 'AC' : 'IN';
				$vehicle->image = $imageName;
				$vehicle->aerial_image = $imageName;
				$vehicle->save();
				$request->session()->flash('success', 'Cab added successfully for selected State/City');
				return redirect()->route('cabs');
			} catch (Exception $e) {
				$request->session()->flash('error', 'Something went wrong. Please try again later.');
				return redirect()->route('cabs');
			}

		}
	}
	public function show(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$vehicle = Cab::where('id', $id)->first();
			if (isset($vehicle->id)) {
				return view('cabs.view', compact('vehicle'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cabs');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cabs');
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
			$vehicle = Cab::where('id', $id)->first();

			if (isset($vehicle->id)) {

				$type = 'edit';
				$url = route('updateCab') . '/' . $id;
				return view('cabs.create', compact('vehicle', 'type', 'url'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('cabs');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('cabs');
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

			$vehicle = Cab::where('id', $id)->first();
			if (isset($vehicle->id)) {
				$rule = '';
				$rule2 = '';
				if ($request->post('image_exists') == 1) {
					$rule = 'sometimes|';
				}
				if ($request->post('aimage_exists') == 1) {
					$rule2 = 'sometimes|';
				}

				$validate = Validator($request->all(), [
					'val-name' => 'required',
					'val-image' => $rule . 'required|mimes:jpeg,png,jpg,gif,svg',
					'val-arialimage' => $rule2 . 'required|mimes:jpeg,png,jpg,gif,svg',
				]);

				$attr = [
					'val-name' => 'Cab Name',
					'val-image' => 'Cab Image',
					'val-arialimage' => 'Cab Arial Image',
				];

				$validate->setAttributeNames($attr);

				if ($validate->fails()) {
					return redirect()->route('editCab', ['id' => $id])->withInput($request->all())->withErrors($validate);
				} else {

					try {
						if ($request->post('image_exists') == 0) {
							//upload vehicle image in public/uploads/vehicles folder
							$imageName = time() . $request->file('val-image')->getClientOriginalName();
							$request->file('val-image')->move(public_path('uploads/vehicles'), $imageName);
						} else {
							$imageName = $vehicle->image;
						}
						if ($request->post('aimage_exists') == 0) {
							//upload vehicle image in public/uploads/vehicles folder
							$imageName1 = time() . $request->file('val-arialimage')->getClientOriginalName();
							$request->file('val-arialimage')->move(public_path('uploads/vehicles/aerial'), $imageName1);
						} else {
							$imageName1 = $vehicle->aerial_image;
						}

						$cabtype = $vehicle->cab_type;
						$vehicle->cab_type = $request->post('val-name');
						$vehicle->status = ($request->post('val-status') == 'on') ? 'AC' : 'IN';
						$vehicle->image = $imageName;
						$vehicle->aerial_image = $imageName1;
						$vehicle->save();

						$allvehicles = VehicleType::where('vehicle_type', $cabtype)->get();
						foreach ($allvehicles as $key => $value) {
							$cab = VehicleType::find($value->id);
							$cab->vehicle_type = $vehicle->cab_type;
							$cab->image = $imageName;
							$cab->aerial_image = $imageName1;
							$cab->save();
						}

						$request->session()->flash('success', 'Cab updated successfully for selected State/City');

						return redirect()->route('cabs');
					} catch (Exception $e) {
						$request->session()->flash('error', 'Something went wrong. Please try again later.');
						return redirect()->route('cabs');
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
	 * update status of specified resource to 'DL' from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$vehicle = Cab::find($id);
			if (isset($vehicle->id)) {
				$vehicle->status = 'DL';

				if ($vehicle->save()) {
					$request->session()->flash('success', 'Cab deleted successfully.');
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
	public function updateStatus(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			//update vehicle status
			$vehicle = Cab::find($id);

			if (isset($vehicle->id)) {
				$status = ($vehicle->status == 'AC') ? 'IN' : 'AC';
				$vehicle->status = $status;

				if ($vehicle->save()) {
					if ($vehicle->status == 'AC') {
						$request->session()->flash('success', 'Cab activated successfully.');
					} else {
						$request->session()->flash('success', 'Cab deactivated successfully.');
					}

					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update Cab. Please try again later.');
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
