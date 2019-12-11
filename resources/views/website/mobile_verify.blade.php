@extends('website.layouts.app')

@section('content')
	<div class="driverStep wrapper bg-pink">
        <div class="container">
            <div class="row">
                <h1 class="sec-title text-center">Driver Registration</h1>
                <div class="col-lg-12 col-md-12">
                    <div class="formContent bg-white mobVerify">
                        <div class="mobVerfiSec">
                            <div class="logoBig text-center">
                                <img src="{{URL::asset('/images/logo-big.png')}}" alt="">
                            </div>
                            <div class="wcRigo text-center">
                                <p class="font30 bold clr-black">Welcome to NXG Charge!</p>
                                <p class="font18 normal clr-black">Enter Your Mobile Number to Continue</p>
                            </div>
                            <div class="errormsg text-danger m-t-20">

                            </div>
                            <form id="formValide">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="frmGrp form-group">
                                            <span class="input">+91</span>
                                            <input type="number" class="input mtg_number" minlength="10" maxlength="10" name="mobile" placeholder="Phone Number" id="number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="frmGrp text-center">
                                           <input type="submit" class="btn-red txt-upr" id="submitBtn" value="continue">
                                        </div>
                                    </div>
                                </div>
                            </form>
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

                                    <a href="">
                                        <img src="{{URL::asset('/images/gplay.png')}}" alt="">
                                    </a>
                                    <a href="">
                                        <img src="{{URL::asset('/images/macapp.png')}}" alt="">
                                    </a>
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
        $(document).on('keypress',".mtg_number",function (e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});
 $(document).on('keypress',".mtg_deci",function (e) {
     if ((e.which != 46 || $(this).val().indexOf('.') != -1) && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});

 $.extend($.validator.messages, {
    maxlength: $.validator.format("Please enter {0} characters.")
});

        $(document).ready(function(){
            $("body").addClass("driver-login-Page");

            $('#formValide').validate({
                errorClass: "invalid-feedback animated fadeInDown",
                errorElement: "div",
                errorPlacement: function(e, a) {
                    jQuery(a).parents(".form-group").append(e);
                    $('.animated').css('display', 'flex');
                },
                highlight: function(e) {
                    jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid")
                },
                success: function(e) {
                    jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove()
                },
                rules: {
                    'mobile' : {
                        required : !0,
                        digits: !0,
                        exactlength: 10
                    }
                },
                messages: {
                    'mobile':{
                        exactlength: "Please enter correct mobile number."
                    }
                }
            });
            $(document).on('submit', '#formValide', function(e){
                e.preventDefault();
                $('#submitBtn').prop('disabled', true);
                $('#submitBtn').val('Please Wait');
                if($(this).valid())
                {
                    var number = $('#number').val();
                    $.ajax({
                        url: "{{route('otpVerify')}}",
                        type: "POST",
                        data: {number: number},
                        success: function(data){
                            var res = JSON.parse(data);
                            if(res.status == 1)
                            {
                                $('.formContent').html(res.message);
                                $('.errormsg').hide();

                            }
                            else
                            {
                                $('.errormsg').html(res.message);
                                $('#submitBtn').prop('disabled', false);
                                $('.errormsg').show();
                                $('#number').val(res.number);
                            }
                        }
                    })
                }
            })

            $(document).on('click', '#resendOtp', function(){

                $('#submitBtn').prop('disabled', true);
                var number = $('#mobilenumber').val();
                $.ajax({
                    url: "{{route('otpVerify')}}",
                    type: "POST",
                    data: {number: number},
                    success: function(data){
                        var res = JSON.parse(data);
                        if(res.status == 1)
                        {
                            $('.formContent').html(res.message);
                            $('.errormsg').hide();
                            $('.successmsg').html('OTP sent successfully.');

                        }
                        else
                        {
                            $('.errormsg').html(res.message);
                            $('#submitBtn').prop('disabled', false);
                            $('.errormsg').show();
                            $('.successmsg').hide();
                            $('#number').val(res.number);
                        }
                    }
                })
            })

            $('#verifyOtp').validate({
                errorClass: "invalid-feedback animated fadeInDown",
                errorElement: "div",
                errorPlacement: function(e, a) {
                    jQuery(a).parents(".form-group").append(e);
                    $('.animated').css('display', 'flex');
                },
                highlight: function(e) {
                    jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid")
                },
                success: function(e) {
                    jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove()
                },
                rules: {
                    'otp[]' : {
                        required : !0,
                        digits: !0,
                        exactlength: 1
                    }
                },
                messages: {
                    'otp[]':{
                        exactlength: "Please enter valid OTP."
                    }
                }
            });

            $(document).on('keypress keyup', 'input[name="otp[]"]', function(){
                if (window.getSelection){ // all modern browsers and IE9+
                    selectedText = window.getSelection().toString()
                }
                if(selectedText.length < 1 && $(this).val().length == 1)
                {
                    $(this).next('input').focus();
                    return false;
                }
            });

            $(document).on('submit', '#verifyOtp', function(e){
                e.preventDefault();
                $('#submit').prop('disabled', true);
                $('#submit').val('Please Wait');
                if($(this).valid())
                {
                    var number = $('#mobilenumber').val();
                    var otp = '';

                    $('input[name="otp[]"]').each(function(index){
                        otp += $(this).val();
                    })
                    console.log(otp);
                    $.ajax({
                        url: "{{route('verifyOTP')}}",
                        type: "POST",
                        data: {number: number, otp:otp},
                        success: function(data){
                            console.log(data);
                            var res = JSON.parse(data);
                            if(res.status == 1)
                            {
                                window.location = "{{route('driverSignup')}}";
                            }
                            else if(res.status == 0)
                            {
                                $('.errormsg').html(res.message);
                                $('#submit').prop('disabled', false);
                                $('.errormsg').show();
                            }
                            else if(res.status == 2)
                            {
                                $('.formContent').html(res.message);
                                $('.errormsg').html('Wrong OTP');
                                $('#submit').prop('disabled', false);
                                $('.errormsg').show();
                            }
                        }
                    })
                }
            })

            $(document).on('click', '#changeMobileNo', function(){
                var image = "{{URL::asset('/images/logo-big.png')}}";

                $('.formContent').html('<div class="mobVerfiSec"><div class="logoBig text-center"><img src="'+image+'" alt=""></div><div class="wcRigo text-center"><p class="font30 bold clr-black">Welcome to NXG Charge!</p><p class="font18 normal clr-black">Enter Your Mobile Number to Continue</p></div><div class="errormsg text-danger m-t-20"></div><form id="formValide"><div class="row"><div class="col-md-12"><div class="frmGrp form-group"><span class="input">+91</span><input type="number" class="input" name="mobile" placeholder="Phone Number" id="number" value="'+$('#mobilenumber').val()+'"></div></div></div><div class="row"><div class="col-md-12"><div class="frmGrp text-center"><input type="submit" class="btn-red txt-upr" value="continue"></div></div></div></form></div>');
            })
        });
    </script>
@endpush