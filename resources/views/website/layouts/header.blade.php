<header class="header wrapper bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-3 col-sm-4 col-4">
                <div class="logo">
                    <a href="{{route('home')}}">
                        <img src="images/logo.png" alt="tekyslab" title="NXG Charge">
                    </a>
                </div>
            </div>
                <div class="col-lg-9 col-md-9 col-sm-8 col-8">
                    <a class="toggle d-block d-sm-none text-right" id="like" href="#">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </a>
                <div class=" mob-toggle d-flex align-items-center justify-content-end">
                    <div class="navlinks d-flex justify-content-end align-items-center">
                        <a href="{{route('about')}}" class="txt-upr clr-black font14">About</a>
                        <a href="{{route('mobileVerify')}}" class="button btn-black">Become a driver</a>
                    </div>
                    <div class="ftrsocial ml-3">
                        <ul class="">
                            <li>
                                <a href="{{$social[1]->rule_value}}" class="font15" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            </li>
                            <li>
                                <a href="{{$social[0]->rule_value}}"" class="font15" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                            </li>
                            <li>
                                <a href="{{$social[2]->rule_value}}"" class="font15" target="_blank"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
                            </li>
                            <li>
                                <a href="{{$social[3]->rule_value}}"" class="font15" target="_blank"><i class="fa fa-instagram"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>