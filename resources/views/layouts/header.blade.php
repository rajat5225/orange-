<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{URL::to('/')}}" target="_blank">
                <b><img src="{{URL::asset('/images/logo.png')}}" alt="homepage" title="NXG Charge" class="dark-logo" style="width: 60%;" /></b>
            </a>
        </div>
        <div class="navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                <li class="nav-item"> <a class="nav-link sidebartoggler hidden-sm-down waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>

                <li class="nav-item">
                    <div class="d-flex align-items-center" style="margin: 15px 0;">
                        <label class="mb-0">Country:&nbsp;&nbsp;&nbsp;</label>
                        <select class="form-control col-md-3" name="countryFilter" id="countryFilter">
                            <option value="all" @if(session()->get('globalCountry')=='all') selected @endif>All</option>
                            @foreach(config('statecity.countries') as $country)
                                <option value="{{$country}}" @if(session()->get('globalCountry')==$country) selected @endif>{{$country}}</option>
                            @endforeach
                        </select>

                        <label class="mb-0">&nbsp;&nbsp;&nbsp;State:&nbsp;&nbsp;&nbsp;</label>
                        <select class="form-control col-md-3" name="stateFilter" id="stateFilter">
                            @if(session()->get('globalCountry')!='all')
                                <option value="all" @if(session()->get('globalState')=='all') selected @endif>All</option>
                                @foreach(config('statecity.states') as $state)
                                    <option value="{{$state}}" @if(session()->get('globalState')==$state) selected @endif>{{$state}}</option>
                                @endforeach
                            @else
                                <option value="all">Select Country first</option>
                            @endif
                        </select>

                        <label class="mb-0">&nbsp;&nbsp;&nbsp;City:&nbsp;&nbsp;&nbsp;</label>
                        <select class="form-control col-md-3" name="cityFilter" id="cityFilter">
                            @if(session()->get('globalState')!='all')
                                <option value="all" @if(session()->get('globalCity')=='all') selected @endif>All</option>
                                @foreach(config('statecity.cities') as $city)
                                    <option value="{{$city}}" @if(session()->get('globalCity')==$city) selected @endif>{{$city}}</option>
                                @endforeach
                            @else
                                <option value="all">Select State first</option>
                            @endif
                        </select>
                    </div>
                </li>


            </ul>
            <ul class="navbar-nav my-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{URL::asset('/images/user-gray.png')}}" alt="user" class="profile-pic" /></a>
                    <div class="dropdown-menu dropdown-menu-right animated flipInY">
                        <ul class="dropdown-user">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-img"><img src="{{URL::asset('/images/user-gray.png')}}" alt="user" class="profile-pic" /></div>
                                    <div class="u-text">
                                        <h4>NXG Charge</h4>
                                        <p class="text-muted">Admin</p></div>
                                </div>
                            </li>
                            {{-- <li role="separator" class="divider"></li>
                            <li><a href="#"><i class="ti-user"></i> My Profile</a></li> --}}
                            <li role="separator" class="divider"></li>
                            <li><a href="{{route('logout')}}"><i class="fa fa-power-off"></i> Logout</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
