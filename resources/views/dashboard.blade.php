@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
	<div class="container-fluid">
        <!-- <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Dashboard</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div> -->
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex no-block">
                            <div class="m-r-20 align-self-center"><span class="lstick m-r-20"></span><img src="{{URL::asset('/images/icon/income.png')}}" alt="Income" /></div>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">Total Rides</h6>
                                <h2 class="m-t-0">{{$booking}}</h2></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex no-block">
                            <div class="m-r-20 align-self-center"><span class="lstick m-r-20"></span><img src="{{URL::asset('/images/icon/staff.png')}}" alt="Income" /></div>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">Total Driver</h6>
                                <h2 class="m-t-0">{{$driver}}</h2></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex no-block">
                            <div class="m-r-20 align-self-center"><span class="lstick m-r-20"></span><img src="{{URL::asset('/images/icon/staff.png')}}" alt="Income" /></div>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">Total Users</h6>
                                <h2 class="m-t-0">{{$user}}</h2></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex no-block">
                            <div class="m-r-20 align-self-center"><span class="lstick m-r-20"></span><img src="{{URL::asset('/images/icon/staff.png')}}" alt="Income" /></div>
                            <div class="align-self-center">
                                <h6 class="text-muted m-t-10 m-b-0">Ride Shared</h6>
                                <h2 class="m-t-0">{{$shareRide}}</h2></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-5 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div>
                                <h4 class="card-title"><span class="lstick"></span>Recently Registered Drivers</h4></div>
                        </div>
                        <div class="table-responsive m-t-20">
                            @if($driverList->count()>0)
                                <table class="table vm no-th-brd no-wrap pro-of-month">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile Number</th>
                                            {{-- <th>Email Address</th> --}}
                                            <th>State</th>
                                            <th>City</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach($driverList as $item)
                                            <tr>
                                                <td>{{ucfirst($item->name)}}</td>
                                                <td>{{$item->mobile_number}}</td>
                                                {{-- <td>{{$item->email}}</td> --}}
                                                <td>{{ucfirst($item->state)}}</td>
                                                <td>{{ucfirst($item->city)}}</td>
                                                {{-- <td>{{route()}}</td> --}}
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if($driverList->count()==0)
                                <h4>No record found</h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div>
                                <h4 class="card-title"><span class="lstick"></span>Top 10 Driver's Total Finished Bookings</h4></div>
                        </div>
                        <div class="table-responsive m-t-20">
                            @if($driverBooking->count()>0)
                                <table class="table vm no-th-brd no-wrap pro-of-month">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile Number</th>
                                            {{-- <th>Email Address</th> --}}
                                            <th>Total Bookings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($driverBooking as $item)
                                        <tr>
                                            <td>{{ucfirst($item->name)}}</td>
                                            <td>{{$item->mobile_number}}</td>
                                            {{-- <td>{{$item->email}}</td> --}}
                                            <td>{{ucfirst($item->total_bookings)}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if($driverBooking->count()==0)
                                <h4>No record found</h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><span class="lstick"></span>Ride Status</h4>
                        <div id="rides" style="height:290px; width:100%;"></div>
                        <table class="table vm font-14">
                            <tr>
                                <td class="b-0">Finished Rides</td>
                                <td class="text-right font-medium b-0">{{$finished}}</td>
                            </tr>
                            <tr>
                                <td>Cancelled Rides</td>
                                <td class="text-right font-medium">{{$cancelled}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
       
    </div>
@endsection
@push('scripts')
	<script src="{{URL::asset('/plugins/chartist-js/dist/chartist.min.js')}}"></script>
    <script src="{{URL::asset('/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js')}}"></script>
    <!--c3 JavaScript -->
    <script src="{{URL::asset('/plugins/d3/d3.min.js')}}"></script>
    <script src="{{URL::asset('/plugins/c3-master/c3.min.js')}}"></script>
    <!-- Chart JS -->
    <script src="{{URL::asset('/js/dashboard2.js')}}"></script>
    <script>
          var chart = c3.generate({
        bindto: '#rides',
        data: {
            columns: [
                ['Cancelled', {{$cancelled}}],
                ['Finished', {{$finished}}],
            ],
            
            type : 'donut',
            onclick: function (d, i) { console.log("onclick", d, i); },
            onmouseover: function (d, i) { console.log("onmouseover", d, i); },
            onmouseout: function (d, i) { console.log("onmouseout", d, i); }
        },
        donut: {
            label: {
                show: false
              },
            title:"Rides",
            width:20,
            
        },
        
        legend: {
          hide: true
          //or hide: 'data1'
          //or hide: ['data1', 'data2']
        },
        color: {
              pattern: [ 'rgb(234, 61, 34)', 'rgb(50, 141, 16)','#1e88e5']
        }
    });
    </script>
@endpush