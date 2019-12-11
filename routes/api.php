<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("login", "ApiController@login");
Route::post("isMobileNumberExist", "ApiController@isMobileNumberExist");
Route::post("userRegister", "ApiController@userRegister");
Route::post("driverRegister", "ApiController@driverRegister");
Route::post("vehicleRegister", "ApiController@vehicleRegister");
Route::post("getDocumentType", "ApiController@getDocumentType");
Route::post("getVehicleType", "ApiController@getVehicleType");
Route::post("updateLocation", "ApiController@updateLocation");
Route::post("changePassword", "ApiController@changePassword");
Route::post("updatePassword", "ApiController@updatePassword");
Route::post("profileVisible", "ApiController@profileVisible");
Route::post("logout", "ApiController@logout");
// New Api param
Route::post("getBookingInfo", "ApiController@getBookingInfo");
Route::post("getAllBookingMonthly", "ApiController@getAllBookingMonthly");
Route::post("getAllBookingInfo", "ApiController@getAllBookingInfo");
Route::post("getCompliments", "ApiController@getCompliments");
Route::post("getMonthlyEarning", "ApiController@getMonthlyEarning");
Route::post("getMonthwiseEarning", "ApiController@getMonthwiseEarning");

//api apoorva

Route::get("getComplimentsCount", "ApiController@getComplimentsCount");

// New Api
Route::post("getUserDetail", "ApiController@getUserDetail");
Route::post("getUserRating", "ApiController@getUserRating");
Route::post("userRating", "ApiController@userRating");
Route::post("updateProfile", "ApiController@updateProfile");
Route::post("uploadDocuments", "ApiController@uploadDocuments");
Route::post("forgotPassword", "ApiController@forgotPassword");
Route::post("countryList", "ApiController@countryList");
Route::post("stateList", "ApiController@stateList");
Route::post("cityList", "ApiController@cityList");
Route::post("cabList", "ApiController@cabList");
Route::post("driverList", "ApiController@driverList");
Route::post("sendTestNotification", "ApiController@sendTestNotification");

Route::post("cabsList", "ApiController@cabsList");
Route::post("getFareEstimate", "ApiController@getFareEstimate");
Route::post("addTrustedContacts", "ApiController@addTrustedContacts");
Route::post("getTrustedContacts", "ApiController@getTrustedContacts");
Route::post("deleteContact", "ApiController@deleteContact");
Route::post("getCouponCodes", "ApiController@getCouponCodes");
Route::post("isCouponCodeExist", "ApiController@isCouponCodeExist");
Route::post("getComplements", "ApiController@getComplements");
Route::post("getSupportSubject", "ApiController@getSupportSubject");
Route::post("bookingSupport", "ApiController@bookingSupport");
Route::post("getBookingSupport", "ApiController@getBookingSupport");
Route::post("addFavLocation", "ApiController@addFavLocation");
Route::post("getFavLocation", "ApiController@getFavLocation");
Route::post("deleteFavLocation", "ApiController@deleteFavLocation");
Route::post("deleteUserDocument", "ApiController@deleteUserDocument");

Route::post("getWalletBalance", "ApiController@getWalletBalance");
Route::post("getWalletHistory", "ApiController@getWalletHistory");
Route::post("getUserDocuments", "ApiController@getUserDocuments");
Route::post("testUser", "ApiController@testUser");
// Ride
Route::post("upcomingRide", "ApiController@upcomingRide");
Route::post("previousRide", "ApiController@previousRide");
Route::post("shareRide", "ApiController@shareRide");
Route::post("getSharedRide", "ApiController@getSharedRide");
Route::post("updateRunningRide", "ApiController@updateRunningRide");
Route::post("updateRunningRideDriver", "ApiController@updateRunningRideDriver");

Route::post("bookRide", "ApiController@bookRide");
Route::post("responseRide", "ApiController@responseRide");
Route::post("driverArrived", "ApiController@driverArrived");
Route::post("startRide", "ApiController@startRide");
Route::post("endRide", "ApiController@endRide");
Route::post("cancelRide", "ApiController@cancelRide");
Route::post("finishRide", "ApiController@finishRide");
Route::post("scheduleRide", "ApiController@scheduleRide");
Route::post("editScheduledRide", "ApiController@editScheduledRide");
Route::post("cancelSchduledRide", "ApiController@cancelSchduledRide");
Route::post("bookRideAgain", "ApiController@bookRideAgain");

//paytm configure
Route::get('/paytmConfigure', ['uses' => 'TransactionsController@generateCheckSum'])->name('generateCheckSum');
Route::post('/paytmCallback', ['uses' => 'TransactionsController@paytmCallback'])->name('savePaytmDetails');

Route::get('/payment_success', ['uses' => 'TransactionsController@paymentSuccess'])->name('generateCheckSum');
Route::get('/payment_failed', ['uses' => 'TransactionsController@paymentFailed'])->name('generateCheckSum');

######### Cron Jobs ###########
Route::get("cronCheckRideStatus", "ApiController@cronCheckRideStatus");
Route::get("cronScheduledRequest", "ApiController@cronScheduledRequest");
Route::get("cronScheduledRequestToNextDrivers", "ApiController@cronScheduledRequestToNextDrivers");