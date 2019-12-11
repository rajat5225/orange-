<?php

namespace App\Http\Controllers;
use App\Library\Paytm;
use App\Library\ResponseMessages;
use App\Model\Transaction;
use App\Model\User;
use App\Model\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TransactionsController extends Controller {
	public $paytmSettings;
	public $paytm;
	public $transaction;
	public $city;
	public $state;
	public $country;

	public function __construct() {
		$this->paytm = new Paytm;
		$this->paytmSettings = $this->paytm->getConfig();
		$this->transaction = new Transaction;
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
		$transactions = $this->transaction->fetchTransactions($this->city, $this->state, $this->country, $request);
		// dd($transactions->toArray());
		// dd($transactions[0]->user->name);
		return view('transactions.index', compact('transactions'));
	}

	/**
	 * generate checksum for paytm integration
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function generateCheckSum(Request $request) {

		$required = array("user_id", "amount", "device_type");
		$input = array_keys($request->all());
		$existance = implode(", ", array_diff($required, $input));
		if (!empty($existance)) {
			if (count(array_diff($required, $input)) == 1) {
				$response = array(
					"status" => 101,
					"message" => $existance . " key is missing",
				);
			} else {
				$response = array(
					"status" => 101,
					"message" => $existance . " keys are missing",
				);
			}
			echo json_encode($response);
			exit;
		}

		if ($user = User::select("status")->where("id", $request->user_id)->first()) {

			if ($user->status == "AC") {

			} else {
				$response = array(
					"status" => 216,
					"message" => ResponseMessages::getStatusCodeMessages(216),
				);
				echo json_encode($response);
				exit;
			}
		} else {
			$response = array(
				"status" => 321,
				"message" => ResponseMessages::getStatusCodeMessages(321),
			);
			echo json_encode($response);
			exit;
		}

		$transaction = new Transaction;
		// $transaction->transaction_code = Helper::generateNumber("transactions", "transaction_code");
		$transaction->user_id = $request->user_id;
		$transaction->amount = $request->amount;
		$transaction->device_type = $request->device_type;
		$transaction->save();

		$paramList = array();
		$findme = 'REFUND';
		$findmepipe = '|';

		$paramList["MID"] = $this->paytmSettings['paytm_staging_mid'];
		$paramList["ORDER_ID"] = 'ORDER' . $transaction->id;
		$paramList["CUST_ID"] = 'CUST' . $request->user_id;
		$paramList["MOBILE_NO"] = $user->mobile_number;
		$paramList["EMAIL"] = $user->email;
		$paramList["CHANNEL_ID"] = $this->paytmSettings['paytm_channel_id'];
		$paramList["TXN_AMOUNT"] = $request->amount;
		$paramList["WEBSITE"] = $this->paytmSettings['paytm_app_name'];
		$paramList["INDUSTRY_TYPE_ID"] = $this->paytmSettings['paytm_industry_type'];
		$paramList["CALLBACK_URL"] = $this->paytmSettings['paytm_callback_url'];

		// $paramList["MID"] = "RigoRi53284073450976";
		// $paramList["ORDER_ID"] = "order".rand(1,999);
		// $paramList["CUST_ID"] = "cust123";
		//  		$paramList["MOBILE_NO"] = "7777777777";
		//   	$paramList["EMAIL"] = "manish.vervelogic@gmail.com";
		// $paramList["CHANNEL_ID"] = "WEB";
		// $paramList["TXN_AMOUNT"] = "100.12";
		// $paramList["WEBSITE"] = "WEBSTAGING";
		// $paramList["INDUSTRY_TYPE_ID"] = "Retail";
		// $paramList["CALLBACK_URL"] = "https://rigoride.vervelogic.in/api/paytmCallback";

		// define("merchantMid", "RigoRi53284073450976");
		// // Key in your staging and production MID available in your dashboard
		// define("merchantKey", "7UnEiQUfqpCsQ_14");
		// // Key in your staging and production merchant key available in your dashboard
		// define("orderId", "order".rand(1,999));
		// define("channelId", "WEB");
		// define("custId", "cust123");
		// define("mobileNo", "7777777777");
		// define("email", "manish.vervelogic@gmail.com");
		// define("txnAmount", "100.12");
		// define("website", "WEBSTAGING");
		// // This is the staging value. Production value is available in your dashboard
		// define("industryTypeId", "Retail");
		// // This is the staging value. Production value is available in your dashboard
		// // define("callbackUrl", "https://<Merchant_Response_URL>");
		// define("callbackUrl", "http://rigoride.vervelogic.in/paytm/response.php");

		/*$paramList["EMAIL"] = 'rigoride2017@gmail.com';
			$paramList["MOBILE_NO"] = '7777777777';
		*/
		// foreach ($_POST as $key => $value) {
		// 	$pos = strpos($value, $findme);
		// 	$pospipe = strpos($value, $findmepipe);
		// 	if ($pos === false || $pospipe === false) {
		// 		$paramList[$key] = $value;
		// 	}
		// }
		// print_r($paramList);
		// die;

		//create checksum from library
		$checkSum = $this->paytm->getChecksumFromArray($paramList, $this->paytmSettings['paytm_merchant_key']);

		$paramList["CHECKSUMHASH"] = $checkSum;
		// $paramList["CHECKSUMHASH"] = "khzT9ivO/8Ry/ddhPJZW5lyyPC4ffsyyD+zdiWpOIRKDIBe3ZB8H59xjkvMCOiVTbzlbJF7gOIlmqUlJFtLLfBmPeLsYD6QUJ/Hqo+q0+as=";
		$paramList["user_id"] = $request->user_id;
		$paramList["amount"] = $request->amount;
		// $isValidChecksum = $this->paytm->verifychecksum_e($paramList, $this->paytmSettings['paytm_merchant_key'], $checkSum); //will return TRUE or FALSE string.
		// echo $isValidChecksum ? "Y" : "N";
		//save checksum for this transaction in database
		$tr = Transaction::find($transaction->id);
		$tr->checksum = $checkSum;
		$tr->transaction_code = $paramList["ORDER_ID"];
		$tr->save();
		// $transactionURL = "https://securegw.paytm.in/theia/processTransaction";
		$transactionURL = $this->paytmSettings['paytm_transaction_url'];
		$paytmChecksum = $checkSum;

		// echo json_encode($paramList);
		return view("transactions.generate_checksum", compact('paramList', 'transactionURL', 'paytmChecksum'));
	}

	/**
	 * paytm callback function to save transaction details from paytm
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function paytmCallback(Request $request) {
		$Transaction = Transaction::where("transaction_code", $request['ORDERID'])->first();
		// $Transaction->paytm_order_id = $request['ORDERID'];
		$Transaction->paytm_txnid = $request['TXNID'];
		$Transaction->paytm_mid = $request['MID'];
		$Transaction->paytm_txnamount = $request['TXNAMOUNT'];
		$Transaction->paytm_paymentmode = $request['PAYMENTMODE'];
		$Transaction->paytm_currency = $request['CURRENCY'];
		$Transaction->paytm_txndate = $request['TXNDATE'];
		$Transaction->paytm_status = $request['STATUS'];
		$Transaction->paytm_respcode = $request['RESPCODE'];
		$Transaction->paytm_respmsg = $request['RESPMSG'];
		$Transaction->paytm_gateway_name = $request['GATEWAYNAME'];
		$Transaction->paytm_bank_txn_id = $request['BANKTXNID'];
		$Transaction->paytm_bank_name = $request['BANKNAME'];
		$Transaction->paytm_checksum = $request['CHECKSUMHASH'];
		($request['STATUS'] == "TXN_SUCCESS") ? $Transaction->status = "CM" : $Transaction->status = "FL";
		($request['STATUS'] == "TXN_SUCCESS") ? $Transaction->transaction_status = "success" : $Transaction->transaction_status = "failed";
		$Transaction->save();
		$status = $Transaction->status;
		if ($request['STATUS'] == "TXN_SUCCESS") {
			$user = User::where("id", $Transaction->user_id)->first();
			$user->wallet_amount = $user->wallet_amount + $Transaction->paytm_txnamount;
			$user->save();
		}
		$wallet = new Wallet();
		$wallet->user_id = $Transaction->user_id;
		$wallet->type = "transaction";
		$wallet->payment_mode = "wallet";
		$wallet->transaction_id = $Transaction->id;
		$wallet->save();
		// $device_type = $Transaction->device_type;
		return view("transactions.payment_response", compact('status'));
		// if($request['STATUS']=="TXN_SUCCESS"){
		// 	if ($device == 'android') {
		// 		redirect('payment_success');
		// 	} else {
		// 		redirect('rigoride_success://');
		// 	}
		// }else{
		// 	if ($device == 'android') {
		// 		redirect('payment_failed');
		// 	} else {
		// 		redirect('rigoride_failed://');
		// 	}
		// }
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
}
