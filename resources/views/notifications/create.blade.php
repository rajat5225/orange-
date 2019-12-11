@extends('layouts.app')
@section('title', 'Send Notification')

@section("css")

    <link href="{{ asset('assets/plugins/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<form action="{{ route("storeNotification") }}" method="get" enctype="multipart/form-data" id="form">
{{csrf_field()}}

{{-- @include("layouts.filter") --}}


<div class="row">
    <div class="col-lg-12">
        @if(session("success"))
            @alert(["type" => "alert-success"])
                {{ session("success") }}
            @endalert
        @elseif(session("danger"))
            @alert(["type" => "alert-danger"])
                {{ session("danger") }}
            @endalert
        @endif

        <div class="card">
            <div class="card-header bg-info">
                <h4 class="m-b-0 text-white">Send Notification</h4>
            </div>
            <div class="card-body">
                    <div class="form-body">
                        @if($user_id==0)
                        <div class="row p-t-20">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_type">User Type</label>
                                 {{ Form::select("user_type",["all"=>"All","user"=>"User","driver"=>"Driver"],(isset($_GET['user_type'])) ? $_GET['user_type'] : "",["class"=>"form-control","id"=>"user_type"]) }}
                                </div>
                            </div>
                        </div>
                        @else
                            <input type="hidden" name="user_type" value="single">
                        @endif
                        
                        <div class="row p-t-20 custom_notification">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Notification Title</label>
                                    <input type="text" name="title" class="form-control noti_validate" required>
                                    <input type="hidden" name="user_id" value="{{$user_id}}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row event_notification">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea name="description" class="form-control event_validate" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn waves-effect waves-light btn-success"> <i class="fa fa-check"></i> Send</button>
                    </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->


</form>
@endsection

@push('scripts')

	<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

 <!-- end - This is for export functionality only -->
    <script>
    $('#example23').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "order": [
            [0, 'desc']
        ],
        "columnDefs":[
           {"visible": false, "targets":0}
        ]
    });
    $('.clockpicker').clockpicker({
        donetext: 'Done',
    }).find('input').change(function() {
        console.log(this.value);
    });

    jQuery('.mydatepicker, #datepicker').datepicker();
    jQuery('#datepicker-autoclose').datepicker({
        autoclose: true,
        todayHighlight: true
    });
    jQuery('#date-range').datepicker({
        toggleActive: true
    });

    </script>
@endpush