@extends('layouts.app')
@section('title', ucfirst($usertype).'s')

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">{{ucfirst($usertype)}}s</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($usertype)}}s</li>
                </ol>
            </div>
        </div>
<form action="" method="get" id="filter_form">
@include("layouts.filter")
</form>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <h4 class="card-title">{{ucfirst($usertype)}}s</h4>
                        <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                        <form action="{{route('singleNotification')}}" method="post">
                            {{csrf_field()}}
                            <div class="dt-buttons float-right">
                                <button type="submit" id="sendNotification" class="btn dt-button" disabled>Send Notification</button>
                            </div>
                            <div class="table-responsive m-t-40">
                                <table id="usersTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Name</th>
                                            <th>Email ID</th>
                                            <th>Contact Number</th>
                                            <th>Country</th>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Payment Method</th>
                                            <th>Average Rating</th>
                                            <th>Wallet Amount</th>
                                            <th>Referral Code</th>
                                            <th>Verified</th>
                                            @if($usertype == 'driver')
                                                <th>Registration Number</th>
                                                <th>Number Plate</th>
                                                <th>Vehicle Manufacturer</th>
                                                <th>Vehicle</th>
                                                <th>Vehicle Model</th>
                                                <th>Vehicle Color</th>
                                                <th>Identity Verified</th>
                                                <th>Vehicle Verified</th>
                                                <th>Document Verified</th>
                                            @endif
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Select</th>
                                            <th>Name</th>
                                            <th>Email ID</th>
                                            <th>Contact Number</th>
                                            <th>Country</th>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Payment Method</th>
                                            <th>Average Rating</th>
                                            <th>Wallet Amount</th>
                                            <th>Referral Code</th>
                                            <th>Verified</th>
                                            @if($usertype == 'driver')
                                                <th>Registration Number</th>
                                                <th>Number Plate</th>
                                                <th>Vehicle Manufacturer</th>
                                                <th>Vehicle</th>
                                                <th>Vehicle Model</th>
                                                <th>Vehicle Color</th>
                                                <th>Identity Verified</th>
                                                <th>Vehicle Verified</th>
                                                <th>Document Verified</th>
                                            @endif
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    	@foreach($users as $user)
    										<tr>
                                                <td>
                                                    <div class="checkbox checkbox-info pull-left p-t-0">
                                                        <input id="checkbox-signup" type="checkbox" class="filled-in chk-col-light-blue send" name="user_id[]" value="{{$user->id}}" style="position: inherit !important;opacity: 1 !important;">
                                                    </div></td>
    											<td>{{$user->name}}</td>
    											<td>{{$user->email}}</td>
    											<td>{{$user->mobile_number}}</td>
                                                <td>{{$user->country}}</td>
                                                <td>{{$user->state}}</td>
                                                <td>{{$user->city}}</td>
                                                <td>{{$user->payment_method}}</td>
                                                <td>{{isset($user->avgRating[0]->avg_rating) ? $user->avgRating[0]->avg_rating : 0}}</td>
                                                <td>{{session()->get('currency') . " " . $user->wallet_amount}}</td>
                                                <td>{{$user->referral_code}}</td>
                                                <td>{{config('constants.CONFIRM.'.$user->is_verified)}}</td>
                                                @if($usertype == 'driver')
                                                <td>{{$user->registration_number}}</td>
                                                    <td>{{$user->number_plate}}</td>
                                                    <td>{{$user->vehicle_manufacturer}}</td>
                                                    <td>{{isset($user->vehicle_type->vehicle_type)?$user->vehicle_type->vehicle_type:'-'}}</td>
                                                    <td>{{$user->vehicle_model}}</td>
                                                    <td>{{$user->vehicle_color}}</td>
                                                    <td>{{config('constants.CONFIRM.'.$user->identity_verification)}}</td>
                                                    <td>{{config('constants.CONFIRM.'.$user->vehicle_verification)}}</td>
                                                    <td>{{config('constants.CONFIRM.'.$user->document_verification)}}</td>
                                                @endif
    											<td>{{ucfirst(config('constants.STATUS.'.$user->status))}}</td>
    											<td>
    												@if($user->status == 'AC')
    													<a href="javascript:void(0)" class="changeStatus toolTip" data-status="{{$user->status}}" data-id="{{$user->id}}" data-toggle="tooltip" data-placement="bottom" title="Block {{ucfirst($usertype)}}"><i class="fa fa-lock" aria-hidden="true"></i></a>
    												@else
    													<a href="javascript:void(0)" class="changeStatus toolTip" data-status="{{$user->status}}" data-id="{{$user->id}}" data-toggle="tooltip" data-placement="bottom" title="Unblock {{ucfirst($usertype)}}"><i class="fa fa-unlock" aria-hidden="true"></i></a>
    												@endif
                                                    &nbsp;&nbsp;&nbsp;<a href="{{route('view'.ucfirst($usertype),['id'=>$user->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>
                                                    {{-- @if($usertype == 'driver')
                                                        &nbsp;&nbsp;&nbsp;<a href="{{route('info'.ucfirst($usertype),['id'=>$user->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Info Detail"><i class="fa fa-eye"></i></a></td>
                                                    @endif --}}
    										</tr>
                                    	@endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>

    <div class="modal fade" id="userStatusModal" tabindex="-1" role="dialog" aria-labelledby="userStatusModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="userStatusModalLabel"><span class='userstatus'></span> {{ucfirst($usertype)}}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-valide" method="post" id="blockForm" action="{{route('block'.ucfirst($usertype))}}">
                        {{csrf_field()}}
                        <input type="hidden" name="val-id" id="val-id">
                        <input type="hidden" name="val-status" id="val-status">
                        <div class="form-group">
                            <label class="col-form-label" for="val-reason"><span class='userstatus'></span> Reason</label>
                            <textarea class="form-control" id="val-reason" name="val-reason" rows="5"></textarea>
                        </div>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-info btn-flat blockBtn m-b-30 m-t-30">Submit</button>
                    </form>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')

    <script src="{{URL::asset('/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script type="text/javascript">
    	$(function(){
            @if($usertype == 'user')
        		var table = $('#usersTable').DataTable({
    		        dom: 'Bfrtip',
    		        buttons: [
                        {extend: 'copy',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'csv',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'excel',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'pdf',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'print',exportOptions: {columns: 'th:not(:last-child)'}}
                    ],
                    "columnDefs": [
                        {"targets": 13,"orderable": false},
                        {"targets": [4,5,6,7,8,9,10,11], visible: false},
                        {"targets": [4,5,6,7,8,9,10,11], "searchable": true}
                    ],
                    "aaSorting": []
    		    });
            @elseif($usertype == 'driver')
                var table = $('#usersTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {extend: 'copy',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'csv',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'excel',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'pdf',exportOptions: {columns: 'th:not(:last-child)'}},
                        {extend: 'print',exportOptions: {columns: 'th:not(:last-child)'}}
                    ],
                    "columnDefs": [
                        {"targets": 20,"orderable": false},
                        {"targets": [4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20], visible: false},
                        {"targets": [4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20], "searchable": true}
                    ],
                    "aaSorting": []
                });
            @endif

    		$('.changeStatus').click(function(){
    			var status = $(this).data('status');
    			var id = $(this).data('id');
    			if(status == 'AC')
    			{
    				$('#val-id').val(id);
    				$('#val-status').val('IN');
    				$('.userstatus').text('Block');
    			}
    			else
    			{
    				$('#val-id').val(id);
    				$('#val-status').val('AC');
    				$('.userstatus').text('Unblock');
    			}

    			$('#userStatusModal').modal('show');
    		});

            // $("#sendNotification").click(function(event) {
            //     var url = "{{route('createNotification')}}";
            //     console.log($('input[name="user_ids"]:checked').serialize());
            //     var ids="";
            //     $('input[name="user_ids"]:checked').each(function() {
            //        ids+=(this.value)+",";
            //     });
            //     ids = ids.replace(/,\s*$/, "");
            //     window.location.href = url+"?user_id="+ids;
            //     // console.log(ids);
            // });

            $(document).on("click",".send",function(event) {
                var items = [];
                $("input[name='user_id[]']:checked").each(function(){items.push($(this).val());});
                if(items.length>0){
                    $("#sendNotification").removeAttr('disabled');
                }else{
                    $("#sendNotification").prop('disabled', 'disabled');
                }
                console.log(items);
            });
    	});
    </script>
@endpush