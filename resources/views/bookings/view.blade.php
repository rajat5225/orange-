@extends('layouts.app')
@section('title', 'Ride - '.$ride->user->name)

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Ride Details</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('rides')}}">Rides</a></li>
                    <li class="breadcrumb-item active">Ride Details</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                	@include('layouts.message')
                    <div class="card-body"><small class="text-muted">Booking Code </small>
                        <h6>{{$ride->booking_code}}</h6>  <small class="text-muted p-t-30 db">User </small>
                        <h6>{{isset($ride->user->name) ? $ride->user->name : '-'}}</h6> <small class="text-muted p-t-30 db">Driver</small>
                        <h6>{{isset($ride->driver->name) ? $ride->driver->name : '-'}}</h6> <small class="text-muted p-t-30 db">Location</small>
                        <h6>{{$ride->user->city}}, {{$city->state->name}}, {{$city->state->country->name}}</h6>
                        <small class="text-muted p-t-30 db">Booking Status</small><h6>{{ucfirst(config('constants.BOOKING_STATUS.'.$ride->booking_status))}}</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#details" role="tab">Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#fare" role="tab">Fare Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#map" role="tab">Route Map</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#sharedRide" role="tab">Shared Ride</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Reviews</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="details" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>User</strong>
                                        <br>
                                        <p class="text-muted">{{$ride->user->name}}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Driver</strong>
                                        <br>
                                        <p class="text-muted">{{isset($ride->driver->name) ? $ride->driver->name : '-'}}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6 b-r"> <strong>Vehicle</strong>
                                        <br>
                                        <p class="text-muted">{{$ride->vehicle->vehicle_type}}</p>
                                    </div>
                                    <div class="col-md-3 col-xs-6"> <strong>Location</strong>
                                        <br>
                                        <p class="text-muted">{{$ride->user->city}}, {{$city->state->name}}, {{$city->state->country->name}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="m-t-30">
                                	<small class="text-muted p-t-30 db">Pick Up Address</small><h5>{{$ride->pickup_address}}</h5>
                                    <small class="text-muted p-t-30 db">Drop Off Address</small><h5>{{$ride->dropoff_address}}</h5>
                                    <small class="text-muted p-t-30 db">Distance</small><h5>{{$ride->distance}}</h5>
                                    <small class="text-muted p-t-30 db">Booking Time</small><h5>{{date('Y M, d h:i:s a', strtotime($ride->created_at))}}</h5>
                                    <small class="text-muted p-t-30 db">Driver Accept Time</small><h5> {{($ride->driver_id!=0) ? date('Y M, d h:i:s a', doubleVal($ride->driver_accept_time)/1000) : "-"}}</h5>
                                    @if($ride->booking_status!=4)
                                    <small class="text-muted p-t-30 db">Arrival Time</small><h5>{{date('Y M, d h:i:s a', doubleVal($ride->arrived_time)/1000)}}</h5>
                                    <small class="text-muted p-t-30 db">Start Time</small><h5>{{date('Y M, d h:i:s a', doubleVal($ride->start_time)/1000)}}</h5>
                                    @endif
                                    <small class="text-muted p-t-30 db">Waiting Time (in sec)</small><h5>{{($ride->waiting_time != '') ? $ride->waiting_time : '-'}}</h5>
                                    @if($ride->booking_status==4)
                                    <small class="text-muted p-t-30 db">Cancellation Time</small><h5>{{date('Y M, d h:i:s a', strtotime($ride->updated_at))}}</h5>
                                    @else
                                    <small class="text-muted p-t-30 db">End Time</small><h5>{{date('Y M, d h:i:s a', doubleVal($ride->end_time)/1000)}}</h5>
                                    @endif
                                    <small class="text-muted p-t-30 db">Scheduled Ride</small><h5>{{config('constants.CONFIRM.'.$ride->schedule)}}</h5>
                                    @if($ride->schedule == 1)
                                        <small class="text-muted p-t-30 db">Scheduled Time</small><h5>{{date('Y M, d h:i:s a', strtotime($ride->scheduled_dateTime))}}</h5>
                                    @endif
                                    <small class="text-muted p-t-30 db">Booking Status</small><h5>{{ucfirst(config('constants.BOOKING_STATUS.'.$ride->booking_status))}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="fare" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <small class="text-muted p-t-30 db">Fare</small><h5>{{$ride->cost}}</h5>
                                    <small class="text-muted p-t-30 db">Vehicle Base Fare Charge</small><h5>{{$ride->base_fare_charge}}</h5>
                                    <small class="text-muted p-t-30 db">Promo Code</small><h5>{{isset($ride->coupon_code->coupon_code) ? $ride->coupon_code->coupon_code : '-'}}</h5>
                                    <small class="text-muted p-t-30 db">Promo Code Type</small><h5>{{isset($ride->coupon_code->discount_type) ? ucfirst($ride->coupon_code->discount_type) : '-'}}</h5>
                                    @if(isset($ride->promo_code->promo_code))
                                        <small class="text-muted p-t-30 db">Promo Code Deduction(in {{session()->get('currency')}})</small><h5>{{$ride->promo_deduct}}</h5>
                                    @endif
                                    @if($ride->booking_status!=4)
                                    <small class="text-muted p-t-30 db">Cancellation Charges(in {{session()->get('currency')}})</small><h5>{{$ride->cancellation_charge}}</h5>
                                    @else
                                    <small class="text-muted p-t-30 db">Cancellation Charges on Next Ride(in {{session()->get('currency')}})</small><h5>{{$ride->cancellation_charge_next_ride}}</h5>
                                    @endif
                                    <small class="text-muted p-t-30 db">CGST(in {{session()->get('currency')}})</small><h5>{{$ride->cgst}}</h5>
                                    <small class="text-muted p-t-30 db">SGST(in {{session()->get('currency')}})</small><h5>{{$ride->sgst}}</h5>
                                    <small class="text-muted p-t-30 db">IGST(in {{session()->get('currency')}})</small><h5>{{$ride->igst}}</h5>
                                    <small class="text-muted p-t-30 db">Total Fare(in {{session()->get('currency')}})</small><h5>{{$ride->total}}</h5>
                                    <small class="text-muted p-t-30 db">Payment Method</small><h5>{{($ride->payment_mode != null) ? ucfirst($ride->payment_mode) : '-'}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="map" role="tabpanel">
                            <div class="card-body">
                                @if($ride->path_image != null)
                                    <img src="{{$ride->path_image}}" alt="Route map image not available.">
                                @else
                                    <h5 class="text-danger">Path Not Available</h5>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane" id="sharedRide" role="tabpanel">
                            <div class="card-body">
                                @if($ride->trusted_contacts == 1)
                                    <small class="text-muted p-t-30 db">Ride shared with contacts</small><h5>{{config('constants.CONFIRM.'.$ride->trusted_contacts)}}</h5>

                                    <div class="m-t-20">
                                        @if(count($ride->user->trusted_contact) > 0)
                                            @foreach($ride->user->trusted_contact as $contact)
                                                @if(isset($contact->parent->id))
                                                    <small class="p-t-30 db">{{$contact->parent->name}}</small>
                                                    <a href="/admin/user/{{$contact->parent->id}}" target="_blank"><h5 class="text-primary">{{$contact->parent->email}}</h5></a>
                                                @endif
                                            @endforeach
                                        @else
                                            <h5 class="text-danger m-t-20">No contact found.</h5>
                                        @endif
                                    </div>
                                @else
                                    <h5 class="text-danger m-t-20">This ride is not shared with any of the contact.</h5>
                                @endif

                                @if($ride->sos == 1)
                                    <small class="text-muted m-t-20 p-t-30 db">SOS</small><h5>{{config('constants.CONFIRM.'.$ride->sos)}}</h5>
                                @else
                                    <h5 class="text-danger m-t-20">This ride is not shared with you.</h5>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane" id="reviews" role="tabpanel">
                            <div class="card-body">
                                @if(count($ride->reviews) > 0)
                                    @foreach($ride->reviews as $review)
                                        @if(isset($review->parent->id) && isset($review->user->id))
                                            <h5 class="p-t-20 db"><span class="text-primary"><a href="/admin/user/{{$review->user->id}}" target="_blank">{{$review->user->name}}</a></span> reviewed <span class="text-primary"><a href="/admin/user/{{$review->parent->id}}" target="_blank">{{$review->parent->name}}</a></span></h5>
                                            <div class="col-6">
                                                <small class="text-muted p-t-30 db">Rating</small><h5>{{$review->rating}}</h5>
                                            </div>
                                            @if($review->complement_id != 0)
                                                <div class="col-6">
                                                    <small class="text-muted p-t-30 db">Compliment</small><h5>{{$review->complement->name}}</h5>
                                                </div>
                                            @endif
                                            <div class="col-12">
                                                <small class="text-muted p-t-30 db">Comments</small><h6>{!! ($review->comment != '') ? $review->comment : '-' !!}</h6>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <h5 class="text-danger m-t-20">No reviews found.</h5>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($ride->booking_support) > 0)
                <div class="col-lg-12 col-xlg-12 col-md-12">
                    <div class="card">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs profile-tab" role="tablist">
                            <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#support" role="tab">Support Requests</a> </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="support" role="tabpanel">
                                <div class="card-body">
                                    <div class="">
                                        @if(count($ride->booking_support) > 0)
                                            @foreach($ride->booking_support as $support)
                                                @if(isset($support->booking->user->id))
                                                    <h6 class="p-t-30 db">{{$support->booking->user->name}} sent you the request</h6><a href="/admin/user/{{$support->booking->user->id}}" target="_blank"><h6 class="text-primary">{{$support->booking->user->email}}</h6></a>
                                                    <small class="p-t-30 db">Support Code</small>
                                                    <h6>{{$support->support_code}}</h6>
                                                    <small class="p-t-30 db">Support Subject</small>
                                                    <h6>{{$support->subject->subject}}</h6>
                                                    <small class="p-t-30 db">Message</small>
                                                    <h6>{{$support->comment}}</h6>
                                                    <small class="p-t-10 db">Status</small>
                                                    <h6>{{ucfirst(config('constants.STATUS.'.$support->status))}}</h6>
                                                    <small class="p-t-10 db">Your Reply</small>
                                                    <div contenteditable="false" placeholder="Send a Reply" class="m-t-10 m-b-40 p-10" id="click2edit_{{$support->id}}" data-reply="{!! $support->reply !!}">
                                                        {!! $support->reply !!}
                                                    </div>
                                                    <button id="reply_{{$support->id}}" class="btn btn-info replyBtn btn-rounded m-t-10" type="button">Reply</button>
                                                    <button id="send_{{$support->id}}" class="btn btn-success sendBtn btn-rounded m-t-10" type="button">Send</button>
                                                    <button id="cancel_{{$support->id}}" class="btn btn-warning cancelBtn btn-rounded m-t-10" type="button" style="display: none;">Cancel</button>
                                                @endif
                                                <hr>
                                            @endforeach
                                        @else
                                            <h5 class="text-danger m-t-20">No Request found.</h5>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function(){
            $('.replyBtn').click(function(){
                var id = $(this).attr('id').split('_')[1];
                $("#click2edit_"+id).addClass('editableDiv');
                $("#click2edit_"+id).attr('contenteditable', true);
                $('#reply_'+id).hide();
                $('#cancel_'+id).show();
            });

            $('.cancelBtn').click(function(){
                var id = $(this).attr('id').split('_')[1];
                // $("#click2edit_"+id).summernote('destroy');
                $("#click2edit_"+id).removeClass('editableDiv');
                $("#click2edit_"+id).attr('contenteditable', false);
                $("#click2edit_"+id).html($("#click2edit_"+id).data('reply'));
                $('#reply_'+id).show();
                $('#cancel_'+id).hide();
            });

            $('.sendBtn').click(function(){
                var id = $(this).attr('id').split('_')[1];
                $("#send_"+id).prop('disabled', true);
                $("#cancel_"+id).prop('disabled', true);
                $.ajax({
                    url: "{{route('replyRideSupportRequest')}}"+'/'+id,
                    type: "POST",
                    data: {content: $("#click2edit_"+id).text()},
                    success: function(res){
                        var data = JSON.parse(res);
                        $("#send_"+id).prop('disabled', false);
                        $("#cancel_"+id).prop('disabled', false);
                        if(data.status == 1)
                        {
                            $("#click2edit_"+id).data('reply', $("#click2edit_"+id).text());
                            $("#click2edit_"+id).removeClass('editableDiv');
                            $("#click2edit_"+id).attr('contenteditable', false);
                            $('#reply_'+id).show();
                            $('#cancel_'+id).hide();
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
                })

            });
        });

    </script>
@endpush
