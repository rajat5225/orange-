<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{csrf_token()}}">

        <!-- Favicon icon -->
        <!-- <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png"> -->
        <title>NXG Charge - Admin Login</title>
        <link href="{{URL::asset('/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/css/style.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/css/toastr/toastr.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/css/pages/login-register-lock.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/css/colors/default-dark.css')}}" id="theme" rel="stylesheet">

    </head>
    <body class="card-no-border">
        <div class="preloader">
            <div class="loader">
                <div class="loader__figure"></div>
                <p class="loader__label">NXG Charge</p>
            </div>
        </div>

        <section id="wrapper">
            <div class="login-register" style="background-image:url({{ asset('/images/background/login-register.jpg') }});">
                <div class="login-box card">
                    <div class="card-body">
                        <div class="login-form">
                            <h4>Login</h4>

                            @include('layouts.message')
                            <form class="form-horizontal form-material form-valide" method="post" id="loginform" action="{{route('login')}}">
                                {{csrf_field()}}
                                <div class="form-group row">
                                    <label class="col-form-label" for="val-email">Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="val-email" name="val-email" placeholder="Your Email">
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label" for="val-pass">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="val-pass" name="val-pass" placeholder="Your Password">
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <div class="checkbox checkbox-info pull-left p-t-0">
                                            <input id="checkbox-signup" type="checkbox" class="filled-in chk-col-light-blue" name="val-remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label for="checkbox-signup"> Remember me </label>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="pull-right">
                                            <a href="{{route('forgotGet')}}">Forgot Password?</a>
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info btn-flat m-b-30 m-t-30">Sign in</button>
                                <!-- <div class="register-link m-t-15 text-center">
                                    <p>Don't have account ? <a href="#"> Sign Up Here</a></p>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="{{URL::asset('/plugins/jquery/jquery.min.js')}}"></script>
        <!-- Bootstrap popper Core JavaScript -->
        <script src="{{URL::asset('/plugins/bootstrap/js/popper.min.js')}}"></script>
        <script src="{{URL::asset('/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
        <!-- slimscrollbar scrollbar JavaScript -->
        <script src="{{URL::asset('/js/perfect-scrollbar.jquery.min.js')}}"></script>
        <!--Wave Effects -->
        <script src="{{URL::asset('/js/waves.js')}}"></script>
        <!--Menu sidebar -->
        <script src="{{URL::asset('/js/sidebarmenu.js')}}"></script>
        <!--Custom JavaScript -->
        <script src="{{URL::asset('/js/custom.min.js')}}"></script>
        <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
        <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>

        @stack('scripts')
        <script type="text/javascript">
            $('div.alert').delay(3000).slideUp(300);
        </script>
    </body>
</html>