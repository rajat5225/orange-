<?php
namespace App\Library;

class ResponseMessages {

	public static function getStatusCodeMessages($status) {
		$codes = array(
			0 => 'Decline Booking',
			1 => 'Accept Booking',
			2 => 'Arrived Driver',
			3 => 'Start Ride Successfully',
			4 => 'Cancel Ride',
			5 => 'End ride successfully ',
			6 => 'Pending',
			8 => 'Request sent',
			9 => 'Booking status is not match with previous booking status',
			10 => 'You have successfully logout',
			11 => 'OTP sent successfully',
			12 => 'Reviewed successfully',
			13 => 'No bookings found',
			14 => 'No Support found',
			15 => 'No Compliments found',

			101 => 'Missing input key',
			102 => 'You are now registered with us please login',
			103 => 'A problem occurred during registration. Please try again',
			104 => 'This email address is already in use.',
			105 => 'This phone number is already in use.',
			106 => 'Your account is not verified by the administrator. Please contact admin',
			107 => 'Welcome to NXG Charge',
			108 => 'Invalid credentials',
			109 => 'Document has been uploaded',
			110 => 'All documents are uploaded succesfully',
			111 => 'Please try again',
			112 => 'List of vehicles',
			113 => 'List of documents',
			114 => 'Document uploaded successfully',
			115 => 'Added images',
			116 => 'List of drivers',
			117 => 'Location update successfully',
			118 => 'Visibility updated successfully',
			119 => 'Your profile has been updated',
			120 => 'Your password has been updated',
			121 => 'Current Password does not matched',
			122 => 'Profile updated Successfully',
			123 => 'List of cabs',
			124 => 'Updated Successfully',
			125 => 'Booking declined',
			126 => 'Booking accepted',
			127 => 'Booking Already Accepted',
			128 => 'Driver has arrived',
			129 => 'Ratings submitted',
			130 => 'Average rating',
			131 => 'Your Ride has been started',
			132 => 'Your ride has been ended',
			133 => 'Previous ride List',
			134 => 'Currently, no rides available',
			135 => 'Ride has been cancelled',
			136 => 'Upcoming ride List',
			137 => 'Waiting time',
			138 => 'We have scheduled your ride. Driver details will be sent 10 minutes before the scheduled time.',
			139 => 'Calculated Fare Estimate',
			140 => 'Booking added Successfully',
			141 => 'You have been logged out',
			142 => 'Driver Visibility',
			143 => 'Email sent',
			144 => 'OK',
			145 => 'Ride Finished',
			146 => 'Booking request sent',
			147 => 'Pending Bookings',
			148 => 'Sorry no cabs found',
			149 => 'Document has been deleted',
			150 => 'Document not found',
			151 => 'Favorite location not found',
			152 => 'No trusted contacts found',
			153 => 'Your minimum balance is over, please add money to your wallet balance to enjoy wallet rides',
			154 => 'Country not available',

			200 => "OK",
			211 => 'List of Cabs',
			212 => 'No cabs found for this city',
			213 => 'Service is not available',
			214 => 'User not found',
			215 => 'Incorrect current password',
			216 => 'You are blocked, please contact to administrator',
			217 => 'Login screen',
			218 => 'Registration screen',
			219 => 'State not available',
			220 => 'City not available',
			221 => 'Something went wrong',
			222 => 'Please select vehicle type',
			223 => 'Your Trusted contacts limit is now exceeded',
			224 => 'This Mobile number is already added',
			225 => 'Contact saved',
			226 => 'Currently you have no trusted contact',
			229 => 'Contact deleted',
			227 => 'No coupon code available',
			230 => 'No compliments available',
			231 => 'No previous rides',
			232 => 'Coupon code can not be applied',
			233 => 'Coupon code applied',
			234 => 'Your ride is now shared’',
			235 => 'No wallet history found',
			236 => 'Documents are not uploaded, please upload your documents',
			237 => 'No documents uploaded',
			238 => 'Sorry the passwords did not match',
			239 => 'Mobile number does not exist',
			240 => 'Your Password has been changed',
			241 => 'You are registered as a user please login with NXG Charge User app ',
			242 => 'You have registered as a driver. Please login with NXG Charge Driver app',
			243 => 'Your Address has been changed',
			244 => "This address is already in use please try again with a new address",
			245 => "Your Drop location has been changed",
			246 => 'No upcoming rides available',
			247 => "Your ride fare does not match with the coupon’s minimum amount",
			248 => "You have already applied this coupon please try another one",
			249 => "You have already applied a ride based coupon",

			301 => 'Your vehicle details are saved, please upload your documents to complete your registration',
			302 => "This vehicle's registration number or number plate is already registered with us",
			303 => "No driver found",
			304 => "This ride has already been booked with someone else",
			305 => "Document failed to upload, please try again",
			306 => "You are visible, you can take rides now",
			307 => "You are not visible, you cannot take rides now",
			308 => "Invalid email address or phone number",
			309 => "Invalid password",
			310 => "Your personal details are saved, please add your vehicle details",
			311 => "Your personal details not saved, please verify your identity",
			312 => "Your vehicle not verified, please verify your vehicle",
			313 => "Your documents not verified, please contact to administrator",
			314 => "Login failed",
			315 => 'Document saved, please upload another document',
			316 => 'This ride is no longer available',
			317 => 'You already have a scheduled booking for this time slot',
			318 => 'You have already booked your ride for this time slot',
			319 => 'You are registered as a user please login with NXG Charge User app',
			320 => 'You have registered as a driver. Please login with NXG Charge Driver app',
			321 => 'User does not exist',
			322 => 'You are registered as a user please login with NXG Charge User app',
			323 => 'You have registered as a driver. Please login with NXG Charge Driver app',
			324 => 'This password is already used please try with another one',
			325 => 'Vehicle not found',
			326 => 'Scheduled booking has been cancelled',
			327 => 'Your scheduled ride updated. Driver details will be sent 10 minutes before the scheduled time.',
			328 => 'No scheduled booking found',
			329 => "You can't this ride now",
			330 => "Booking does not exist",
			331 => "You are already logged in on a different device",
			332 => "You can't cancel this ride, as this ride is booked from a different device",
			333 => "You can't edit this scheduled ride, as this ride is booked from a different device",
			334 => "You can't schedule ride in this time slot",

			500 => "Internal server error",
			501 => 'Exception error',
			502 => 'Something goes wrong from server',
		);
		return (isset($codes[$status])) ? $codes[$status] : "";
	}
}