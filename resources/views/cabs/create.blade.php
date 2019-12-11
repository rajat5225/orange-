@extends('layouts.app')
@section('title', ucfirst($type).' Cab')

@section('content')
	<div class="container-fluid">


        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($type)}} Cab</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cabs')}}">Cabs</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Cab</li>
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
                            <h4 class="card-title">Fill In Cab Details</h4>
                        @elseif($type == 'edit')
                            <h4 class="card-title">Edit Cab Details</h4>
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

                        <form class="form-material m-t-40 row " method="post" action="{{$url}}" enctype="multipart/form-data">

                        	{{csrf_field()}}
                            <div class="form-group col-md-12 m-t-20">
                            	<label>Cab Name</label>
                                <input type="text" class="form-control form-control-line" placeholder="Enter Cab Name" name="val-name" value="{{old('val-name', $vehicle->cab_type)}}">
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                            	<label>Cab Image</label>
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
                            </div>
                            <div class="form-group col-md-6 m-t-20">
                            	<label>Cab Aerial Image</label>
                                <input type="hidden" name="aimage_exists" id="aimage_exists" value="1">

                                @if($type == 'add' || ($type == 'edit' && $vehicle->aerial_image == null))
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span>
                                        <input type="file" name="val-arialimage"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                @elseif($type == 'edit')
                                    <br>
                                    <div id="vehicleImage1">

                                        <img src="@if($vehicle->aerial_image != null && file_exists(public_path('/uploads/vehicles/aerial/'.$vehicle->aerial_image))){{URL::asset('/uploads/vehicles/aerial/'.$vehicle->aerial_image)}}@endif" class="img-circle" width="150"" />
                                        &nbsp;&nbsp;&nbsp;<a id="changeImage1" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete">Change</a>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group col-md-4 m-t-20">
                            	<label class="col-md-4">Status</label>
                                <input type="checkbox" @if($type == 'edit') @if(isset($vehicle) && $vehicle->status == 'AC') checked @endif @else checked @endif class="js-switch" data-color="rgb(26, 180, 27)" data-size="small" name="val-status"/>
                            </div>

							<div class="col-12 m-t-20">
	                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-10">Save</button>
	                            <a href="{{route('cabs')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
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
            $('#changeImage1').click(function(){
                $('#vehicleImage1').parent().append('<div class="fileinput fileinput-new input-group" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .gif, .svg)</span> <span class="fileinput-exists">Change</span><input type="file" name="val-arialimage"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#vehicleImage1').remove();
                $('#aimage_exists').val(0);
            });

        });
	</script>
@endpush