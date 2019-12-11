@extends('layouts.app')
@section('title', ucfirst($type).' Vehicle')

@section('content')
	<div class="container-fluid">
		@if(session()->get('globalState') == 'all' && $type == 'add')
			<div class="displayOverlay">
				<p>Select State and City for adding Vehicle in a specific City.</p>
				<p><span class="middleKeyword">OR</span></p>
				<p>Select State to add vehicle in all Cities of a specific State.</p>
			</div>
		@endif

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Vehicle</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('vehicles')}}">Vehicles</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Vehicle</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">

                        @if($type == 'add')
                            <h4 class="card-title">Fill In Vehicle Details</h4>
                        @elseif($type == 'edit')
                            <h4 class="card-title">Edit Vehicle Details</h4>
                        @endif

                        @if ($errors->any())
						    <div class="alert alert-danger">
						        <ul>
						            @foreach ($errors->all() as $error)
						                <li>{{ $error }}</li>
						            @endforeach
						        </ul>
						    </div>
						@endif

                        <form class="form-material m-t-40 row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                            {{csrf_field()}}
                            <div class="form-group col-md-4 m-t-20">
                                <label>All Vehicles</label>
                                <select class="form-control" name="cab_type" required>
                                    <option value=''>Select Vehicles</option>

                                     @foreach($cabs as $cab)
                                            <option value="{{$cab->id}}" @if($cab->cab_type==$vehicle->vehicle_type) selected @endif>{{$cab->cab_type}}</option>
                                        @endforeach

                                </select>
                            </div>
                            {{--  <div class="form-group col-md-6 m-t-20">
                            	<label>Vehicle Name</label>
                                <input type="text" class="form-control form-control-line" placeholder="Enter Vehicle Name" name="val-name" value="{{old('val-name', $vehicle->vehicle_type)}}">
                            </div>  --}}
                            {{--  <div class="form-group col-md-6 m-t-20">
                            	<label>Vehicle Image</label>
                                <input type="hidden" name="image_exists" id="image_exists" value="1">

                                @if($type == 'add' || ($type == 'edit' && $vehicle->image == null))
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                        <input type="file" name="val-image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                @elseif($type == 'edit')
                                    <br>
                                    <div id="vehicleImage">

                                        <img src="@if($vehicle->image != null && file_exists(public_path('/uploads/vehicles/'.$vehicle->image))){{URL::asset('/uploads/vehicles/'.$vehicle->image)}}@endif" class="img-circle" width="150"" />
                                        &nbsp;&nbsp;&nbsp;<a id="changeImage" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                    </div>
                                @endif
                            </div>  --}}
                            <div class="form-group col-md-4 m-t-20">
                            	<label>Capacity</label>
                                <input type="number" name="val-capacity" class="form-control" placeholder="No. of persons accommodated" value="{{old('val-capacity', $vehicle->capacity)}}">
                            </div>
							 <div class="form-group col-md-4 m-t-20">
                            	<label>Base Fare (in {{session()->get('currency')}})</label>
                                <input type="text" class="form-control form-control-line decimalInput" name="val-basefare" placeholder="Enter Base Fare" value="{{old('val-basefare', $vehicle->base_fare)}}">
                            </div>


                            <div class="form-group col-md-4 m-t-20">
                            	<label>Price (in {{session()->get('currency')}})</label>
                                <input type="text" class="form-control form-control-line decimalInput" name="val-price" placeholder="Enter Price" value="{{old('val-price', $vehicle->price)}}">
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                            	<label>Driver Charge (in %)</label>
                                <input type="text" name="val-driver" class="form-control decimalInput" placeholder="Enter Driver Charge" value="{{old('val-driver', $vehicle->driver_charge)}}">
                            </div>
                            {{-- <div class="form-group col-md-4 m-t-20">
                            	<label>Distance Time</label>
                                <input type="text" class="form-control" name="val-distance" placeholder="Enter Distance Time" value="{{old('val-distance', $vehicle->distance_time)}}">
                            </div> --}}
                            <div class="form-group col-md-4 m-t-20">
                            	<label>Waiting Charge (in {{session()->get('currency')}})</label>
                                <input type="text" class="form-control decimalInput" name="val-waiting" placeholder="Enter Waiting Charge" value="{{old('val-waiting', $vehicle->waiting_charge)}}">
                            </div>
                            {{-- <div class="form-group col-md-4 m-t-20">
                            	<label>Cancellation Charge (in {{session()->get('currency')}})</label>
                                <input type="text" class="form-control decimalInput" name="val-cancel" placeholder="Enter Cancellation Charge" value="{{old('val-cancel', $vehicle->cancellation_charge)}}">
                            </div> --}}
                            <div class="form-group col-md-4 m-t-20">
                            	<label class="col-md-4">Status</label>
                                <input type="checkbox" @if($type == 'edit') @if(isset($vehicle) && $vehicle->status == 'AC') checked @endif @else checked @endif class="js-switch" data-color="rgb(26, 180, 27)" data-size="small" name="val-status"/>
                            </div>
                            @if($type == 'edit')
                                <div class="form-group col-md-4 m-t-20">
                                    <label>Country</label>
                                    <select class="form-control" name="val-country" id="vehicleCountry">
                                        @foreach(config('statecity.countries') as $country)
                                            <option value="{{$country}}" @if($vehicleCountry==$country) selected @endif>{{$country}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 m-t-20">
                                    <label>State</label>
                                    <select class="form-control" name="val-state" id="vehicleState">
                                        @foreach($states as $state)
                                            <option value="{{$state}}" @if($vehicleState==$state) selected @endif>{{$state}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 m-t-20">
                                    <label>City</label>
                                    <select class="form-control" name="city" id="vehicleCity">
                                        <option value='all'>All</option>
										{{--  @if(is_array($cities))  --}}
                                        @foreach($cities as $city)
                                            <option value="{{$city}}" @if($vehicle->city==$city) selected @endif>{{$city}}</option>
                                        @endforeach
										{{--  @endif  --}}
                                    </select>
                                </div>
                            @else
                                <div class="form-group col-md-4 m-t-20">
                                    <label>Country</label>
                                    <select class="form-control" name="val-country" id="vehicleCountry">
                                        <option value=''>Select Country</option>
                                        @foreach(config('statecity.countries') as $country)
                                            <option value="{{$country}}" @if($country==Session::get('globalCountry')) selected @endif>{{$country}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 m-t-20">
                                    <label>State</label>
                                    <select class="form-control" name="val-state" id="vehicleState" required>
                                        <option value=''>Select State</option>
                                        @foreach(config('statecity.states') as $state)
                                            <option value="{{$state}}" @if($state==Session::get('globalState')) selected @endif>{{$state}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 m-t-20">
                                    <label>City</label>
                                    <select class="form-control" name="city[]" id="vehicleCity" multiple required style="height: 250px">
                                        <option value=''>Select City</option>

                                        {{-- <option value='all'>All</option> --}}
                                         @if($cities)
                                            @foreach($cities as $city)
                                                <option value="{{$city}}" @if($vehicle->city==$city) selected @endif>{{$city}}</option>
                                            @endforeach
                                         @endif
                                    </select>
                                </div>
                            @endif
							<div class="col-12 m-t-20">
	                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Save</button>
	                            <a href="{{route('vehicles')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
	                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
	<script type="text/javascript">
		$(function(){
			$(".decimalInput").on('keypress',function (e) {
				//if the letter is not digit then display error and don't type anything
				if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
					if(e.which != 46)
						return false;
				}
			});

			$("input[type=number]").on('keypress',function (e) {
				//if the letter is not digit then display error and don't type anything
				if(e.which == 46)
					return false;
			});

            /*$('#deleteImage').click(function(){
                $.ajax({
                    url: "{{route('imgdeleteVehicle', ['id'=>$vehicle->id])}}",
                    type: "POST",
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            $(".fileinput").show();
                            $('#vehicleImage').remove();
                            toastr.success(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                        else
                        {
                            toastr.error(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                    }
                });
            });*/

            $('#changeImage').click(function(){
                $('#vehicleImage').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="val-image"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#vehicleImage').remove();
                $('#image_exists').val(0);
            });

            $('#vehicleState').change(function(){
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
                        $('#vehicleCity').html(options);
                    }
                })
            });
            $('#vehicleCountry').change(function(){
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
                        $('#vehicleState').html(options);
                    }
                })
            });
        });
	</script>
@endpush