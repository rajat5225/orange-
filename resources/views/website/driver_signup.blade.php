@extends('website.layouts.app')

@section('content')
    <div class="driverStep wrapper bg-pink">
        <div class="container">
            <div class="row">
                <h1 class="sec-title text-center">Driver Registration</h1>
                <div class="col-lg-12 col-md-12">
                    <div class="formContent bg-white ">
                        <ul class="d-flex justify-content-center" role="tablist">
                            <li class="">
                                <a class="active" id="link1" data-toggle="tab" href="#step1">
                                    <span class="">1</span>
                                </a>
                            </li>
                            <li class="">
                                <a class=""  id="link2" data-toggle="tab" href="#step2">
                                    <span>2</span>
                                </a>
                            </li>
                            <li class="">
                                <a class="" id="link3" data-toggle="tab" href="#step3">
                                    <span>3</span>
                                </a>
                            </li>
                        </ul>
                        <form id="signupForm" method="POST" action="{{route('driverSignupPOST')}}" enctype="multipart/form-data">
                            <div class="tab-content">
                                <div id="step1" class="container tab-pane active">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="frmGrp form-group">
                                                <div class="images" id="image_profile">
                                                    <div class="pic" id="pic_profile">
                                                        <img src="{{URL::asset('/images/add-img.png')}}" alt="">
                                                    </div>
                                                    <input type="file" accept="image/*" class="driverImage file" name="profile" id="file_profile" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="text" class="input" placeholder="First Name" value="{{old('firstname')}}" name="firstname">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="text" class="input" placeholder="Last Name" value="{{old('lastname')}}" name="lastname">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="frmGrp form-group">
                                                <input type="email" class="input" placeholder="Email" value="{{old('email')}}" name="email">
                                            </div>
                                        </div>
                                    </div>
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="password" class="input" placeholder="Password" id="password" value="{{old('password')}}" name="password">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="password" class="input" placeholder="Confirm Password" value="{{old('password_confirmation')}}" name="password_confirmation">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <select class="input" name="state" id="driverState">
                                                    <option value="">-Select State-</option>
                                                    @foreach($states as $state)
                                                        <option value="{{$state}}" @if(old('state') == $state) selected @endif>{{$state}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <select class="input" name="city" id="driverCity">
                                                    <option value="">-Select State first-</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="frmGrp text-center">
                                                <input type="button" id="continue_step1" href="javascript:;" class="btn-red txt-upr" value="continue">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step2" class="container tab-pane ">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="text" class="input" placeholder="Vehicle Registration Number" value="{{old('reg_num')}}" name="reg_num">
                                                <small>Eg: RJ 14 CA 2222</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="text" class="input" placeholder="Vehicle Number Plate" value="{{old('num_plate')}}" name="num_plate">
                                                <small>Eg: RJ14 CA 2222</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <select class="input" name="vehicle_type" id="driverVehicle">
                                                    <option value="">-Select City first-</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="text" class="input" placeholder="Vehicle Manufacturer" value="{{old('vehicle_manufacturer')}}" name="vehicle_manufacturer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="text" class="input" placeholder="Vehicle Model" value="{{old('vehicle_model')}}" name="vehicle_model">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="frmGrp form-group">
                                                <input type="text" class="input" placeholder="Vehicle Color" value="{{old('vehicle_color')}}" name="vehicle_color">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="frmGrp text-center">
                                                <input type="button" id="continue_step2" href="javascript:;" class="btn-red txt-upr" value="continue">
                                                <!-- <a href="driver-signup-step-1.html" class="font16 normal clr-black">Back</a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="step3" class="container tab-pane">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6 form-group docUpload">
                                                <div class="images" id="image_license">
                                                    <div class="pic" id="pic_license">
                                                        <img src="{{URL::asset('/images/uploadicon.png')}}" alt="">
                                                        <p class="font18 clr-black">Driving Licence</p>
                                                    </div>
                                                    <input type="file" class="driverDoc file" name="license" id="file_license"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group docUpload">
                                                <div class="images" id="image_voter">
                                                    <div class="pic" id="pic_voter">
                                                        <img src="{{URL::asset('/images/uploadicon.png')}}" alt="">
                                                        <p class="font18 clr-black">Votel ID Card</p>
                                                    </div>
                                                    <input type="file" class="driverDoc file" name="voter" id="file_voter" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-6 form-group docUpload">
                                                <div class="images" id="image_aadhar">
                                                    <div class="pic" id="pic_aadhar">
                                                        <img src="{{URL::asset('/images/uploadicon.png')}}" alt="">
                                                        <p class="font18 clr-black">Aadhar Card</p>
                                                    </div>
                                                    <input type="file" class="driverDoc file" name="aadhar" id="file_aadhar" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group docUpload">
                                                <div class="images" id="image_rc">
                                                    <div class="pic" id="pic_rc">
                                                        <img src="{{URL::asset('/images/uploadicon.png')}}" alt="">
                                                        <p class="font18 clr-black">RC</p>
                                                    </div>
                                                    <input type="file" class="driverDoc file" name="rc" id="file_rc" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-12 form-group docUpload lastDoc">
                                                <div class="images" id="image_insurance">
                                                    <div class="pic" id="pic_insurance">
                                                        <img src="{{URL::asset('/images/uploadicon.png')}}" alt="">
                                                        <p class="font18 clr-black">Insurance</p>
                                                    </div>
                                                    <input type="file" class="driverDoc file" name="insurance" id="file_insurance" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="frmGrp text-center">
                                                <input type="submit" class="btn-red txt-upr submitBtn" value="Register">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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
        $(function () {

            $("body").addClass("driver-login-Page")
            var button = $('.images .pic')
            var id = '';
            var images = $('.images')

            button.on('click', function () {
                id = $(this).attr('id').split('_')[1];
                $(this).parent().find('.file').click();
            })

            $('.file').on('change', function () {
                console.log(id);
                var reader = new FileReader()
                reader.onload = function (event) {
                    var parent = images.parent();
                    var idDiv = parent.find('#image_' + id);
                    idDiv.css('display', 'flex').prepend('<div class="img" style="background-image: url(\'' + event.target.result + '\');" rel="' + event.target.result + '"><span>remove</span></div>');
                    $('.lastDoc .images').css('display', 'inline-block');
                }
                reader.readAsDataURL($('#file_'+id)[0].files[0])

            })

            @if(old('state') != null)
                $.ajax({
                    url: "{{route('getDriverCity')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data: {state: "{{old('state')}}"},
                    success: function(data){
                        var cities = JSON.parse(data);
                        var options = "<option value=''>-Select City-</option>";
                        for (var i = 0; i < cities.length; i++) {
                            options += "<option value='" + cities[i] + "'";
                                if(cities[i] == "{{old('city')}}")
                                    options += " selected";
                            options += ">" + cities[i] + "</option>";
                        }
                        $('#driverCity').html(options);
                    }
                })
            @endif

            @if(old('city') != null)
                $.ajax({
                    url: "{{route('getDriverVehicle')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data: {city: "{{old('city')}}", state: "{{old('state')}}"},
                    success: function(data){
                        var vehicles = JSON.parse(data);
                        var options = "<option value=''>-Select Vehicle-</option>";
                        for (var i = 0; i < vehicles.length; i++) {
                            options += "<option value='" + vehicles[i].id + "'";
                                if(vehicles[i].id == "{{old('vehicle_type')}}")
                                    options += " selected";
                            options += ">" + vehicles[i].vehicle_type + "</option>";
                        }
                        $('#driverVehicle').html(options);
                    }
                })
            @endif

            images.on('click', '.img', function () {
                $(this).remove()
            })

            $('#signupForm').validate({
                ignore: [],
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
                    'firstname' : {
                        required : !0,
                        lettersonly: !0,
                        maxlength: 30
                    },
                    'lastname' : {
                        required : !0,
                        lettersonly: !0,
                        maxlength: 20
                    },
                    'email' : {
                        required : !0,
                        email: !0,
                        maxlength: 50
                    },
                    'password' : {
                        required : !0,
                        maxlength: 100,
                        correctPassword: !0
                    },
                    'password_confirmation' : {
                        required : !0,
                        equalTo: "#password",
                        maxlength: 100,
                        correctPassword: !0
                    },
                    'state' : {
                        required : !0,
                    },
                    'city' : {
                        required : !0,
                    },
                    'profile' : {
                        required : !0
                    }
                },
                messages: {
                    'firstname' : {
                        required : 'Please enter your First Name.'
                    },
                    'lastname' : {
                        required : 'Please enter your Last Name.'
                    },
                    'email' : {
                        required : 'Please enter your Email Address.',
                        email : 'Please enter valid Email Address.'
                    },
                    'password' : {
                        required : 'Please enter your Password.'
                    },
                    'password_confirmation' : {
                        required : 'Please confirm your password.',
                        equalTo : 'Your password does not match.',
                    },
                    'state' : {
                        required : 'Please select your State.'
                    },
                    'city' : {
                        required : 'Please select your City.'
                    },
                    'profile' : {
                        required: 'Please select your profile picture'
                    }
                }
            });


            $(document).on('click', "#continue_step1", function () {
                if($('#signupForm').valid())
                {
                    $("a#link1").removeClass("active").addClass("done back")
                    $("a#link2").addClass("active")
                    $("#step1").removeClass("active show")
                    $("#step2").addClass("active show")
                }
            });

            $("#continue_step2").click(function () {
                $('select[name=vehicle_type]').rules('add', {
                    required: !0,
                    messages: {
                        required: "Please select your Vehicle"
                    }
                });
                $('input[name=reg_num]').rules('add', {
                    required: !0,
                    vehicle_regno: !0,
                    messages: {
                        required: "Please enter Vehicle Registration Number",
                        vehicle_regno: "Please enter valid Registration Number"
                    }
                });
                $('input[name=vehicle_manufacturer]').rules('add', {
                    required: !0,
                    maxlength: 100,
                    messages: {
                        required: "Please enter Vehicle Manufacturer",
                        maxlength: "Vehicle Manufacturer cannot exceed 100 characters"
                    }
                });
                $('input[name=vehicle_model]').rules('add', {
                    required: !0,
                    maxlength: 100,
                    messages: {
                        required: "Please enter Vehicle Model",
                        maxlength: "Vehicle Model cannot exceed 100 characters"
                    }
                });
                $('input[name=vehicle_color]').rules('add', {
                    required: !0,
                    maxlength: 50,
                    messages: {
                        required: "Please enter Vehicle Color",
                        maxlength: "Vehicle Color cannot exceed 50 characters"
                    }
                });
                $('input[name=num_plate]').rules('add', {
                    required: !0,
                    maxlength: 20,
                    vehicle_plate: 20,
                    messages: {
                        required: "Please enter Vehicle Number Plate",
                        maxlength: "Vehicle Number Plate cannot exceed 20 characters",
                        vehicle_plate: "Please enter valid Plate Number"
                    }
                });

                if($('#signupForm').valid())
                {
                    $("a#link2").removeClass("active").addClass("done back")
                    $("a#link3").addClass("active")
                    $("#step2").removeClass("active show")
                    $("#step3").addClass("active show")
                }
            });

            $('#signupForm').submit(function(e){
                $('input[name=license]').rules('add', {
                    required: !0,
                    docextension: "jpeg|png|jpg|doc|docx|pdf",
                    messages: {
                        required: "Please add your license."
                    }
                });
                $('input[name=aadhar]').rules('add', {
                    required: !0,
                    docextension: "jpeg|png|jpg|doc|docx|pdf",
                    messages: {
                        required: "Please add your Aadhar Card."
                    }
                });
                $('input[name=voter]').rules('add', {
                    required: !0,
                    docextension: "jpeg|png|jpg|doc|docx|pdf",
                    messages: {
                        required: "Please add your Voter ID Card."
                    }
                });
                $('input[name=rc]').rules('add', {
                    required: !0,
                    docextension: "jpeg|png|jpg|doc|docx|pdf",
                    messages: {
                        required: "Please add your RC."
                    }
                });
                $('input[name=insurance]').rules('add', {
                    required: !0,
                    docextension: "jpeg|png|jpg|doc|docx|pdf",
                    messages: {
                        required: "Please add your Insurance."
                    }
                });
                if(!$(this).valid())
                    e.preventDefault();

            });

            $("#link1").click(function () {
                if($(this).hasClass('back'))
                {
                    $("a#link2,a#link3").removeClass("active done back")
                    $("a#link1").addClass("active").removeClass("done back")
                    $("#step2,#step3").removeClass("active show")
                    $("#step1").addClass("active show")
                }

            });

            $("#link2").click(function () {
                if($(this).hasClass('back'))
                {
                    $("a#link3").removeClass("active")
                    $("a#link2").addClass("active").removeClass("done back")
                    $("#step3").removeClass("active show")
                    $("#step2").addClass("active show")
                }
            });
        })

    </script>
    <script>

    </script>
@endpush