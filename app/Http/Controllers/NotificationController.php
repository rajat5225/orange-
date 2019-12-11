<?php

namespace App\Http\Controllers;

use App\Library\Firebase;
use App\Model\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller {
	public $rating;
	public $city;
	public $state;

	public function __construct() {
		$this->notification = new Notification;
		$this->city = Session::get('globalCity');
		$this->state = Session::get('globalState');
	}

	public function index() {
		$query = Notification::where('status', '!=', 'DL');
		if ($this->city != "all") {
			$query->where('city', $this->city);
		}
		if ($this->state != "all") {
			$query->where('state', $this->state);
		}
		$notifications = $query->get();
		return view('notifications.index', compact('notifications'));
	}

	public function create(Request $request) {
		$user_id = 0;
		(isset($request->user_id)) ? $user_id = implode(',', $request->user_id) : 0;
		return view('notifications.create', compact('user_id'));
	}

	public function store(Request $request) {
		$notification = new Notification();
		$notification->title = $request->title;
		$notification->description = $request->description;
		$notification->user_type = $request->user_type;
		$notification->user_id = $request->user_id;
		if ($this->city != "all") {
			$notification->city = $this->city;
		}
		if ($this->state != "all") {
			$notification->state = $this->state;
		}
		if ($notification->save()) {
			Firebase::sendCustomNotifications($notification, $request);
			$request->session()->flash('success', 'Notifications Sent');
			return redirect("admin/notifications");
		} else {
			$request->session()->flash('danger', 'Something goes wrong');
			return redirect("admin/notifications");
		}
	}
}
