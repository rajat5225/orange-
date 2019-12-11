@extends('layouts.app')
@section('title', 'Vehicles')

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Vehicles</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Vehicles</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <h4 class="card-title">Vehicles</h4>
                        <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                        <div class="dt-buttons float-right">
                            <a href="{{route('createVehicle')}}" class="btn dt-button">Add Vehicle</a>
                        </div>
                        <div class="table-responsive m-t-40">
                            <table id="vehiclesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Price</th>
                                        <th>Driver Charge <small>(In %)</small></th>
                                        {{-- <th>Distance Time<small>(sec/km)</small></th> --}}
                                        <th>Waiting Charge</th>
                                        {{-- <th>Cancellation Charge</th> --}}
                                        <th>Capacity</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Price</th>
                                        <th>Driver Charge <small>(In %)</small></th>
                                        {{-- <th>Distance Time<small>(sec/km)</small></th> --}}
                                        <th>Waiting Charge </th>
                                        {{-- <th>Cancellation Charge</th> --}}
                                        <th>Capacity</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>

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
    <script src="{{URL::asset('/js/datatable-pipeline.js')}}"></script>
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
    		var table = $('#vehiclesTable').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax":  {
                    url: "{{route('vehiclesAjax')}}",
                    dataSrc:"data",
                } ,

                paging: true,
                pageLength: 10,
                // "bProcessing": true,
                // "bServerSide": true,
                "bLengthChange": false,
                "aoColumns": [
                    { "data": "vehicle_type" },
                    { "data": "price" },
                    { "data": "driver_charge" },
                    { "data": "waiting_charge" },
                    { "data": "capacity" },
                    { "data": "city" },
                    { "data": "state" },
                    { "data": "country" },
                    // { "data": "featured" },
                    { "data": "status" },
                    { "data": "action" },
                ],
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
                    {"targets": [3,4,8], visible: false},
                    {"targets": [0,5,6,7,8], "searchable": true}
                ],
                "aaSorting": []
		    });

    	});
    </script>
@endpush