@extends('layouts.app')
@section('title', 'Ride Support')

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Ride Supports</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Ride Support</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <h4 class="card-title">Ride Support</h4>
                        <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                        <div class="table-responsive m-t-40">
                            <table id="ridesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Support Code</th>
                                        <th>User</th>
                                        <th>Driver</th>
                                        <th>Subject</th>
                                        <th>Comment</th>
                                        <th>Reply</th>
                                        <th>Vehicle</th>
                                        <th>Booking Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Support Code</th>
                                        <th>User</th>
                                        <th>Driver</th>
                                        <th>Subject</th>
                                        <th>Comment</th>
                                        <th>Reply</th>
                                        <th>Vehicle</th>
                                        <th>Booking Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                	@foreach($supports as $support)
                                    	<tr>
											<td>{{ $support->support_code}}</td>
                                            <td>{{isset($support->booking->user->name) ? $support->booking->user->name : '-'}}</td>
											<td>{{isset($support->booking->driver->name) ? $support->booking->driver->name : '-'}}</td>
                                            <td>{!! $support->subject->subject !!}</td>
                                            <td>{!! $support->comment !!}</td>
                                            <td>{!! isset($support->reply) ? $support->reply : '-' !!}</td>
                                            <td>{{isset($support->booking->vehicle->vehicle_type) ? $support->booking->vehicle->vehicle_type : '-'}}</td>
                                            <td>{{date('Y M, d', strtotime($support->booking->created_at))}}</td>
                                            <td>{{ucfirst(config('constants.STATUS.'.$support->status))}}</td>
											<td>
												@if($support->status == 'AC')
                                                    <a href="{{route('updateRideSupportRequest',['id'=>$support->id])}}" class="toolTip" data-status="{{$support->status}}" data-id="{{$support->id}}" data-toggle="tooltip" data-placement="bottom" title="Deactivate"><i class="fa fa-lock" aria-hidden="true"></i></a>
                                                @else
                                                    <a href="{{route('updateRideSupportRequest',['id'=>$support->id])}}" class="toolTip" data-status="{{$support->status}}" data-id="{{$support->id}}" data-toggle="tooltip" data-placement="bottom" title="Activate"><i class="fa fa-unlock" aria-hidden="true"></i></a>
                                                @endif
                                                &nbsp;&nbsp;&nbsp;<a href="{{route('editRideSupportRequest',['id'=>$support->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>
                                                &nbsp;&nbsp;&nbsp;<a href="{{route('viewRideSupportRequest',['id'=>$support->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>
                                                &nbsp;&nbsp;&nbsp;<a href="{{route('deleteRideSupportRequest',['id'=>$support->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>
                                            </td>
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
                    {"targets": 9,"orderable": false},
                    {"targets": [5,6,7], visible: false},
                    {"targets": [5,6,7], "searchable": true}
                ],
                "aaSorting": []
		    });

    	});
    </script>
@endpush