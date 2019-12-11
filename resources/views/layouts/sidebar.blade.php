<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
               {{--  <li class="user-profile">
                    <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><span>NXG Charge</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="#">My Profile </a></li>
                        <li><a href="{{route('logout')}}">Logout</a></li>
                    </ul>
                </li> --}}
                {{-- <li class="nav-devider"></li> --}}
                <li class="nav-small-cap">HOME</li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('index')}}"><i class="mdi mdi-gauge"></i><span class="hide-menu">Dashboard</a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('users')}}"><i class="fa fa-user"></i><span class="hide-menu">Users</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('drivers')}}"><i class="fa fa-user-o"></i><span class="hide-menu">Drivers</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('cmsPages')}}"><i class="fa fa-outdent"></i><span class="hide-menu">CMS Pages</span></a>
                </li>
                 <li>
                    <a class="waves-effect waves-dark" href="{{route('vehicles')}}"><i class="fa fa-truck"></i><span class="hide-menu">Vehicles</span></a>
                </li>
				<li>
                    <a class="waves-effect waves-dark" href="{{route('cabs')}}"><i class="fa fa-truck"></i><span class="hide-menu">Cabs</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('rides')}}"><i class="fa fa-car"></i><span class="hide-menu">Rides</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('rideSupportRequests')}}"><i class="fa fa-cog"></i><span class="hide-menu">Ride Support</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('transactions')}}"><i class="fa fa-credit-card"></i><span class="hide-menu">Transactions</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('notifications')}}"><i class="fa fa-bell"></i><span class="hide-menu">Notifications</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('couponCodes')}}"><i class="fa fa-percent"></i><span class="hide-menu">Coupon Code</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('ratings')}}"><i class="fa fa-star-o"></i><span class="hide-menu">Ratings</span></a>
                </li> 
                <li>
                    <a class="waves-effect waves-dark" href="{{route('countries')}}"><i class="fa fa-flag"></i><span class="hide-menu">Countries</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('states')}}"><i class="fa fa-flag"></i><span class="hide-menu">States</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('cities')}}"><i class="fa fa-flag"></i><span class="hide-menu">Cities</span></a>
                </li>
                <li>
                    <a class="waves-effect waves-dark" href="{{route('setting')}}"><i class="fa fa-gear"></i><span class="hide-menu">Settings</span></a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>