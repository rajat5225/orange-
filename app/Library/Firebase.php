<?php
namespace App\Library;
use App\Model\BusRuleRef;
use App\Model\UserView;
use DB;

class Firebase {

	public static function sendCustomNotifications($data, $request) {
		// dd($request);
		DB::enableQueryLog();
		// $user = User::select(DB::raw("GROUP_CONCAT(device_token) as device_token"),"device_type")->where("status", "!=", "IN")->where("is_verified", 1)->where("device_token", "!=", "");
		// $user = UserView::where("status", "!=", "IN")->where("is_verified", 1)->where("device_token", "!=", "")->where("wallet_amount", "=", "6.00");
		$user = UserView::where("status", "!=", "IN")->where("is_verified", 1)->where("device_token", "!=", "");
		if (isset($data->state)) {
			if ($data->state != "all") {
				$user->where("state", $data->state);
			}

		}
		if (isset($data->city)) {
			if ($data->city != "all") {
				$user->where("city", $data->city);
			}

		}
		if (isset($request->user_type)) {
			if ($request->user_type != "all" && $request->user_type != "single") {
				$user->where("role", $request->user_type);
			}

		}
		if (isset($request->user_id)) {
			if ($request->user_id != 0) {
				$user->whereIn("user_id", explode(",", $request->user_id));
			}

		}
		// if (isset($request->gender)) {
		// 	$user->where("user.gender", $request->gender);
		// }
		// $user = $user->groupBy("device_type")->take(5)->get();
		$user = $user->get();
		// echo "<pre>";
		// print_r(DB::getQueryLog());
		// print_r($user->toArray());
		// die;
		foreach ($user as $item) {
			// echo $item->device_type;
			// die;
			// try {
			// $NotificationUser = new NotificationUser();
			// $NotificationUser->user_id = $item->id;
			// $NotificationUser->notification_id = $data->id;
			// $NotificationUser->save();
			$fcmData = array(
				"notification_id" => $data->id,
				'message' => $data->title,
				'body' => $data->description,
			);
			// echo $item->device_token;
			// echo "----";
			// print_r(array("msg" => $data->title, "notification_id" => $data->id, "notification_type" => "Custom", "device_type" => $item->device_type, "fcmData" => $fcmData, "title" => $data->title));
			// die;
			// $result = Firebase::sendBulkFirebaseNotification($item, array("msg" => $data->title, "notification_id" => $data->id, "notification_type" => "Custom", "device_type" => $item->device_type, "fcmData" => $fcmData, "title" => $data->title, "description" => $data->description));
			$result = Firebase::sendSingleNotification($item->device_token, array("msg" => $data->title, "notification_id" => $data->id, "notification_type" => "Custom", "device_type" => $item->device_type, "fcmData" => $fcmData, "title" => $data->title, "description" => $data->description));
			// } catch (\Exception $ex) {
			// 	// print_r($ex);
			// }
		}
		// die;
	}

	public static function getBusRuleRef($rule_name) {
		if ($BusRuleRef = BusRuleRef::where("rule_name", $rule_name)->first()) {
			return $BusRuleRef->rule_value;
		}
	}

	public static function sendSingleNotification($deviceid, $msgarray) {
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fcmApiKey = Firebase::getBusRuleRef("fcm_api_key");
		$singleID = $deviceid;
		$device_type = $msgarray['device_type'];

		$fcmMsg = array(
			'message' => $msgarray['description'],
			'title' => $msgarray['title'],
			'text' => $msgarray['description'],
			'vibrate' => 1,
			'type' => $msgarray['notification_type'],
			"date_time" => date("Y-m-d H:i:s"),
		);

		if ($device_type == "ios") {
			$fcmFields = array(
				'to' => $singleID,
				'priority' => 'high',
				'notification' => $fcmMsg,
				'data' => $msgarray['fcmData'],
			);
		} else {
			$fcmFields = array(
				'to' => $singleID,
				'priority' => 'high',
				'data' => array_merge($fcmMsg, array('type' => $msgarray['notification_type'])),
			);
		}

		$headers = array(
			'Authorization: key=' . $fcmApiKey,
			'Content-Type: application/json',
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
		$result = curl_exec($ch);
		if ($result === FALSE) {
			// die('Curl failed: ' . curl_error($ch));
		}
		// echo "<br><pre>";
		// echo $singleID;
		// print_r($fcmMsg);
		// print_r($result);
		// echo "</pre>";
		// echo "---------------------------<br><br><br><br>";
		curl_close($ch);
		// die;
		return $result;
	}

	public static function sendBulkFirebaseNotification($user, $msgarray) {
		// echo json_encode(explode(",", $user->device_token));
		// die;
		// echo "-1-";
		// dd($msgarray);
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fcmApiKey = Firebase::getBusRuleRef("fcm_api_key");
		$fcmMsg = array(
			'message' => $msgarray['fcmData'],
			'title' => $msgarray['title'],
			'text' => $msgarray['description'],
			'vibrate' => 1,
			'type' => $msgarray['notification_type'],
			"date_time" => date("Y-m-d H:i:s"),
		);
		if ($user->device_type == "ios") {
			$fcmFields = array(
				'to' => json_encode(explode(",", $user->device_token)),
				'priority' => 'high',
				'notification' => $fcmMsg,
				'data' => $msgarray['fcmData'],
			);
		} else {
			$fcmFields = array(
				'to' => json_encode(explode(",", $user->device_token)),
				'priority' => 'high',
				'data' => array_merge($fcmMsg, array('type' => $msgarray['notification_type'])),
			);
		}
		$headers = array(
			'Authorization: key=' . $fcmApiKey,
			'Content-Type: application/json',
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
		$result = curl_exec($ch);
		curl_close($ch);
		// echo "<pre>";
		// print_r($fcmMsg);
		// print_r($result);
		// echo "</pre>";
		// echo "---------------------------<br><br>";
		return $result;
	}

}