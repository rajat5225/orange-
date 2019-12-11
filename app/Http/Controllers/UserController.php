<?php

namespace App\Http\Controllers;
use App\Library\SendMail;
use App\Model\BlockReason;
use App\Model\Booking;
use App\Model\City;
use App\Model\DocumentType;
use App\Model\Rating;
use App\Model\State;
use App\Model\User;
use App\Model\UserDocument;
use App\Model\VehicleType;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller {
	public $user;
	public $city;
	public $state;
	public $country;

	public function __construct() {
		$this->user = new User;
		$this->vehicle = new VehicleType;
		$this->city = Session::get('globalCity');
		$this->state = Session::get('globalState');
		$this->country = Session::get('globalCountry');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if (strpos($request->path(), 'user') !== false) {
			$usertype = 'user';
		} else {
			$usertype = 'driver';
		}

		$users = $this->user->fetchUsers($this->city, $this->state, $this->country, $usertype, $request);
		return view('users.index', compact('users', 'usertype'));
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
		if (strpos($request->path(), 'user') !== false) {
			$usertype = 'user';
		} else {
			$usertype = 'driver';
		}
		$cities = '';
		if (Session::get('globalState') != "all") {
			$cities = State::getCities(Session::get('globalState'));

		}
		if (isset($id) && $id != null) {
			$user = User::with(['vehicle_type', 'user_doc'])->find($id);

			if (isset($user->id)) {

				if ($user->user_role[0]->role != $usertype) {
					return redirect()->route('view' . ucfirst($user->user_role[0]->role), ['id' => $id]);
				}

				$city = City::where('name', $user->city)->first();
				$vehicles = $this->vehicle->fetchVehiclesTemp($city->name, $this->state);

				$alldoctypes = DocumentType::where('status', 'AC')->get();
				//$alldoctypes['']='Select Document Type';
				$otherDocs = UserDocument::whereHas('docType', function ($query) {
					$query->where('document_type', 'Other');
				})->where('user_id', $id)->where('status', '!=', 'DL')->get();

				$blockReason = BlockReason::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(1)->first();
				return view('users.view', compact('user', 'city', 'blockReason', 'otherDocs', 'alldoctypes', 'cities', 'vehicles'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route($usertype . 's');
			}

		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route($usertype . 's');
		}
	}
	public function info(Request $request, $id = null) {
		if (strpos($request->path(), 'user') !== false) {
			$usertype = 'user';
		} else {
			$usertype = 'driver';
		}
		$cities = '';
		if (Session::get('globalState') != "all") {
			$cities = State::getCities(Session::get('globalState'));

		}
		if (isset($id) && $id != null) {
			$user = User::with(['vehicle_type', 'user_doc'])->find($id);

			if (isset($user->id)) {

				if ($user->user_role[0]->role != $usertype) {
					return redirect()->route('view' . ucfirst($user->user_role[0]->role), ['id' => $id]);
				}

				$driver_booking = Booking::select(DB::raw('COUNT(bookings.id) as total_rides'), DB::raw('SUM(bookings.total) as total_earning'))->
					where("driver_id", $id)->where("bookings.status", "AC")->first();
				$total_complements = Rating::select(DB::raw('COUNT(ratings.id) as total_complements'), DB::raw('round(AVG(ratings.rating)) as total_ratings'))->
					where("parent_id", $id)->where("ratings.status", "AC")->first();
				$recent_complements = Rating::select(DB::raw('count(ratings.id) as total'), 'complements.name', 'bookings.booking_code', DB::raw("CONCAT('" . url("uploads/complements") . "/', complements.image) image"))->
					join('bookings', 'bookings.id', '=', 'ratings.booking_id')->
					join('complements', 'ratings.complement_id', '=', 'complements.id')->
					where("ratings.parent_id", $id)
					->where("ratings.complement_id", '!=', NULL)->where("ratings.status", "AC")->orderBy('ratings.id', 'desc')->groupBy('ratings.complement_id')->get();
				$earning = Booking::select(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m-%d") as date'), DB::raw("(select sum(total) from bookings where payment_mode ='cash' and driver_id='$id') as cash_total"), DB::raw("COALESCE((select sum(total) from bookings where payment_mode ='wallet' and driver_id='$id'),0) as wallet_total"), DB::raw('SUM(bookings.total) as total_earning'), DB::raw('ROUND(((SUM(bookings.total)*(100-vehicle_types.driver_charge))/100),2) as admin_total'))
					->join('vehicle_types', 'vehicle_types.id', '=', 'bookings.vehicle_type_id')->
					where("bookings.driver_id", $id)->where("bookings.status", "AC")->groupBy(DB::raw('DATE_FORMAT(bookings.created_at, "%Y-%m")'))->orderBy(DB::raw('YEAR(bookings.created_at)', 'DESC'))->orderBy(DB::raw('MONTH(bookings.created_at)'), 'DESC')->orderBy(DB::raw('DATE(bookings.created_at)'), 'DESC')->get();
				$all_compliments = Rating::select('complements.name', 'bookings.booking_code', DB::raw('DATE_FORMAT(ratings.created_at, "%Y-%m-%d") as date'), DB::raw("CONCAT('" . url("uploads/complements") . "/', complements.image) image"))->
					join('bookings', 'bookings.id', '=', 'ratings.booking_id')->
					join('complements', 'ratings.complement_id', '=', 'complements.id')->
					where("ratings.parent_id", $id)
					->where("ratings.complement_id", '!=', NULL)->where("ratings.status", "AC")->orderBy('ratings.id', 'desc')->get();
				$bookings = Booking::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'), DB::raw('COUNT(bookings.id) as total_rides'), DB::raw("(select count(id) from bookings where payment_mode ='cash' and driver_id='$id') as cash_count"), DB::raw("(select count(id) from bookings where payment_mode ='wallet' and driver_id='$id') as wallet_count"))->
					where("driver_id", $id)->where("bookings.status", "AC")->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))->orderBy(DB::raw('YEAR(created_at)', 'DESC'))->orderBy(DB::raw('MONTH(created_at)'), 'DESC')->orderBy(DB::raw('DATE(created_at)'), 'DESC')->get();

				$city = City::where('name', $user->city)->first();
				$vehicles = $this->vehicle->fetchVehiclesTemp($city->name, $this->state);

				$alldoctypes = DocumentType::where('status', 'AC')->get();
				//$alldoctypes['']='Select Document Type';
				$otherDocs = UserDocument::whereHas('docType', function ($query) {
					$query->where('document_type', 'Other');
				})->where('user_id', $id)->where('status', '!=', 'DL')->get();

				$blockReason = BlockReason::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(1)->first();
				return view('users.info', compact('user', 'city', 'blockReason', 'otherDocs', 'alldoctypes', 'cities', 'vehicles', 'driver_booking', 'total_complements', 'recent_complements', 'all_compliments', 'earning', 'bookings'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route($usertype . 's');
			}

		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route($usertype . 's');
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
	public function update(Request $request) {
		//block/unblock user
		if (strpos($request->path(), 'user') !== false) {
			$usertype = 'user';
		} else {
			$usertype = 'driver';
		}

		$user = User::find($request->post('val-id'));
		$userStatus = $user->status;
		if (isset($user->id)) {
			$user->status = $request->post('val-status');
			if ($user->save()) {
				$blockData = array(
					'user_id' => $request->post('val-id'),
					'block_status' => $request->post('val-status'),
					'reason' => $request->post('val-reason'),
				);
				$insert = BlockReason::insert($blockData);
				if ($userStatus != "IN") {
					if ($request->post('val-status') != 'on') {
						SendMail::sendBlockMail("Block - NXG Charge", $user, $blockData, null);
					}
				}
				if ($insert) {
					if ($user->status == 'AC') {
						$request->session()->flash('success', ucfirst($user->user_role[0]->role) . ' unblocked successfully.');
					} else {
						$request->session()->flash('success', ucfirst($user->user_role[0]->role) . ' blocked successfully.');
					}

					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update ' . $user->user_role[0]->role . ' status. Please try again later.');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Unable to update ' . $user->user_role[0]->role . ' status. Please try again later.');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route($usertype . 's');
		}

	}

	public function updateStatus(Request $request, $id = null) {
		if (strpos($request->path(), 'user') !== false) {
			$usertype = 'user';
		} else {
			$usertype = 'driver';
		}

		if (isset($id) && $id != null) {
			//update user status(verified, blocked)
			$user = User::find($id);
			$userStatus = $user->status;
			$documenVerification = $user->document_verification;

			if (isset($user->id)) {
				$user->status = ($request->post('val-block') == 'on') ? 'IN' : 'AC';
				// $user->is_verified = ($request->post('val-verify') == 'on') ? 1 : 0;

				if ($user->user_role[0]->role == 'driver') {
					$user->is_verified = ($request->post('val-verify') == 'on') ? 1 : 0;
					// $user->identity_verification = ($request->post('val-idverify') == 'on') ? 1 : 0;
					// $user->vehicle_verification = ($request->post('val-vhverify') == 'on') ? 1 : 0;
					$user->document_verification = ($request->post('val-verify') == 'on') ? 1 : 0;
					if ($documenVerification == 0) {
						if ($request->post('val-verify') == 'on') {
							SendMail::sendDocumentVerifyMail("Document Verified - NXG Charge", $user, null);
						}
					}
				}
				$status = ($request->post('val-block') == 'on') ? 'IN' : 'AC';
				if ($user->save()) {
					$blockData = array(
						'user_id' => $id,
						'block_status' => $status,
						'reason' => $request->post('val-msg'),
					);
					$insert = BlockReason::insert($blockData);
					if ($userStatus != "IN") {
						if ($request->post('val-block') == 'on') {
							SendMail::sendBlockMail("Block - NXG Charge", $user, $blockData, null);
						}
					}
					if ($insert) {
						$request->session()->flash('success', ucfirst($user->user_role[0]->role) . ' updated successfully.');
						return redirect()->back();
					} else {
						$request->session()->flash('error', 'Unable to update ' . $user->user_role[0]->role . '. Please try again later.');
						return redirect()->back();
					}
				} else {
					$request->session()->flash('error', 'Unable to update ' . $user->user_role[0]->role . '. Please try again later.');
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
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}

	/**
	 * Remove the other document of driver uploaded by admin
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function deleteDoc(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			//update user document status as DL
			$userdoc = UserDocument::find($id);

			if (isset($userdoc->id)) {
				$userdoc->status = 'DL';

				if ($userdoc->save()) {
					echo json_encode(["status" => 1, 'message' => 'Document deleted successfully.']);
				} else {
					echo json_encode(["status" => 0, 'message' => 'Unable to delete document. Please try again later.']);
				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
			}
		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}

	}

	/**
	 * add other document of driver by admin
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function addDoc(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			//add document to user account as Other type
			$user = User::find($id);
			// dd($request->all());
			if (isset($user->id)) {
				$validate = Validator($request->all(), [
					'doc.*' => 'required|mimes:jpeg,png,jpg,doc,docx,pdf',
					//'document_type_id'=>'required',
				], ['doc.*.mimes' => 'Please select images with valid extensions (.jpg, .jpeg, .png, .doc, .docx, .pdf)', 'doc.*.uploaded' => 'Please select images with valid extensions (.jpg, .jpeg, .png, .doc, .docx, .pdf)']);

				$attr = [
					'doc' => 'Document',
				];

				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					return redirect()->route('viewDriver', ['id' => $id])->withInput($request->all())->withErrors($validate);
				} else {
					$count = 0;
					foreach ($request->file('doc') as $value) {
						$imageName = time() . $value->getClientOriginalName();
						$value->move(public_path('uploads/documents/' . $id), $imageName);

						$userdoc = new UserDocument;
						$userdoc->user_id = $id;
						$userdoc->document_type_id = 6;
						$userdoc->document_name = $imageName;
						$userdoc->status = 'AC';

						if ($userdoc->save()) {
							$count++;
						}

					}
					if ($count == count($request->file('doc'))) {
						$request->session()->flash('success', 'Document(s) added successfully.');
						return redirect()->back();
					} else {
						if ($count > 0) {
							$request->session()->flash('error', 'Some of the document(s) were not uploaded. Please verify.');
							return redirect()->back();
						} else {
							$request->session()->flash('error', 'None of the document(s) were uploaded. Please verify.');
							return redirect()->back();
						}
					}
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
	public function uploadDoc(Request $request, $id = null) {

		if (isset($id) && $id != null) {
			//add document to user account as Other type
			if (count($request->all()) > 1) {
				$user = User::find($id);
				if (isset($user->id)) {
					foreach ($request->all() as $key => $item) {
						$number = str_replace("alldoc", "", $key);
						if (is_numeric($number)) {
							if ($item) {
								$imageName = time() . $item->getClientOriginalName();
								$item->move(public_path('uploads/documents/' . $id), $imageName);
								UserDocument::where(["user_id" => $id, "document_type_id" => $number])->update(["status" => "DL"]);
								$UserDocument = new UserDocument();
								$UserDocument->user_id = $id;
								$UserDocument->document_type_id = $number;
								$UserDocument->document_name = $imageName;
								$UserDocument->save();
							} else {
								$request->session()->flash('error', 'Please Select Document');
								return redirect()->back();
							}
						}
					}
					$request->session()->flash('success', 'Document(s) added successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Invalid Data');
					return redirect()->back();
				}
			} else {
				$request->session()->flash('error', 'Please Select Document');
				return redirect()->back();
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->back();
		}

	}
	public function addVehicleType(Request $request, $id = null) {

		if (isset($id) && $id != null) {

			$user = User::find($id);
			if (isset($user->id)) {
				$validate = Validator($request->all(), [
					'vehicle_type' => 'required',
					'val-state' => 'required',
					'city' => 'required',
					//'document_type_id'=>'required',
				]);

				$attr = [
					'doc' => 'Vehicle Type',
				];

				$validate->setAttributeNames($attr);
				if ($validate->fails()) {
					return redirect()->route('viewDriver', ['id' => $id])->withInput($request->all())->withErrors($validate);
				} else {
					$user->vehicle_type_id = $request->post('vehicle_type');
					$user->city = $request->post('city');
					$user->state = $request->post('val-state');
					$user->save();
					$request->session()->flash('success', 'Vehicle added successfully.');
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
	public function getVehicleType(Request $request) {
		$vehicles = $this->vehicle->fetchVehiclesTemp($request->city, $request->state);
		echo json_encode($vehicles);
	}

}
