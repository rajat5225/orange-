<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', 'WebsiteController@index')->name('home');
Route::get('/about-us', 'WebsiteController@about')->name('about');
Route::get('/contact-us', 'WebsiteController@contact')->name('contact');
Route::get('/driver-signup', 'WebsiteController@driverSignup')->name('driverSignup');
Route::post('/driver-signup', 'WebsiteController@driverSignup')->name('driverSignupPOST');
Route::get('/mobile-verify', 'WebsiteController@mobileVerify')->name('mobileVerify');
Route::post('/mobile-verify', 'WebsiteController@otpVerify')->name('otpVerify');
Route::post('/verify-otp', 'WebsiteController@verifyOTP')->name('verifyOTP');
Route::get('/faq', 'WebsiteController@faq')->name('faq');
Route::get('/terms-and-conditions', 'WebsiteController@terms')->name('terms');
Route::get('/privacy-policy', 'WebsiteController@pageContent')->name('privacy');
Route::get('/refund-and-cancellation', 'WebsiteController@pageContent')->name('refund');
Route::get('/thank-you', 'WebsiteController@thanks')->name('thanks');
Route::get('/user-agreement', 'WebsiteController@agreement')->name('userAgreement');
Route::post('/getDriverCity', ['uses' => 'WebsiteController@getDriverCity'])->name('getDriverCity');
Route::post('/getDriverVehicle', ['uses' => 'WebsiteController@getDriverVehicle'])->name('getDriverVehicle');
Route::get('/addCabs', ['uses' => 'VehicleTypeController@addCabs'])->name('addCabs');

//email templates
Route::get('/admintemp', 'WebsiteController@admintemp')->name('admintemp');
Route::get('/usertemp', 'WebsiteController@usertemp')->name('usertemp');
Route::get('/drivertemp', 'WebsiteController@drivertemp')->name('drivertemp');
Route::get('/invoicetemp', 'WebsiteController@invoicetemp')->name('invoicetemp');
Route::get('/driverinvoicetemp', 'WebsiteController@driverinvoicetemp')->name('driverinvoicetemp');

