@extends('layouts.app')
@section('title', ucfirst($type).' Coupon Code')

@section('content')
	<div class="container-fluid">
		@if(session()->get('globalState') == 'all' && $type == 'add')
			<div class="displayOverlay">
				<p>Select State and City for adding Coupon Code in a specific City.</p>
				<p><span class="middleKeyword">OR</span></p>
				<p>Select State to add Coupon Code in all Cities of a specific State.</p>
			</div>
		@endif

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Coupon Code</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('couponCodes')}}">Coupon Codes</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Coupon Code</li>
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
                            <h4 class="card-title">Fill In Code Details</h4>
                        @elseif($type == 'edit')
                            <h4 class="card-title">Edit Code Details</h4>
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

                        <form class="form-material m-t-40 row form-valide" method="post" action="{{$url}}">

                        	{{csrf_field()}}
                            <div class="form-group col-md-6 m-t-20">
                            	<label>Coupon Code</label>
                                <input type="text" class="form-control form-control-line" placeholder="Enter Coupon Code" name="val-code" value="{{old('val-code', $code->coupon_code)}}">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                            	<label>Title</label>
                                <input type="text" class="form-control form-control-line" placeholder="Enter Title" name="val-title" value="{{old('val-title', $code->title)}}">
                            </div>
                            <div class="form-group col-md-12 m-t-20">
                            	<label>Description</label>
                                <textarea name="val-description" class="form-control" placeholder="Enter Code Description" value="{{old('val-description', $code->description)}}" rows="5">{{old('val-description', $code->description)}}</textarea>
                            </div>
                            <div class="form-group col-md-12 m-t-20">
                            	<label>Terms Of Use</label>
                                <div class="click2edit">
                                    {!! old('val-couponTerms', $code->terms) !!}
                                </div>
                                <input type="hidden" name="val-couponTerms" value="{{old('val-couponTerms', $code->terms)}}">
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                                <label>Minimum Amount (in {{session()->get('currency')}})</label>
                                <input type="text" name="val-amt" class="form-control decimalInput mtg_deci" placeholder="Enter Minimum Amount" value="{{old('val-amt', $code->min_amount)}}" required>
                            </div>
                            <div class="form-group col-md-4 m-t-20 val-maxamt">
                                <label>Maximum Amount (in {{session()->get('currency')}})</label>
                                <input type="text" name="val-maxamt" class="form-control decimalInput mtg_deci" placeholder="Enter Maximum Amount" value="{{old('val-amt', $code->max_amount)}}">
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                                <label>Discount Type</label>
                                <select class="form-control" name="val-amounttype" id="amountType">
                                    @foreach(config('constants.AMOUNT_TYPE') as $key => $dtype)
                                        <option value="{{$key}}" @if(old('val-amounttype', $code->amount_type)==$key) selected @endif>{{$dtype}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                            	<label>Coupon Type</label>
                                <select class="form-control" name="val-type" id="discountType">
                                    @foreach(config('constants.DISCOUNT_TYPE') as $key => $dtype)
                                        <option value="{{$key}}" @if(old('val-type', $code->discount_type)==$key) selected @endif>{{$dtype}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                            	<label>Discount Value (in <span class="valueType">%</span>)</label>
                                <input type="text" class="form-control decimalInput mtg_deci" name="val-discount" placeholder="Enter Discount Value" value="{{old('val-discount', $code->discount_value)}}">
                            </div>
                            <div class="form-group col-md-4 m-t-20" id="rideNum" style="display: none">
                                <label>No. of Rides</label>
                                <input type="text" class="form-control mtg_number" name="val-rides" placeholder="Enter No. of Rides" value="{{old('val-rides', $code->no_of_rides)}}">
                            </div>
                            <div class="form-group col-md-4 m-t-20 val-applies">
                                <label>No. of Applies</label>
                                <input type="text" class="form-control mtg_number" name="val-applies" placeholder="Enter maximum no. of usage" value="{{old('val-applies', $code->no_of_applies)}}">
                            </div>
                            <div class="form-group col-md-4 m-t-20" id="minRide" style="display: none">
                                <label>Minimum No. of Rides for Usage</label>
                                <input type="text" class="form-control mtg_number" name="val-minRides" placeholder="Enter minimum no. of rides to use this coupon" title="Enter minimum no. of rides to use this coupon" value="{{old('val-minRides', $code->min_rides)}}">
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                            	<label>Start Date</label>
                                <input type="text" class="form-control" name="val-startDate" placeholder="Select Start Date" id="start-date" value="{{old('val-startDate', $code->start_date)}}" required>
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                                <label>End Date</label>
                                <input type="text" class="form-control" name="val-endDate" placeholder="Select End Date" id="end-date" value="{{old('val-endDate', $code->end_date)}}" required>
                            </div>
                            <div class="form-group col-md-4 m-t-20">
                            	<label class="col-md-4">Status</label>
                                <input type="checkbox" @if(isset($code->id)) @if($code->status == 'AC') checked @endif @else checked @endif class="js-switch" data-color="#f62d51" data-size="small" name="val-status"/>
                            </div>
                            @if($type == 'edit')
                                <div class="form-group col-md-4 m-t-20">
                                    <label>Country</label>
                                    <select class="form-control" name="val-country" id="codeCountry">
                                        @foreach(config('statecity.countries') as $country)
                                            <option value="{{$country}}" @if($code->country==$country) selected @endif>{{$country}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 m-t-20">
                                    <label>State</label>
                                    <select class="form-control" name="val-state" id="codeState">
                                        @foreach(config('statecity.states') as $state)
                                            <option value="{{$state}}" @if($code->state==$state) selected @endif>{{$state}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 m-t-20">
                                    <label>City</label>
                                    <select class="form-control" name="city" id="codeCity">
                                        <option value='all'>All</option>
                                        @foreach($cities as $city)
                                            <option value="{{$city}}" @if($code->city==$city) selected @endif>{{$city}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
							<div class="col-12 m-t-20">
	                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Save</button>
	                            <a href="{{route('couponCodes')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
$(document).on('keypress',".mtg_number",function (e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});
 $(document).on('keypress',".mtg_deci",function (e) {
     if ((e.which != 46 || $(this).val().indexOf('.') != -1) && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});

		$(function(){
            if($('#discountType').val() == 'rides')
            {
                $('#minRide').hide();
                $('#rideNum').show();
                // $('.val-applies').hide();
                $('input[name="val-applies"]').prop('readonly', true);
                $('input[name="val-applies"]').val("1");
                $('input[name="val-applies"]').rules('remove', "required");
                $('input[name="val-rides"]').rules('add', "required");
                $('#rideNum input').rules('add', 'required');
                $('input[name="val-discount"]').rules('remove', "max");
                $('#val-discount-error').remove();
                // $('.valueType').text('{{session()->get("currency")}}');
            }
            else if($('#discountType').val() == 'usage')
            {
                $('#rideNum').hide();
                $('#minRide').show();
                // $('.val-applies').show();
                $('input[name="val-applies"]').prop('readonly', false);
                $('input[name="val-applies"]').val();
                $('#minRide input').rules('add', 'required');
                $('input[name="val-rides"]').rules('remove', "required");
                $('input[name="val-applies"]').rules('add', "required");
                $('input[name="val-discount"]').rules('remove', "max");
                $('#val-discount-error').remove();
                // $('.valueType').text('{{session()->get("currency")}}');

            }
            else
            {
                $('#rideNum').hide();
                $('#minRide').hide();
                // $('.val-applies').show();
                $('input[name="val-applies"]').prop('readonly', false);
                $('input[name="val-applies"]').val();
                $('input[name="val-rides"]').rules('remove', "required");
                $('input[name="val-applies"]').rules('add', "required");
                $('#rideNum input').rules('remove', 'required');
                $('#minRide input').rules('remove', 'required');
                $('.valueType').text('%');

                if($('#amountType').val() == 'percent')
                {
                    $('.val-maxamt').show();
                    $('input[name="val-maxamt"]').rules('add', 'required');
                    $('input[name="val-discount"]').rules('add', {max: 100});
                    (!$('#val-discount-error').is(':visible') && $('input[name="val-discount"]').val() > 100) ? $('input[name="val-discount"]').closest('.form-group').append('<div id="val-discount-error" class="invalid-feedback animated fadeInDown" style="display: block;">Please enter a value less than or equal to 100.</div>') : $('#val-discount-error').show();
                }
                else
                {
                    $('.val-maxamt').hide();
                    $('input[name="val-maxamt"]').rules('remove', "required");
                    $('input[name="val-discount"]').rules('remove', "max");
                    $('#val-discount-error').remove();
                    $('.valueType').text('{{session()->get("currency")}}');

                }

            }

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

            $('.click2edit').summernote({
                minHeight: '100px',
                callbacks: {
                    onChange: function(contents, $editable) {
                        console.log(contents);
                        $('input[name="val-couponTerms"]').val(contents);
                    }
                }
            });

            $('#end-date').bootstrapMaterialDatePicker({ format: 'YYYY-MM-DD', time: false, minDate: new Date() });
            $('#start-date').bootstrapMaterialDatePicker({ format: 'YYYY-MM-DD', time: false, minDate: new Date() }).on('change', function(e, date){
                $('#end-date').bootstrapMaterialDatePicker('setMinDate', date);
            });

            $('#amountType').change(function(){
                if($(this).val() == 'percent')
                {
                    $('.val-maxamt').show();
                    $('input[name="val-maxamt"]').rules('add', 'required');
                    $('input[name="val-discount"]').rules('add', {max: 100});
                    (!$('#val-discount-error').is(':visible') && $('input[name="val-discount"]').val() > 100) ? $('input[name="val-discount"]').closest('.form-group').append('<div id="val-discount-error" class="invalid-feedback animated fadeInDown" style="display: block;">Please enter a value less than or equal to 100.</div>') : $('#val-discount-error').show();
                    $('.valueType').text('%');
                }
                else
                {
                    $('.val-maxamt').hide();
                    $('input[name="val-maxamt"]').rules('remove', "required");
                    $('input[name="val-discount"]').rules('remove', "max");
                    $('#val-discount-error').remove();
                    $('.valueType').text('{{session()->get("currency")}}');

                }
            });
            $('#discountType').change(function(){
                if($(this).val() == 'rides')
                {
                    // $('.val-applies').hide();
                    $('#minRide').hide();
                    $('#rideNum').show();
                    $('input[name="val-applies"]').prop('readonly', true);
                    $('input[name="val-applies"]').val("1");
                    $('input[name="val-rides"]').rules('add', "required");
                    $('input[name="val-applies"]').rules('remove', "required");
                    $('#rideNum').rules('add', 'required');
                    $('input[name="val-discount"]').rules('remove', "max");
                    $('#val-discount-error').remove();
                    // $('.valueType').text('{{session()->get("currency")}}');

                }
                else if($(this).val() == 'usage')
                {
                    // $('.val-applies').show();
                    $('#rideNum').hide();
                    $('#minRide').show();
                    $('input[name="val-applies"]').prop('readonly', false);
                    $('input[name="val-applies"]').val();
                    $('input[name="val-rides"]').rules('remove', "required");
                    $('input[name="val-applies"]').rules('add', "required");
                    $('#minRide').rules('add', 'required');
                    $('input[name="val-discount"]').rules('remove', "max");
                    $('#val-discount-error').remove();
                    // $('.valueType').text('{{session()->get("currency")}}');

                }
                else
                {
                    // $('.val-applies').show();
                    $('input[name="val-applies"]').prop('readonly', false);
                    $('input[name="val-applies"]').val();
                    $('input[name="val-rides"]').rules('remove', "required");
                    $('input[name="val-applies"]').rules('add', "required");
                    $('#rideNum').hide();
                    $('#minRide').hide();
                    $('#rideNum').rules('remove', 'required');
                    $('#minRide').rules('remove', 'required');
                    $('.valueType').text('%');


                    if($('#amountType').val() == 'percent')
                    {
                        $('input[name="val-maxamt"]').rules('add', "required");
                        $('input[name="val-discount"]').rules('add', {max: 100});
                        (!$('#val-discount-error').is(':visible') && $('input[name="val-discount"]').val() > 100) ? $('input[name="val-discount"]').closest('.form-group').append('<div id="val-discount-error" class="invalid-feedback animated fadeInDown" style="display: block;">Please enter a value less than or equal to 100.</div>') : $('#val-discount-error').show();
                    }
                    else
                    {
                        $('input[name="val-maxamt"]').rules('remove', "required");
                        $('input[name="val-discount"]').rules('remove', "max");
                        $('#val-discount-error').remove();
                        $('.valueType').text('{{session()->get("currency")}}');

                    }
                }
            });

            $('#codeState').change(function(){
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
                        $('#codeCity').html(options);
                    }
                })
            });
            $('#codeCountry').change(function(){
                var country = $(this).val();
                $.ajax({
                    url: "{{route('getCity')}}",
                    type: "POST",
                    data: {country: country},
                    success: function(data){
                        var states = JSON.parse(data);
                        var options = "<option value='all'>All</option>";
                        for (var i = 0; i < states.length; i++) {
                            options += "<option value='" + states[i] + "'>" + states[i] + "</option>";
                        }
                        $('#codeState').html(options);
                    }
                })
            });
        });
	</script>
@endpush