@extends('website.layouts.app')

@section('content')
	<div class="driverStep wrapper bg-pink thnx-page">
        <div class="container">
            <div class="row">
                <h1 class="sec-title text-center">Driver Registration</h1>
                <div class="col-lg-12 col-md-12">
                    <div class="formContent bg-white ">
                        <div class="thnx-block">
                            <img src="{{URL::asset('/images/thnx-page-icon.png')}}" alt="">
                            <h3>Thank You.</h3>
                            <p>Your request has been Submitted.</p>
                            <a href="{{route('home')}}">Back to home</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="wrapper fleet drvrPages bg-gray">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="rigoapp">
                        <div class="row">
                            <div class="col-lg-7 order-lg-1 col-md-12 order-md-2 col-sm-12 order-sm-2 col-12 order-2">
                                <div class="appContent">
                                    <div class="apptitle font40 extrabold">Book a NXG Charge from the App</div>
                                    <p class="clr-black font18">Download the app for exclusive deals and ease of booking </p>

                                    <a href=""><img src="{{URL::asset('/images/gplay.png')}}" alt=""></a>
                                    <a href=""><img src="{{URL::asset('/images/macapp.png')}}" alt=""></a>
                                </div>
                            </div>
                            <div class="col-lg-5 order-lg-2 col-md-12 order-md-1 col-sm-12 order-sm-1 col-12 order-1">
                                <div class="appimg">
                                    <img src="{{URL::asset('/images/appimg.png')}}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
	<script>
        $(document).ready(function(){
            $("body").addClass("driver-login-Page")
        });
    </script>
@endpush