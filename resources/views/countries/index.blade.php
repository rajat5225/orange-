@extends('layouts.app')
@section('title', 'Coupon Codes')

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Coupon Codes</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item active">Coupon Codes</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <h4 class="card-title">Coupon Codes</h4>
                        <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                        <div class="dt-buttons float-right">
                            <a href="{{route('createCouponCode')}}" class="btn dt-button">Add Coupon Code</a>
                        </div>
                        <div class="table-responsive m-t-40">
                            <table id="codesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Coupon Code</th>
                                        <th>Title</th>
                                        <th>Discount Type</th>
                                        <th>Coupon Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Country</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Minimum Amount</th>
                                        <th>No. of Rides</th>
                                        <th>Minimum Rides</th>
                                        <th>Discount Value</th>
                                        <th>No. Of Applies</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Coupon Code</th>
                                        <th>Title</th>
                                        <th>Discount Type</th>
                                        <th>Coupon Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Country</th>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Minimum Amount</th>
                                        <th>No. of Rides</th>
                                        <th>Minimum Rides</th>
                                        <th>Discount Value</th>
                                        <th>No. Of Applies</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                	@foreach($codes as $code)
										<tr>
											<td>{{$code->coupon_code}}</td>
											<td>{{$code->title}}</td>
                                            <td>{{ucfirst($code->amount_type)}}</td>
                                            <td>{{ucfirst($code->discount_type)}}</td>
                                            <td>{{date('Y M, d', strtotime($code->start_date))}}</td>
                                            <td>{{date('Y M, d', strtotime($code->end_date))}}</td>
                                            <td>{{$code->country}}</td>
                                            <td>{{$code->state}}</td>
                                            <td>{{$code->city}}</td>
                                            <td>{{$code->min_amount}}</td>
                                            <td>{{($code->no_of_rides != null) ? $code->no_of_rides : '-'}}</td>
                                            <td>{{($code->min_rides != null) ? $code->min_rides : '-'}}</td>
                                            <td>{{$code->discount_value}}</td>
                                            <td>{{$code->no_of_applies}}</td>
                                            <td>{{date('Y M, d', strtotime($code->created_at))}}</td>
                                            <td>{{config('constants.STATUS.'.$code->status)}}</td>
											<td>
												@if($code->status == 'AC')
													<a href="{{route('statusCouponCode', ['id' => $code->id])}}" class="toolTip" data-status="{{$code->status}}" data-id="{{$code->id}}" data-toggle="tooltip" data-placement="bottom" title="Deactivate"><i class="fa fa-lock" aria-hidden="true"></i></a>
												@else
													<a href="{{route('statusCouponCode', ['id'=>$code->id])}}" class="toolTip" data-status="{{$code->status}}" data-id="{{$code->id}}" data-toggle="tooltip" data-placement="bottom" title="Activate"><i class="fa fa-unlock" aria-hidden="true"></i></a>
												@endif
                                                &nbsp;&nbsp;&nbsp;<a href="{{route('editCouponCode', ['id'=>$code->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>
												&nbsp;&nbsp;&nbsp;<a href="{{route('viewCouponCode', ['id'=>$code->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>
                                                &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteCode" data-code="{{$code->coupon_code}}" data-id="{{$code->id}}" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>
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
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-valide" method="post" id="blockForm" action="{{route('deleteCouponCode')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="val-id" id="val-id">
                        <h5 class="m-t-10 text-danger">Are you sure you want to delete Coupon Code : <span id="val-code"></span></h5>
                        <button type="button" class="btn btn-secondary btn-flat cancelBtn m-b-30 m-t-30" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info btn-flat confirmBtn m-b-30 m-t-30">Confirm</button>
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
    		var table = $('#codesTable').DataTable({
		        dom: 'Bfrtip',
		        buttons: [
                    {extend: 'copy',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'csv',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'excel',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'pdf',exportOptions: {columns: 'th:not(:last-child)'}},
                    {extend: 'print',exportOptions: {columns: 'th:not(:last-child)'}}
                ],
                "columnDefs": [
                    {"targets": 16,"orderable": false},
                    {"targets": [1,6,7,9,10,11,12,13,14], visible: false},
                    {"targets": [1,6,7,9,10,11,12,13,14], "searchable": true}
                ],
                "aaSorting": []
		    });

            $('.deleteCode').click(function(){
                var id = $(this).data('id');
                var code = $(this).data('code');
                $('#val-id').val(id);
                $('#val-code').text(code);

                $('#confirmDeleteModal').modal('show');
            });

    	});
    </script>
@endpush