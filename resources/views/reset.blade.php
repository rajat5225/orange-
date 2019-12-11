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
        <title>NXG Charge - Reset Password</title>
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
                            <h4>Reset Password</h4>
                            @include('layouts.message')
                            <form class="form-valide" method="post" action="{{route('resetPost')}}">
                                {{csrf_field()}}
                                <input type="hidden" name="val-email" value="{{$email}}">
                                <div class="form-group row">
                                    <label class="col-form-label" for="val-password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="val-password" name="val-password" placeholder="Choose a safe one..">
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label" for="val-confirm-password">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="val-confirm-password" name="val-confirm-password" placeholder="..and confirm it!">
                                </div>
                                <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30">Confirm</button>
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
