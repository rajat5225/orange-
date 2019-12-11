@extends('layouts.app')
@section('title', 'Rides')

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Rides</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Rides</li>
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
                        <h4 class="card-title">Rides</h4>
                        <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                        <div class="table-responsive m-t-40">
                            <table id="ridesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Booking Code</th>
                                        <th>User</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th>Promo Code</th>
                                        <th>Pickup Address</th>
                                        <th>Drop Off Address</th>
                                        <th>Distance</th>
                                        <th>Booking Time</th>
                                        <th>Driver Accept Time</th>
                                        <th>Arrival Time</th>
                                        <th>Start Time</th>
                                        <th>Waiting Time</th>
                                        <th>End Time</th>
                                        <th>Scheduled Ride</th>
                                        <th>Scheduled Time</th>
                                        <th>Fare</th>
                                        <th>Promo Code Deduction(in {{session()->get('currency')}})</th>
                                        <th>Cancellation Charges(in {{session()->get('currency')}})</th>
                                        <th>CGST(in {{session()->get('currency')}})</th>
                                        <th>SGST(in {{session()->get('currency')}})</th>
                                        <th>IGST(in {{session()->get('currency')}})</th>
                                        <th>Total Fare(in {{session()->get('currency')}})</th>
                                        <th>Payment Type</th>
                                        <th>Booking Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Booking Code</th>
                                        <th>User</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th>Promo Code</th>
                                        <th>Pickup Address</th>
                                        <th>Drop Off Address</th>
                                        <th>Distance</th>
                                        <th>Booking Time</th>
                                        <th>Driver Accept Time</th>
                                        <th>Arrival Time</th>
                                        <th>Start Time</th>
                                        <th>Waiting Time (in sec)</th>
                                        <th>End Time</th>
                                        <th>Scheduled Ride</th>
                                        <th>Scheduled Time</th>
                                        <th>Fare</th>
                                        <th>Promo Code Deduction(in {{session()->get('currency')}})</th>
                                        <th>Cancellation Charges(in {{session()->get('currency')}})</th>
                                        <th>CGST(in {{session()->get('currency')}})</th>
                                        <th>SGST(in {{session()->get('currency')}})</th>
                                        <th>IGST(in {{session()->get('currency')}})</th>
                                        <th>Total Fare(in {{session()->get('currency')}})</th>
                                        <th>Payment Type</th>
                                        <th>Booking Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                	@foreach($rides as $ride)
                                    	<tr>
											<td>{{$ride->booking_code}}</td>
                                            <td>{{isset($ride->user->name) ? $ride->user->name : '-'}}</td>
											<td>{{isset($ride->driver->name) ? $ride->driver->name : '-'}}</td>
                                            <td>{{isset($ride->vehicle->vehicle_type) ?$ride->vehicle->vehicle_type : '-'}}</td>
                                            <td>{{isset($ride->coupon_code->coupon_code) ? $ride->coupon_code->coupon_code : '-'}}</td>
                                            <td>{{$ride->pickup_address}}</td>
                                            <td>{{$ride->dropoff_address}}</td>
                                            <td>{{$ride->distance}}</td>
                                            <td>{{date('Y M, d h:i a', strtotime($ride->created_at))}}</td>
                                            <td>{{date('Y M, d h:i a', doubleVal($ride->driver_accept_time)/1000)}}</td>
                                            <td>{{date('Y M, d h:i a', doubleVal($ride->arrived_time)/1000)}}</td>
                                            <td>{{date('Y M, d h:i a', doubleVal($ride->start_time)/1000)}}</td>
                                            <td>{{($ride->waiting_time != '') ? $ride->waiting_time : '-'}}</td>
                                            <td>{{date('Y M, d h:i a', doubleVal($ride->end_time)/1000)}}</td>
                                            <td>{{config('constants.CONFIRM.'.$ride->schedule)}}</td>
                                            <td>{{date('Y M, d h:i a', strtotime($ride->scheduled_dateTime))}}</td>
                                            <td>{{$ride->cost}}</td>
                                            <td>{{$ride->promo_deduct}}</td>
                                            <td>{{$ride->cancellation_charge}}</td>
                                            <td>{{$ride->cgst}}</td>
                                            <td>{{$ride->sgst}}</td>
                                            <td>{{$ride->igst}}</td>
                                            <td>{{$ride->total}}</td>
                                            <td>{{$ride->payment_type}}</td>
                                            <td>{{ucfirst(config('constants.BOOKING_STATUS.'.$ride->booking_status))}}</td>
											<td>
												<a href="{{route('viewRide', ['id' => $ride->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a></td>
										</tr>
                                	@endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
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
    		var table = $('#ridesTable').DataTable({
		        dom: 'Bfrtip',
		        buttons: [
                    {extend: 'copy',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'csv',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'excel',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'pdf',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'print',exportOptions: {columns: 'th:not(:last-child)'}}
                ],
                "columnDefs": [
                    {"targets": 25,"orderable": false},
                    {"targets": [4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23], visible: false},
                    {"targets": [4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23], "searchable": true}
                ],
                "aaSorting": []
		    });

    	});
    </script>
@endpush