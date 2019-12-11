<?php
namespace App\Library;

use App\Model\BusRuleRef;
use PHPMailer\PHPMailer\PHPMailer;

class SendMail {

	public static function sendmail($subject, $data, $file = null) {
		$mailData = $data;
		$mail = new PHPMailer(true);
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = 'mail.vlcare.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@vlcare.com';
		$mail->Password = 'noreply@123';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->CharSet = "UTF-8";

		$mail->setFrom('noreply@vlcare.com', 'NXG Charge');
		$mail->addAddress($data['email'], $data['user']);
		// $mail->addReplyTo($replyto, $replyto);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$text = view('emails.reply_request', compact('mailData'));

		$mail->MsgHTML($text);

		return $mail->send();
	}

	public static function sendBlockMail($subject, $data, $blockReason, $file = null) {
		// dd($blockReason);
		$mailData = $data;
		$mail = new PHPMailer(true);
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = 'mail.vlcare.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@vlcare.com';
		$mail->Password = 'noreply@123';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->CharSet = "UTF-8";

		$mail->setFrom('noreply@vlcare.com', 'NXG Charge');
		$mail->addAddress($data['email'], $data['user']);
		// $mail->addReplyTo($replyto, $replyto);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$text = view('emails.block_user', compact('mailData', 'blockReason'));

		$mail->MsgHTML($text);

		return $mail->send();
	}

	public static function sendDocumentVerifyMail($subject, $data, $file = null) {
		$mailData = $data;
		$mail = new PHPMailer(true);
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = 'mail.vlcare.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@vlcare.com';
		$mail->Password = 'noreply@123';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->CharSet = "UTF-8";

		$mail->setFrom('noreply@vlcare.com', 'NXG Charge');
		$mail->addAddress($data['email'], $data['user']);
		// $mail->addReplyTo($replyto, $replyto);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$text = view('emails.document_verify', compact('mailData'));

		$mail->MsgHTML($text);

		return $mail->send();
	}

	public static function sendSMS($numbers, $msg) {
		// print_r($numbers);
		// print_r($msg);
		// $numbers should be array
		$apiKey = urlencode(SendMail::getBusRuleRef("sms_key"));
		$sender = urlencode(SendMail::getBusRuleRef("sms_sender_id"));
		$message = rawurlencode($msg);
		$numbers = implode(',', $numbers);
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		$ch = curl_init('https://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		// print_r($response);
		curl_close($ch);
	}

	public static function sendWelcomeMail($subject, $data, $file = null, $email_view) {
		// $user = $data;
		$share_amount = "50";
		$mail = new PHPMailer(true);
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = 'mail.vlcare.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@vlcare.com';
		$mail->Password = 'noreply@123';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->CharSet = "UTF-8";

		$mail->setFrom('noreply@vlcare.com', 'NXG Charge');
		$mail->addAddress($data->email, $data->name);
		// $mail->addReplyTo($replyto, $replyto);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$text = view($email_view, compact('data', 'share_amount'));

		$mail->MsgHTML($text);

		return $mail->send();
	}

	public static function sendUserInvoiceMail($subject, $data, $file = null, $email_view, $email) {
		// $user = $data;
		$data = (object) $data;
		$mail = new PHPMailer(true);
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = 'mail.vlcare.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@vlcare.com';
		$mail->Password = 'noreply@123';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->CharSet = "UTF-8";

		$mail->setFrom('noreply@vlcare.com', 'NXG Charge');
		$mail->addAddress($email, $data->name);
		// $mail->addReplyTo($replyto, $replyto);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$text = view($email_view, compact('data'));

		$mail->MsgHTML($text);

		return $mail->send();
	}

	public static function sendDriverInvoiceMail($subject, $data, $file = null, $email_view, $email) {
		// $user = $data;
		$data = (object) $data;
		$mail = new PHPMailer(true);
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->Host = 'mail.vlcare.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@vlcare.com';
		$mail->Password = 'noreply@123';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->CharSet = "UTF-8";

		$mail->setFrom('noreply@vlcare.com', 'NXG Charge');
		$mail->addAddress($email, $data->driver_name);
		// $mail->addReplyTo($replyto, $replyto);
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$text = view($email_view, compact('data'));

		$mail->MsgHTML($text);

		return $mail->send();
	}

	public static function getBusRuleRef($rule_name) {
		if ($BusRuleRef = BusRuleRef::where("rule_name", $rule_name)->first()) {
			return $BusRuleRef->rule_value;
		}
	}
}