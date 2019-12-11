@extends('layouts.app')
@section('title', 'Coupon Code - ' . $code->coupon_code)

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Coupon Code - {{$code->coupon_code}} <a href="{{route('editCouponCode', ['id'=>$code->id])}}" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a></h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('couponCodes')}}">Coupon Codes</a></li>
                    <li class="breadcrumb-item active">Coupon Code - {{$code->coupon_code}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <small class="text-muted db">Coupon Code</small><h4 class="card-title m-t-10">{{$code->coupon_code}}</h4>
                        <small class="text-muted p-t-30 db">State</small><h5>{{$code->state}}</h5>
                        <small class="text-muted p-t-30 db">City</small><h5>{{$code->city}}</h5>
                    </div>
                    <div>
                        <hr> </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#details" role="tab">Details</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#description" role="tab">Description</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#terms" role="tab">Terms Of Use</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="details" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <small class="text-muted db">Title</small><h5>{{$code->title}}</h5>
                                    <small class="text-muted p-t-30 db">Minimum Amount (in {{session()->get('currency')}})</small><h5>{{$code->min_amount}}</h5>
                                    <small class="text-muted p-t-30 db">Start date</small><h5>{{date('Y M, d', strtotime($code->start_date))}}</h5>
                                    <small class="text-muted p-t-30 db">End Date</small><h5>{{date('Y M, d', strtotime($code->end_date))}}</h5>
                                    <small class="text-muted p-t-30 db">Discount Type</small><h5>{{ucfirst($code->amount_type)}}</h5>
                                    <small class="text-muted p-t-30 db">Coupon Type</small><h5>{{ucfirst($code->discount_type)}}</h5>
                                    @if($code->discount_type == 'rides')
                                        <small class="text-muted p-t-30 db">No. of Rides</small><h5>{{$code->no_of_rides}}</h5>
                                    @endif
                                    @if($code->discount_type == 'usage')
                                        <small class="text-muted p-t-30 db">Minimum Rides to use this coupon</small><h5>{{$code->min_rides}}</h5>
                                    @endif
                                    <small class="text-muted p-t-30 db">Discount Value (in @if($code->amount_type == 'percent') % @else {{session()->get('currency')}}@endif)</small><h5>{{$code->discount_value}}</h5>
                                    <small class="text-muted p-t-30 db">No. Of times user can use this coupon</small><h5>{{$code->no_of_applies}}</h5>
                                    <small class="text-muted p-t-30 db">Created On</small><h5>{{date('Y M, d', strtotime($code->created_at))}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="description" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <h5>{!! $code->description !!}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="terms" role="tabpanel">
                            <div class="card-body">
                                <div>
                                    <h5>{!! $code->terms !!}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#users" role="tab">Users</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="users" role="tabpanel">
                            <div class="card-body">
                                @if(count($code->bookings) > 0)
                                    <table id="codesTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Ride</th>
                                                <th>Ride Amount</th>
                                                <th>Discount Value</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>User</th>
                                                <th>Ride</th>
                                                <th>Ride Amount</th>
                                                <th>Discount Value</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach($code->bookings as $ride)

                                                <tr>
                                                    <td><small class="db">{{$ride->user['name']}}</small>
                                                    <a href="/admin/user/{{$ride->user['id']}}" target="_blank"><h5 class="text-primary">{{$ride->user['email']}}</h5></a></td>
                                                    <td><a href="/admin/ride/{{$ride->id}}" target="_blank"><h5 class="text-primary">{{$ride->booking_code}}</h5></a></td>
                                                    <td>{{session()->get('currency')}} {{$ride->total}}</td>
                                                    <td>{{session()->get('currency')}} {{$ride->promo_deduct}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h4 class="text-themecolor">This Coupon Code has not been used yet.</h4>
                                @endif
                            </div>
                        </div>
                    </div>
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
