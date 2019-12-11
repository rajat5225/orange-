<?php 
require('lib/Pusher.php');

// Change the following with your app details:
// Create your own pusher account @ https://app.pusher.com
$app_id = '540088'; // App ID
$app_key = '414e5a744049e8c9a9ed'; // App Key
$app_secret = '75ea0062e553a68faa08'; // App Secret
$pusher = new Pusher($app_key, $app_secret, $app_id);

// Check the receive message
if(isset($_POST['key']) && !empty($_POST['key'])) {		
	$data['message'] = "hi vishnu";	
	
	if($pusher->trigger('private-'.$_POST['device_id'], 'location_event', $data)) {				
		echo json_encode('success');			
	} else {		
		echo json_encode('error');	
	}
}