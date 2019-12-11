
<div class="mobVerfiSec otpBlock">
    <div class="logoBig text-center">
        <img src="{{URL::asset('/images/mobile.png')}}" alt="">
    </div>
    <div class="wcRigo text-center">
        <p class="font18 normal clr-black">Verification code has been sent to <br> your mobile phone</p>
        <a href="javascript:void(0)" id="resendOtp" class="font18 normal clr-black">Resend OTP?</a>
    </div>
    <div class="errormsg text-danger text-center m-t-20">

    </div>
    <div class="successmsg text-success text-center m-t-20">

    </div>
    <form method="POST" id="verifyOtp">
        <div class="row">
            <div class="col-md-12">
                <div class="frmGrp">
                    <p class="font24 bold clr-black">Enter Verification code</p>
                    <input type="number" name="otp[]" class="otpInput" maxlength="1">
                    <input type="number" name="otp[]" class="otpInput" maxlength="1">
                    <input type="number" name="otp[]" class="otpInput" maxlength="1">
                    <input type="number" name="otp[]" class="otpInput" maxlength="1">
                </div>
            </div>
            <input type="hidden" name="mobilenumber" id="mobilenumber" value="{{$number}}">
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="frmGrp text-center">
                    <input type="submit" class="btn-red txt-upr" id="submit" style="text-decoration: none" value="Continue">
                    <a href="javascript:void(0)" id="changeMobileNo" class="font18 normal clr-black">Change Mobile Number</a>
                </div>
            </div>
        </div>
    </form>
</div>