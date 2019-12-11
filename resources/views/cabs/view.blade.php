@extends('layouts.app')
@section('title', 'Cab - ' . $vehicle->cab_type)

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Cab - {{$vehicle->cab_type}} <a href="{{route('editCab', ['id'=>$vehicle->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a></h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cabs')}}">Cabs</a></li>
                    <li class="breadcrumb-item active">Cab - {{$vehicle->cab_type}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-xlg-3 col-md-5">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <center class="m-t-30"> <img src="@if($vehicle->image != null && file_exists(public_path('/uploads/vehicles/'.$vehicle->image))){{URL::asset('/uploads/vehicles/'.$vehicle->image)}} @else {{URL::asset('/images/user-gray.png')}} @endif" class="img-circle" width="150" />
                            <h4 class="card-title m-t-10">{{$vehicle->cab_type}}</h4>
                        </center>
                    </div>
                    <div>
                        <hr> </div>
                </div>
            </div>
            <div class="col-lg-6 col-xlg-3 col-md-5">
                <div class="card">
                 
                    @include('layouts.message')
                    <div class="card-body">
                        <center class="m-t-30"> <img src="@if($vehicle->aerial_image != null && file_exists(public_path('/uploads/vehicles/aerial/'.$vehicle->aerial_image))){{URL::asset('/uploads/vehicles/aerial/'.$vehicle->aerial_image)}} @else {{URL::asset('/images/user-gray.png')}} @endif" class="img-circle" width="150" />
                            <h4 class="card-title m-t-10">{{$vehicle->cab_type}}</h4>
                        </center>
                    </div>
                    <div>
                        <hr> </div>
                </div>
            </div>
            

            {{--  <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#details" role="tab">Details</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="details" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <small class="text-muted p-t-30 db">Capacity</small><h5>{{$vehicle->capacity}}</h5>
                                    <small class="text-muted p-t-30 db">Price (in {{session()->get('currency')}})</small><h5>{{$vehicle->price}}</h5>
                                    <small class="text-muted p-t-30 db">Base Fare (in {{session()->get('currency')}})</small><h5>{{$vehicle->base_fare}}</h5>
                                    <small class="text-muted p-t-30 db">Driver Charge (in %)</small><h5>{{$vehicle->driver_charge}}</h5>
                                    {{-- <small class="text-muted p-t-30 db">Distance Time (in sec/km)</small><h5>{{$vehicle->distance_time}}</h5> --}}
                                    {{--  <small class="text-muted p-t-30 db">Waiting Charge (in {{session()->get('currency')}})</small><h5>{{$vehicle->waiting_charge}}</h5>  --}}
                                   {{--  <small class="text-muted p-t-30 db">Cancellation Charge (in {{session()->get('currency')}})</small><h5>{{$vehicle->cancellation_charge}}</h5> --}}
                                {{--  </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    --}}
        </div>
    </div>
@endsection

@push('scripts')

@endpush
