@extends('layouts.app')
@section('title', 'Cabs')

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Cabs</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Cabs</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <h4 class="card-title">Cabs</h4>
                        <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                        <div class="dt-buttons float-right">
                            <a href="{{route('createCab')}}" class="btn dt-button">Add Cab</a>
                        </div>
                        <div class="table-responsive m-t-40">
                            <table id="cabTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                         <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                	@foreach($cabs as $vehicle)
										<tr>
											<td>{{$vehicle->cab_type}}</td>
											
                                            {{-- <td>{{$vehicle->distance_time}}</td> --}}
                                            
											<td>{{ucfirst(config('constants.STATUS.'.$vehicle->status))}}</td>
											<td>
												@if($vehicle->status == 'AC')
													<a href="{{route('statusCab', ['id'=>$vehicle->id])}}" class="toolTip" data-status="{{$vehicle->status}}" data-id="{{$vehicle->id}}" data-toggle="tooltip" data-placement="bottom" title="Deactivate"><i class="fa fa-lock" aria-hidden="true"></i></a>
												@else
													<a href="{{route('statusCab', ['id'=>$vehicle->id])}}" class="toolTip" data-status="{{$vehicle->status}}" data-id="{{$vehicle->id}}" data-toggle="tooltip" data-placement="bottom" title="Activate"><i class="fa fa-unlock" aria-hidden="true"></i></a>
												@endif
                                                &nbsp;&nbsp;&nbsp;<a href="{{route('editCab', ['id'=>$vehicle->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>
												&nbsp;&nbsp;&nbsp;<a href="{{route('viewCab', ['id'=>$vehicle->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>
                                                &nbsp;&nbsp;&nbsp;<a href="{{route('deleteCab', ['id'=>$vehicle->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>
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
    		var table = $('#cabTable').DataTable({
		        dom: 'Bfrtip',
		        buttons: [
                    {extend: 'copy',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'csv',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'excel',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'pdf',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'print',exportOptions: {columns: 'th:not(:last-child)'}}
                ],
                "columnDefs": [
                    {"targets": 2,"orderable": false},
                    {"targets": [0,1,2], "searchable": true}
                ],
                "aaSorting": []
		    });

    	});
    </script>
@endpush