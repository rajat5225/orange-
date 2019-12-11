@extends('layouts.app')
@section('title', ucfirst($user->user_role[0]->role).' - ' . $user->name)

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($user->user_role[0]->role)}} Profile</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route($user->user_role[0]->role.'s')}}">{{ucfirst($user->user_role[0]->role)}}s</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($user->user_role[0]->role)}} Profile</li>
                </ol>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                	@include('layouts.message')
                    <div class="card-body">
                        <center class="m-t-30"> <img src="@if($user->profile_picture != null){{URL::asset('/uploads/profiles/'.$user->profile_picture)}} @else {{URL::asset('/images/user-gray.png')}} @endif" class="img-circle" width="150" height="150" />
                            <h4 class="card-title m-t-10">{{$user->name}}</h4>
                            <small class="text-muted">Average Ratings </small>
                        <h6>{{$total_complements->total_ratings}}</h6>
                        </center>
                    </div>
                    <div>
                        <hr> </div>
                    <div class="card-body"> <small class="text-muted">Email address </small>
                        <h6>{{$user->email}}</h6> <small class="text-muted p-t-30 db">Phone</small>
                        <h6>+{{$city->state->country->phonecode}} - {{$user->mobile_number}}</h6> <small class="text-muted p-t-30 db">Location</small>
                        <h6>{{$user->city}}, {{$user->state}}, {{$city->state->country->name}}</h6>
                        <small class="text-muted">Total Rides </small>
                        <h6>{{$driver_booking->total_rides}}</h6>
                        <small class="text-muted">Total Earnings </small>
                        <h6>{{$driver_booking->total_earning}}</h6>
                        <small class="text-muted">Total Complements </small>
                        <h6>{{$total_complements->total_complements}}</h6>

                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                      
                        @if($user->user_role[0]->role == 'driver')
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#Complements" role="tab">Complements</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#Earnings" role="tab">Earnings</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#Rides" role="tab">Rides</a> </li>
                          
                          
                        @endif
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="Complements" role="tabpanel">
                            <div class="card-body">
                                    @foreach ($all_compliments as $complement)
                                    <div class="row">
                                        <div class="col-md-3 col-xs-6 b-r"> <strong> Name</strong>
                                            <br>
                                            <p class="text-muted">{{$complement->name}}</p>
                                        </div>
                                        <div class="col-md-3 col-xs-6 b-r"> <strong>Booking id</strong>
                                            <br>
                                            <p class="text-muted">{{$complement->booking_code}}
                                        </div>
                                        <div class="col-md-3 col-xs-6 b-r"> <strong>Date</strong>
                                            <br>
                                            <p class="text-muted">{{$complement->date}}</p>
                                        </div>
                                       <div class="col-md-3 col-xs-6"> <strong>Image</strong>
                                            <br>
                                          <img src="{{$complement->image}}" height="50" width="50">
                                        </div>  
                                    </div>
                                    @endforeach 
                              
                            </div>
                        </div>
                        <div class="tab-pane" id="Earnings" role="tabpanel">
                            <div class="card-body">
                              <table class="table color-table muted-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Cash</th>
                                            <th>Wallet</th>
                                            <th>Total Earning</th>
                                            <th>Admin Total Earning</th> 
                                            <th>Action</th> 
                                        </tr>
                                    </thead>
                                <tbody>
                            @foreach ($earning as $item) 
                                 <tr>
                                    <td> 
                                        <p class="text-muted">{{$item->date}}</p>
                                    </td>
                                    <td> 
                                        <p class="text-muted">{{$item->cash_total}}</p>
                                    </td>
                                    <td> 
                                        <p class="text-muted">{{$item->wallet_total}}</p>
                                    </td>
                                    <td> 
                                        <p class="text-muted">{{$item->total_earning}}</p>
                                    </td>
                                    <td>
                                        <p class="text-muted">{{$item->admin_total}}</p>
                                    </td>
                                    <td>
                                            <a href="" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Info Detail"><i class="fa fa-eye"></i></a>
                                        </td>
                                </tr>  
                                @endforeach
                            </tbody>
                              </table>
                                {{--  <p>
                                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                      Link with href
                                    </a>
                                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                      Button with data-target
                                    </button>
                                  </p>
                                  <div class="collapse" id="collapseExample">
                                    <div class="card card-body">
                                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
                                    </div>
                                  </div>  --}}
                                
                                
                            </div>
                        </div>
                        <div class="tab-pane" id="Rides" role="tabpanel">
                            <div class="card-body">
                                    @foreach ($bookings as $item) 
                                    <div class="row">
                                       <div class="col-md-3 col-xs-6 b-r"> <strong>Date</strong>
                                           <br>
                                           <p class="text-muted">{{$item->date}}</p>
                                       </div>
                                       <div class="col-md-3 col-xs-6 b-r"> <strong>Total Rides</strong>
                                           <br>
                                           <p class="text-muted">{{$item->total_rides}}</p>
                                       </div>
                                       <div class="col-md-3 col-xs-6 b-r"> <strong>Cash Ride</strong>
                                           <br>
                                           <p class="text-muted">{{$item->cash_count}}</p>
                                       </div>
                                       <div class="col-md-3 col-xs-6"> <strong>Wallet Ride</strong>
                                           <br>
                                           <p class="text-muted">{{$item->wallet_count}}</p>
                                       </div>
                                       
                                   </div>  
                                   @endforeach
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--  @if($user->user_role[0]->role == 'driver')
        <div class="row">
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        @if($user->user_role[0]->role == 'user1')
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#trustedContacts" role="tab">Trusted Contacts</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Bookings</a> </li>
                        @endif
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#documents" role="tab">Documents</a> </li>

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="trustedContacts" role="tabpanel">
                            <div class="card-body">
                                <div class="m-t-30">
                                    @if($user->user_role[0]->role == 'driver')
                                        <small class="text-muted">Registration Number</small><h5>{{$user->registration_number}}</h5>
                                        <small class="text-muted">Vehicle Type</small><h5>{{isset($user->vehicle_type->vehicle_type) ? $user->vehicle_type->vehicle_type : '-'}}</h5>
                                        <small class="text-muted">Vehicle Manufacturer</small><h5>{{$user->vehicle_manufacturer}}</h5>
                                        <small class="text-muted">Vehicle Model</small><h5>{{$user->vehicle_model}}</h5>
                                        <small class="text-muted">Number Plate</small><h5>{{$user->number_plate}}</h5>
                                    @endif
                                    <small class="text-muted p-t-30 db">Registered On</small><h5>{{date('Y, M d', strtotime($user->created_at))}}</h5>
                                    <small class="text-muted p-t-30 db">Device Type</small><h5>{{ucfirst($user->device_type)}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="settings" role="tabpanel">
                            <div class="card-body">
                                <form class="form-horizontal form-material" method="post" action="{{route('update'.ucfirst($user->user_role[0]->role), ['id'=>$user->id])}}">
                                    <!-- <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-md-6" for="val-verify">Verified</label>
                                            <input type="checkbox" @if($user->is_verified == 1) checked @endif class="js-switch" data-color="#f62d51" name="val-verify" data-size="small" />
                                        </div>
                                    </div> -->
                                    {{csrf_field()}}
                                    @if($user->user_role[0]->role == 'driver')
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="col-md-6" for="val-idverify">Identity Verified</label>
                                                <input type="checkbox" @if($user->identity_verification == 1) checked @endif class="js-switch" data-color="#f62d51" name="val-idverify" data-size="small" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="col-md-6" for="val-vhverify">Vehicle Verified</label>
                                                <input type="checkbox" @if($user->vehicle_verification == 1) checked @endif class="js-switch" data-color="#f62d51" name="val-vhverify" data-size="small" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="col-md-6" for="val-docverify">Document Verified</label>
                                                <input type="checkbox" @if($user->document_verification == 1) checked @endif class="js-switch" data-color="#f62d51" name="val-docverify" data-size="small" />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <label class="col-md-6" for="val-block">Block</label>
                                            <input type="checkbox" @if($user->status == 'IN') checked @endif id="blockUser"  class="js-switch" data-color="#f62d51" data-size="small" name="val-block" id="blockUser"/>
                                        </div>
                                    </div>
                                    @if(isset($blockReason->id))
                                        <div class="form-group">
                                            <label class="col-md-12">Block/Unblock Reason</label>
                                            <div class="col-md-12 m-t-30">
                                                <textarea rows="5" id="blockMsg" name="val-msg" class="form-control form-control-line" placeholder="Provide your reason to block/unblock this {{$user->user_role[0]->role}}">{{$blockReason->reason}}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane" id="documents" role="tabpanel">
                            <div class="card-body">
                                @foreach($user->user_doc as $doc)
                                    @if($doc->docType->document_type != 'Other')
                                        <div class="row">
                                            <div class="col-md-12"><strong>{{$doc->docType->document_type}}</strong></div>
                                            <div class="col-md-12">
                                                <a href="{{URL::asset('/uploads/documents/'.$user->id.'/'.$doc->document_name)}}" download>
                                                    @if(in_array(pathinfo(URL::asset('/images/documents/'.$doc->document_name), PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'svg', 'gif']))
                                                        <iframe class="m-t-10" src="{{URL::asset('/uploads/documents/'.$user->id.'/'.$doc->document_name)}}" width="100%" height="100%" ></iframe>
                                                    @else
                                                        <span>{{$doc->document_name}}</span>
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif
                                @endforeach
                                <div class="row" id="otherDocs" @if(count($otherDocs) <= 0) style="display: none" @endif>
                                    <div class="col-md-12"><strong>Other Documents</strong></div>
                                    @foreach($otherDocs as $other)
                                        <div class="col-md-12 m-t-20">
                                            <a href="{{URL::asset('/uploads/documents/'.$user->id.'/'.$other->document_name)}}" download>
                                                @if(in_array(pathinfo(URL::asset('/uploads/documents/'.$user->id.'/'.$other->document_name), PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'svg', 'gif']))
                                                    <iframe class="m-t-10" src="{{URL::asset('/uploads/documents/'.$user->id.'/'.$other->document_name)}}" width="80%" height="100%" ></iframe>
                                                @else
                                                    <span>{{$doc->document_name}}</span>
                                                @endif
                                            </a>
                                            <a id="del_{{$other->id}}" href="javascript:void(0)" class="toolTip m-l-20 deleteOtherDoc" data-toggle="tooltip" data-placement="bottom" title="Delete">Delete</a></div>
                                    @endforeach
                                </div>
                                <form class="form-horizontal form-valide form-material m-t-20" method="post" action="{{route('addDoc',['id'=>$user->id])}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div id="uploadDoc">
                                                <a id="uploadOther" href="javascript:void(0)" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Upload Other Documents">Add Other Documents</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 m-t-20">
                                        <button type="submit" style="display: none;" id="uploadBtn" class="btn btn-success waves-effect waves-light m-r-10">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif  --}}
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
	<script type="text/javascript">
		$(function(){
            // $("#blockUser").chage(function(event) {
            //     if($(this).is(":checked")) {
            //     }
            // });

			$('#blockUser').change(function(){
				$('#blockMsg').val('');
                    $("#blockMsg").focus();
			});

            $('#uploadOther').click(function(){
                $('#uploadDoc').parent().append('<div class="fileinput fileinput-new input-group m-t-20" data-provides="fileinput"><div class="form-control" data-trigger="fileinput"> <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"> <span class="fileinput-new">Select file(Allowed Extensions -  .jpg, .jpeg, .png, .doc, .docx, .pdf)</span> <span class="fileinput-exists">Change</span><input type="file" name="doc[]"> </span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a></div>');
                $('.tooltip').tooltip('hide');
                $('#image_exists').val(0);
                $('#uploadBtn').show();
            });

            $('.deleteOtherDoc').click(function(){
                var id = $(this).attr('id').split('_')[1];
                $.ajax({
                    url: "{{route('deleteDoc')}}"+"/"+id,
                    type: "POST",
                    success: function(res)
                    {
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            $("#del_"+id).parent().remove();
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
            });
		})
        $('#vehicleState').change(function(){
                var state = $(this).val();
                $.ajax({
                    url: "{{route('getCity')}}",
                    type: "POST",
                    data: {state: state},
                    success: function(data){
                        var cities = JSON.parse(data);
                        var options = "<option value=''>Select City</option>";
                        for (var i = 0; i < cities.length; i++) {
                            options += "<option value='" + cities[i] + "'>" + cities[i] + "</option>";
                        }
                        $('#vehicleCity').html(options);
                    }
                })
            });
            $('#vehicleCity').change(function(){

                var state = $('#vehicleState').val();
                var city = $('#vehicleCity').val();

                $.ajax({
                    url: "{{route('getVehicleType')}}",
                    type: "POST",
                    data: {
                        state: state,
                        city:city
                    },
                    success: function(data){
                        var cities = JSON.parse(data);
                        var options = "<option value=''>Select Vehicle Type</option>";

                        if(cities.length>0){

                        // for (var i = 0; i < cities.length; i++) {
                        //     options += "<option value='" + cities[i] + "'>" + cities[i] + "</option>";
                        // }
                        $.each(cities,function(index,value){
                            console.log(value['vehicle_type']);
                            options += "<option value='" + value['id'] + "'>" + value['vehicle_type'] + "</option>";
                        });
                        $('#vehicletypes').html(options);
                        }
                        else{

                            alert('Vehicles not found!!')
                            $('#vehicletypes').html(options);
                        }
                    },
                    error: function(){
                $('#vehicletypes').html(options);

                alert('Vehicles not found!!')
        }
                })
            });


	</script>
@endpush