Route::prefix('admin')->group(function () {
	Route::get('/', ['middleware' => ['adminAuth'], 'uses' => 'LoginController@index'])->name('index');
	Route::post('/', ['uses' => 'LoginController@login'])->name('login');
	Route::get('/logout', ['middleware' => ['adminAuth'], 'uses' => '\App\Http\Controllers\Auth\LoginController@logout'])->name('logout');
	Route::get('/forgot-password', ['uses' => 'LoginController@sendPasswordMail'])->name('forgotGet');
	Route::post('/forgot-password', ['uses' => 'LoginController@sendPasswordMail'])->name('forgotPost');
	Route::get('/reset-password', ['uses' => 'LoginController@resetPassword'])->name('resetGet');
	Route::post('/reset-password', ['uses' => 'LoginController@resetPassword'])->name('resetPost');

	Route::middleware(['loginAuth', 'auth', 'adminAuth'])->group(function () {

		// Index and dashboard
		Route::get('/dashboard', ['uses' => 'LoginController@index'])->name('dashboard');

		//Users Module
		Route::get('/users', ['uses' => 'UserController@index'])->name('users');
		Route::get('/user/{id?}', ['uses' => 'UserController@show'])->name('viewUser');
		Route::post('/user/update/{id?}', ['uses' => 'UserController@updateStatus'])->name('updateUser');
		Route::post('/user/block', ['uses' => 'UserController@update'])->name('blockUser');

		//Store state and city session throughout the admin panel
		Route::post('/getState', ['uses' => 'StateCityController@fetchStates'])->name('getState');
		Route::post('/getCity', ['uses' => 'StateCityController@fetch'])->name('getCity');
		Route::post('/setCitySession', ['uses' => 'StateCityController@setSession'])->name('setCitySession');

		//Drivers Module
		Route::get('/drivers', ['uses' => 'UserController@index'])->name('drivers');
		Route::get('/driver/{id?}', ['uses' => 'UserController@show'])->name('viewDriver');
		Route::get('/driver/info/{id?}', ['uses' => 'UserController@info'])->name('infoDriver');
		Route::post('/driver/update/{id?}', ['uses' => 'UserController@updateStatus'])->name('updateDriver');
		Route::post('/driver/block', ['uses' => 'UserController@update'])->name('blockDriver');
		Route::post('/driver/addDoc/{id?}', ['uses' => 'UserController@addDoc'])->name('addDoc');
		Route::post('/driver/uploadDoc/{id?}', ['uses' => 'UserController@uploadDoc'])->name('uploadDoc');
		Route::post('/otherDocs/delete/{id?}', ['uses' => 'UserController@deleteDoc'])->name('deleteDoc');
		Route::post('/driver/addVehicleType/{id?}', ['uses' => 'UserController@addVehicleType'])->name('addVehicleType');
		Route::post('/getVehicleType/', ['uses' => 'UserController@getVehicleType'])->name('getVehicleType');

		//CMS module
		Route::get('/cmsPages', ['uses' => 'CMSController@index'])->name('cmsPages');
		Route::get('/cmsPage/{slug?}', ['uses' => 'CMSController@show'])->name('viewCms');
		Route::get('/cmsPage/edit/{slug?}', ['uses' => 'CMSController@show'])->name('editCms');
		Route::post('/cmsPage/update/{slug?}', ['uses' => 'CMSController@update'])->name('updateCms');

		//Vehicle Type Module
		Route::get('/vehicles', ['uses' => 'VehicleTypeController@index'])->name('vehicles');
		Route::get('/vehicle/add', ['uses' => 'VehicleTypeController@create'])->name('createVehicle');
		Route::post('/vehicle/store', ['uses' => 'VehicleTypeController@store'])->name('addVehicle');
		Route::get('/vehicle/{id?}', ['uses' => 'VehicleTypeController@show'])->name('viewVehicle');
		Route::get('/vehicle/edit/{id?}', ['uses' => 'VehicleTypeController@edit'])->name('editVehicle');
		Route::post('/vehicle/update/{id?}', ['uses' => 'VehicleTypeController@update'])->name('updateVehicle');
		Route::get('/vehicle/status/{id?}', ['uses' => 'VehicleTypeController@updateStatus'])->name('statusVehicle');
		Route::get('/vehicle/delete/{id?}', ['uses' => 'VehicleTypeController@destroy'])->name('deleteVehicle');
		Route::post('/vehicleImage/delete/{id?}', ['uses' => 'VehicleTypeController@imageDelete'])->name('imgdeleteVehicle');
		Route::get('/vehiclesAjax', ['uses' => 'VehicleTypeController@vehiclesAjax'])->name('vehiclesAjax');

		//Cab Type Module
		Route::get('/cabs', ['uses' => 'CabController@index'])->name('cabs');
		Route::get('/cab/add', ['uses' => 'CabController@create'])->name('createCab');
		Route::post('/cab/store', ['uses' => 'CabController@store'])->name('addCab');
		Route::get('/cab/{id?}', ['uses' => 'CabController@show'])->name('viewCab');
		Route::get('/cab/edit/{id?}', ['uses' => 'CabController@edit'])->name('editCab');
		Route::post('/cab/update/{id?}', ['uses' => 'CabController@update'])->name('updateCab');
		Route::get('/cab/delete/{id?}', ['uses' => 'CabController@destroy'])->name('deleteCab');
		Route::get('/cab/status/{id?}', ['uses' => 'CabController@updateStatus'])->name('statusCab');

		//Booking module
		Route::get('/rides', ['uses' => 'BookingController@index'])->name('rides');
		Route::get('/ride/{id?}', ['uses' => 'BookingController@show'])->name('viewRide');

		//Transaction module
		Route::get('/transactions', ['uses' => 'TransactionsController@index'])->name('transactions');
		Route::get('/transactions/{id?}', ['uses' => 'TransactionsController@show'])->name('viewTransactions');

		//Booking Support Requests module
		Route::get('/rideSupportRequests', ['uses' => 'BookingController@indexRequest'])->name('rideSupportRequests');
		Route::get('/rideSupportRequest/{id?}', ['uses' => 'BookingController@showRequest'])->name('viewRideSupportRequest');
		Route::get('/rideSupportRequest/edit/{id?}', ['uses' => 'BookingController@showRequest'])->name('editRideSupportRequest');
		Route::post('/rideSupportRequest/reply/{id?}', ['uses' => 'BookingController@replyRequest'])->name('replyRideSupportRequest');
		Route::get('/rideSupportRequest/status/{id?}', ['uses' => 'BookingController@updateStatusRequest'])->name('updateRideSupportRequest');
		Route::get('/rideSupportRequest/delete/{id?}', ['uses' => 'BookingController@deleteStatusRequest'])->name('deleteRideSupportRequest');

		Route::get('/notifications', ['uses' => 'NotificationController@index'])->name('notifications');
		Route::get('/notification/add', ['uses' => 'NotificationController@create'])->name('createNotification');
		Route::post('/notification/single', ['uses' => 'NotificationController@create'])->name('singleNotification');
		Route::get('/notification/store', ['uses' => 'NotificationController@store'])->name('storeNotification');

		//Coupon Code module
		Route::get('/couponCodes', ['uses' => 'CouponCodeController@index'])->name('couponCodes');
		Route::get('/couponCode/add', ['uses' => 'CouponCodeController@create'])->name('createCouponCode');
		Route::post('/couponCode/store', ['uses' => 'CouponCodeController@store'])->name('addCouponCode');
		Route::get('/couponCode/{id?}', ['uses' => 'CouponCodeController@show'])->name('viewCouponCode');
		Route::get('/couponCode/edit/{id?}', ['uses' => 'CouponCodeController@edit'])->name('editCouponCode');
		Route::post('/couponCode/update/{id?}', ['uses' => 'CouponCodeController@update'])->name('updateCouponCode');
		Route::get('/couponCode/status/{id?}', ['uses' => 'CouponCodeController@updateStatus'])->name('statusCouponCode');
		Route::post('/couponCode/delete/', ['uses' => 'CouponCodeController@destroy'])->name('deleteCouponCode');

		//FAQs module
		Route::post('/addFAQs', ['uses' => 'CMSController@addFAQs'])->name('addFAQs');
		Route::post('/updateFAQs', ['uses' => 'CMSController@updateFAQ'])->name('updateFAQs');
		Route::post('/deleteFAQ/{id?}', ['uses' => 'CMSController@destroy'])->name('deleteFAQ');

		//Transactions module and paytm integration
		Route::post('/updateFAQs', ['uses' => 'CMSController@updateFAQ'])->name('updateFAQs');
		Route::post('/deleteFAQ/{id?}', ['uses' => 'CMSController@destroy'])->name('deleteFAQ');

		//Ratings Module
		Route::get('/ratings', ['uses' => 'RatingController@index'])->name('ratings');
		Route::get('/rating/{id?}', ['uses' => 'RatingController@show'])->name('viewRating');

		//Setting Module
		Route::get('/setting', ['uses' => 'BusRuleRefController@index'])->name('setting');
		Route::get('/setting/store', ['uses' => 'BusRuleRefController@store'])->name('storeSetting');

		//Country module
		Route::get('/countries', ['uses' => 'CountryController@index'])->name('countries');
		Route::get('/country/add', ['uses' => 'CountryController@create'])->name('createCountry');
		Route::post('/country/store', ['uses' => 'CountryController@store'])->name('addCountry');
		Route::get('/country/{id?}', ['uses' => 'CountryController@show'])->name('viewCountry');
		Route::get('/country/edit/{id?}', ['uses' => 'CountryController@edit'])->name('editCountry');
		Route::post('/country/update/{id?}', ['uses' => 'CountryController@update'])->name('updateCountry');
		Route::get('/country/status/{id?}', ['uses' => 'CountryController@updateStatus'])->name('statusCountry');
		Route::post('/country/delete/', ['uses' => 'CountryController@destroy'])->name('deleteCountry');

		//State module
		Route::get('/states', ['uses' => 'StateController@index'])->name('states');
		Route::get('/state/add', ['uses' => 'StateController@create'])->name('createState');
		Route::post('/state/store', ['uses' => 'StateController@store'])->name('addState');
		Route::get('/state/{id?}', ['uses' => 'StateController@show'])->name('viewState');
		Route::get('/state/edit/{id?}', ['uses' => 'StateController@edit'])->name('editState');
		Route::post('/state/update/{id?}', ['uses' => 'StateController@update'])->name('updateState');
		Route::get('/state/status/{id?}', ['uses' => 'StateController@updateStatus'])->name('statusState');
		Route::post('/state/delete/', ['uses' => 'StateController@destroy'])->name('deleteState');

		//City module
		Route::get('/cities', ['uses' => 'CityController@index'])->name('cities');
		Route::get('/city/add', ['uses' => 'CityController@create'])->name('createCity');
		Route::post('/city/store', ['uses' => 'CityController@store'])->name('addCity');
		Route::get('/city/{id?}', ['uses' => 'CityController@show'])->name('viewCity');
		Route::get('/city/edit/{id?}', ['uses' => 'CityController@edit'])->name('editCity');
		Route::post('/city/update/{id?}', ['uses' => 'CityController@update'])->name('updateCity');
		Route::get('/city/status/{id?}', ['uses' => 'CityController@updateStatus'])->name('statusCity');
		Route::post('/city/delete/', ['uses' => 'CityController@destroy'])->name('deleteCity');
	});
});
