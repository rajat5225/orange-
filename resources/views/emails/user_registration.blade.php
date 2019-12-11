<style>
    @import url('https://fonts.googleapis.com/css?family=Roboto:400,500,700');
</style>
<table cellspacing="0" style="margin: -8px auto 0; width: 700px; font-family: 'Roboto', sans-serif;">

    <tr>
        <td style="border-top: 7px solid #ff3800;">

        </td>
    </tr>

    <tr>
        <td style="border-bottom: 1px solid #ccc; padding-bottom: 30px;">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <img style="margin: 30px auto 0;    display: block;" src="{{URL::asset('/images/logo.png')}}" alt="logo">
                    </td>
                </tr>
            </table>
        </td>
    </tr>


    <tr>
        <td>
            <table>
                <tr>
                    <td>
                        <p style="font-weight: bold; font-size: 18px; margin-top: 60px;">Hi {{$data->name}},</p>
                        <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">Welcome to NXG Charge.Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                            laudantium, totam rem aperiam.</p>
                        <div>
                            <img style="margin:0 auto; display: block;" src="{{URL::asset('/images/template-mobile-img.png')}}" alt="">
                        </div>
                    </td>
                </tr>
                {{-- <tr>
                    <td style="border-bottom: 1px solid #ccc;padding-bottom: 20px;margin-bottom: 20px;display: block;">
                        <p style="font-size: 24px;font-weight: bold;color: #262626;margin-top: 45px;">Why ride with NXG Charge?</p>
                        <img src="{{URL::asset('/images/welcome-template-car-icon.png')}}" alt="">
                        <p style="font-size: 21px;color: #262626;margin-bottom: 0;">Secure And Safe Rides</p>
                        <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">Verified drivers, emergency alert button and live ride tracking are some of the features that we
                            have in place to ensure you a safe travel experience.</p>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #ccc;padding-bottom: 20px;margin-bottom: 20px;display: block;">
                        <img src="{{URL::asset('/images/welcome-template-phone-icon.png')}}" alt="">
                        <p style="font-size: 21px;color: #262626;margin-bottom: 0;">Share Live Location</p>
                        <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">To Travel With Lowest Fares Choose NXG Charge, For A faster Travel Exprience We Have Some Fixed Routes With Zero Deviations. Choose Your Rides and share your Live Location your Friend and Family.</p>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #ccc;padding-bottom: 20px;margin-bottom: 20px;display: block;">
                        <img src="{{URL::asset('/images/welcome-template-car1-icon.png')}}" alt="">
                        <p style="font-size: 21px;color: #262626;margin-bottom: 0;">In NXG Charge Entertainment</p>
                        <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">NXG Charge  lets you ride a prime sedan at mini fares book rides without surge pricing and has zero wait time.</p>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #ccc;padding-bottom: 20px;margin-bottom: 20px;display: block;">
                        <img src="{{URL::asset('/images/welcome-template-car02-icon.png')}}" alt="">
                        <p style="font-size: 21px;color: #262626;margin-bottom: 0;">Cashless Rides</p>
                        <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">Now go Cashless and travel easy, Simply recharge your NXG Charge wallet enjoy cashless payments.</p>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #ccc;padding-bottom: 20px;margin-bottom: 20px;display: block;">
                        <img src="{{URL::asset('/images/welcome-template-car3-icon.png')}}" alt="">
                        <p style="font-size: 21px;color: #262626;margin-bottom: 0;">NXG Charge Select</p>
                        <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">NXG Charge  lets you ride a prime sedan at mini fares book rides without surge pricing and has zero wait time.</p>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #ccc;padding-bottom: 20px;margin-bottom: 20px;display: block;">
                        <img src="{{URL::asset('/images/welcome-template-car4-icon.png')}}" alt="">
                        <p style="font-size: 21px;color: #262626;margin-bottom: 0;">Top Drivers NXG Charge</p>
                        <p style="font-size: 16px;color: #5f5e5e;line-height: 1.5;margin-top: 10px;">All our NXG Charge Drivers have their background Verified and they are trained to Deliver only the best experience to you.</p>
                    </td>
                </tr> --}}
            </table>
        </td>
    </tr>


    {{-- <tr style="text-align: center;margin: 50px 0;">
        <td style="margin-top: 45px;margin-bottom: 60px;display: block;padding: 0 100px;">
            <div>
                <img src="{{URL::asset('/images/template-car-icon.png')}}" alt="">
            </div>
            <p style="color: #5f5e5e;font-size: 16px;line-height: 1.5;">Invite your friends and family. Share the NXG Charge love and give friends free rides to try NXG Charge, worth
                up to ₹ {{$share_amount}} each! </p>
            <p style="color: #ff3800;font-size: 26px;">Share code: {{$data->referral_code}}</p>
        </td>
    </tr> --}}
    <tr style="text-align: center;margin: 50px 0;">
        <td style="margin-top: 45px;margin-bottom: 60px;display: block;padding: 0 100px;">
            <div style="width: 50%;float: left;">
                <img src="{{URL::asset('/images/app-store.png')}}" alt="">
            </div>
            <div>
                <img src="{{URL::asset('/images/google-play.png')}}" alt="">
            </div>
        </td>
    </tr>
    <tr style="">
        <td style="background-color: #4d4d4d;padding: 15px 25px;">
            <table style="width:100%">
                <tr>
                    <td style="padding-bottom: 25px;">
                        <div style="float: left;width: 50%;">
                            <img src="{{URL::asset('/images/template-footer-logo.png')}}" alt="">
                        </div>
                        <div style="float: right;width: 50%;text-align: right;margin-top: 15px;">
                            <ul style="list-style: none;">
                                <li style="display: inline-block;margin: 0 5px;">
                                    <a href="#">
                                        <img src="{{URL::asset('/images/fb-icon.png')}}" alt="">
                                    </a>
                                </li>
                                <li style="display: inline-block;margin: 0 5px;">
                                    <a href="#">
                                        <img src="{{URL::asset('/images/twit-icon.png')}}" alt="">
                                    </a>
                                </li>
                                <li style="display: inline-block;margin: 0 5px;">
                                    <a href="#">
                                        <img src="{{URL::asset('/images/insta-icon.png')}}" alt="">
                                    </a>
                                </li>
                                <li style="display: inline-block;margin: 0 5px;">
                                    <a href="#">
                                        <img src="{{URL::asset('/images/google-icon.png')}}" alt="">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #4d4d4d;padding: 15px 25px;text-align: center;border-top: 1px solid #fff;">
                        <p style="color: #fff; font-size: 22px;">Need help?</p>
                        <p style="color: #a2a1a1;font-size:14px;">Tap Help in your app to contact support with</p>
                        <p style="color: #a2a1a1;font-size:14px;">questions about your trip.</p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>


</table>