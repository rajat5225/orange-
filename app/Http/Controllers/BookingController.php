<?php

namespace App\Http\Controllers;

use App\Library\SendMail;
use App\Model\Booking;
use App\Model\BookingSupport;
use App\Model\City;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller {
	public $ride;
	public $rideSupport;
	public $city;
	public $state;
	public $country;

	public function __construct() {
		$this->ride = new Booking;
		$this->rideSupport = new BookingSupport;
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
		$rides = $this->ride->fetchRides($this->city, $this->state, $this->country, $request);
		return view('bookings.index', compact('rides'));
	}

	/**
	 * Display a listing of the ride supports.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function indexRequest() {
		$supports = $this->rideSupport->fetchSupports($this->city, $this->state, $this->country);
		// dd($supports);
		return view('booking_supports.index', compact('supports'));
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
			$ride = Booking::with(['driver', 'vehicle', 'coupon_code', 'user'])->find($id);
			// dd($ride);

			if (isset($ride->id)) {
				$city = City::where('name', $ride->user->city)->first();
				return view('bookings.view', compact('ride', 'city'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('rides');
			}

		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('rides');
		}
	}

	/**
	 * Reply to the support request of the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function replyRequest(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$support = BookingSupport::where('id', $id)->first();

			if (isset($support->id)) {
				$validate = Validator($request->all(), [
					'content' => 'required',
				]);

				if ($validate->fails()) {
					echo json_encode(["status" => 0, 'message' => 'Reply message cannot be empty.']);
				} else {
					$support->reply = $request->post('content');

					// dd(in_array($request->status, ['IN', 'AC', 'DL']));
					if (isset($request->status) && in_array($request->status, ['IN', 'AC', 'DL'])) {
						$support->status = $request->status;
					}

					if ($support->save()) {
						$subject = 'Booking Support Reply';
						$data = array(
							'bookingID' => $support->booking_id,
							'user' => $support->booking->user->name,
							'driver' => $support->booking->driver->name,
							'date' => date('Y M, d', strtotime($support->booking->created_at)),
							'msg' => $support->reply,
							'email' => $support->booking->user->email,
						);
						$mail = SendMail::sendmail($subject, $data);
						if ($mail) {
							echo json_encode(["status" => 1, 'message' => 'Reply sent successfully.']);
						} else {
							echo json_encode(["status" => 0, 'message' => 'Something wrong has occurred. Unable to send mail.']);
						}

					} else {
						echo json_encode(["status" => 0, 'message' => 'Some error occurred while updating the support request']);
					}

				}
			} else {
				echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
			}

		} else {
			echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
		}
	}

	/**
	 * update status of support request (active/inactive)
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function updateStatusRequest(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			//update support request status
			$support = BookingSupport::find($id);

			if (isset($support->id)) {
				$status = ($support->status == 'AC') ? 'IN' : 'AC';
				$support->status = $status;

				if ($support->save()) {
					if ($support->status == 'AC') {
						$request->session()->flash('success', 'Support activated successfully.');
					} else {
						$request->session()->flash('success', 'Support deactivated successfully.');
					}

					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Unable to update support. Please try again later.');
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
	 * update status of support request to 'DL' from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function deleteStatusRequest(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			$support = BookingSupport::find($id);
			if (isset($support->id)) {
				$support->status = 'DL';

				if ($support->save()) {
					$request->session()->flash('success', 'Support deleted successfully.');
					return redirect()->back();
				} else {
					$request->session()->flash('error', 'Some error occurred while deleting the support.');
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
	 * Display the support request.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function showRequest(Request $request, $id = null) {
		if (isset($id) && $id != null) {
			if (strpos($request->path(), 'edit') !== false) {
				$type = 'edit';
			} else {
				$type = 'view';
			}

			$support = BookingSupport::where('id', $id)->first();
			if (isset($support->id)) {
				return view('booking_supports.view', compact('support', 'type'));
			} else {
				$request->session()->flash('error', 'Invalid Data');
				return redirect()->route('rideSupportRequests');
			}
		} else {
			$request->session()->flash('error', 'Invalid Data');
			return redirect()->route('rideSupportRequests');
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
