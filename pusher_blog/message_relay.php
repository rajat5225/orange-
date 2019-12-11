
<?php 
require('lib/Pusher.php');

// Change the following with your app details:
// Create your own pusher account @ https://app.pusher.com
$app_id = '540088'; // App ID
$app_key = '414e5a744049e8c9a9ed'; // App Key
$app_secret = '75ea0062e553a68faa08'; // App Secret
$pusher = new Pusher($app_key, $app_secret, $app_id);

// echo "hi vishnu";


// Check the receive message
//if(isset($_POST['message']) && !empty($_POST['message'])) {		
	$data['message'] = 'test';	
	// $pusher->trigger('private_channel_108', 'client-driverlocation', $data);
	// $pusher->trigger($_REQUEST['channel_name'], 'client-driverlocation', $data)

	// // Return the received message

	 $reply = $pusher->socket_auth($_REQUEST['channel_name'], $_REQUEST['socket_id']);
    echo $reply;
    
	//  if($pusher->trigger($_REQUEST['channel_name'], 'client-driverlocation', $data)) {
	//  echo json_encode(true);		
	//  } else {		
	//  echo json_encode(false);
	//  }
    	//}