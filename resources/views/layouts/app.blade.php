
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
        <title>NXG Charge - @yield('title')</title>
        <link href="{{URL::asset('/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
        <!-- This page CSS -->
        <!-- chartist CSS -->
        <link href="{{URL::asset('/plugins/chartist-js/dist/chartist.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css')}}" rel="stylesheet">
        <!--c3 CSS -->
        <link href="{{URL::asset('/plugins/c3-master/c3.min.css')}}" rel="stylesheet">
        <!--Toaster Popup message CSS -->
        <!-- <link href="{{URL::asset('/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet"> -->
        <!-- Custom CSS -->
        <link href="{{URL::asset('/css/style.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
        <link href="{{URL::asset('/css/pages/dashboard2.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/css/colors/default-dark.css')}}" id="theme" rel="stylesheet">
        <link href="{{URL::asset('/css/toastr/toastr.min.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/summernote/dist/summernote.css')}}" rel="stylesheet" />
        <link href="{{URL::asset('/css/pages/file-upload.css')}}" rel="stylesheet">
        <link href="{{URL::asset('/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css')}}" rel="stylesheet">
        <style type="text/css">
            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }
            input[type=number] {-moz-appearance: textfield;}

            .displayOverlay{
                position: absolute;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 99;
                background: rgba(255,255,255,0.8);
                font-size: 1.5em;
                text-align: center;
                padding-top: 100px;
            }

            .editableDiv{
                border: 1px solid #ced4da;
                overflow: auto;
                min-height: 100px;
                resize: both;
                width: 70%;
            }

            iframe{
                border: 1px solid lightgray;
            }
        </style>
    </head>
    <body class="fix-header fix-sidebar card-no-border">
        <div class="preloader">
            <div class="loader">
                <div class="loader__figure"></div>
                <p class="loader__label">NXG Charge</p>
            </div>
        </div>
        <div id="main-wrapper">
            @include('layouts.header')
            @include('layouts.sidebar')

            <div class="page-wrapper">
                @yield('content')
                @include('layouts.footer')
            </div>
        </div>
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
        <script src="{{URL::asset('/plugins/moment/moment.js')}}"></script>
        <!--Custom JavaScript -->
        <script src="{{URL::asset('/js/custom.min.js')}}"></script>
        <script src="{{URL::asset('/js/jasny-bootstrap.js')}}"></script>
        <script src="{{URL::asset('/plugins/switchery/dist/switchery.min.js')}}"></script>
        <script src="{{URL::asset('/plugins/styleswitcher/jQuery.style.switcher.js')}}"></script>
        <script src="{{URL::asset('/plugins/summernote/dist/summernote.min.js')}}"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
        <script src="{{URL::asset('/js/jquery.validate.min.js')}}"></script>
        <script src="{{URL::asset('/js/jquery.validate-init.js')}}"></script>
        <script src="{{URL::asset('/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js')}}"></script>

        @stack('scripts')
        <script type="text/javascript">
            $('div.alert').delay(10000).slideUp(500);
            $('.js-switch').each(function() {
                new Switchery($(this)[0], $(this).data());
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#stateFilter').change(function(){
                var state = $(this).val();
                $.ajax({
                    url: "{{route('getCity')}}",
                    type: "POST",
                    data: {state: state},
                    success: function(data){
                        var cities = JSON.parse(data);
                        var options = "<option value='all'>All</option>";
                        for (var i = 0; i < cities.length; i++) {
                            options += "<option value='" + cities[i] + "'>" + cities[i] + "</option>";
                        }
                        $('#cityFilter').html(options);
                        window.location.reload();
                        // $('.preloader').show();
                        // $('#main-wrapper').load(window.location.href);
                    }
                })
            });

            $('#countryFilter').change(function(){
                var country = $(this).val();
                $.ajax({
                    url: "{{route('getState')}}",
                    type: "POST",
                    data: {country: country},
                    success: function(data){
                        var states = JSON.parse(data);
                        var options = "<option value='all'>All</option>";
                        for (var i = 0; i < states.length; i++) {
                            options += "<option value='" + states[i] + "'>" + states[i] + "</option>";
                        }
                        $('#stateFilter').html(options);
                        window.location.reload();
                        // $('.preloader').show();
                        // $('#main-wrapper').load(window.location.href);
                    }
                })
            });

            $('#cityFilter').change(function(){
                var city = $(this).val();
                $.ajax({
                    url: "{{route('setCitySession')}}",
                    type: "POST",
                    data: {city: city},
                    success: function(data){
                        window.location.reload();
                    }
                })
            });

        </script>

<script>
$("#like").click(function () {
$(this).toggleClass("like-open");
});
</script>
    </body>
</html>