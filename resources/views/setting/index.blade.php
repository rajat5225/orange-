@extends('layouts.app')
@section('title', 'Setting')

@section("css")

    <link href="{{ asset('assets/plugins/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<form action="{{ route("storeSetting") }}" method="get" enctype="multipart/form-data" id="form">
{{csrf_field()}}

{{-- @include("layouts.filter") --}}


<div class="row">
    <div class="col-lg-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-info">
                <h4 class="m-b-0 text-white">Settings</h4>
            </div>
            <div class="card-body">
                    <div class="form-body">
                        <h3>Android Application Setting</h3>
                        <div class="row p-t-20">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Android User App Version</label>
                                    <input type="text" name="android_version_user" class="form-control mtg_deci" value="{{$setting[11]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Android User App URL</label>
                                    <input type="text" name="android_url_user" class="form-control" value="{{$setting[10]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Android User App Force Update</label><br>
                                    <input type="checkbox" name="android_update_user" @if($setting[9]->rule_value) checked @endif value="1" class="js-switch" data-color="rgb(26, 180, 27)" data-size="small"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Android Driver App Version</label>
                                    <input type="text" name="android_version_driver" class="form-control mtg_deci" value="{{$setting[8]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Android Driver App URL</label>
                                    <input type="text" name="android_url_driver" class="form-control" value="{{$setting[7]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Android Driver App Force Update</label><br>
                                    <input type="checkbox" name="android_update_driver" @if($setting[6]->rule_value) checked @endif value="1" class="js-switch" data-color="rgb(26, 180, 27)" data-size="small"/>
                                </div>
                            </div>
                        </div>
                        <h3>iOS Application Setting</h3>
                        <div class="row p-t-20">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">iOS User App Version</label>
                                    <input type="text" name="ios_version_user" class="form-control mtg_deci" value="{{$setting[5]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">iOS User App URL</label>
                                    <input type="text" name="ios_url_user" class="form-control" value="{{$setting[4]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">iOS User App Force Update</label><br>
                                    <input type="checkbox" name="ios_update_user" @if($setting[3]->rule_value) checked @endif value="1" class="js-switch" data-color="rgb(26, 180, 27)" data-size="small"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">iOS Driver App Version</label>
                                    <input type="text" name="ios_version_driver" class="form-control mtg_deci" value="{{$setting[2]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">iOS Driver App URL</label>
                                    <input type="text" name="ios_url_driver" class="form-control" value="{{$setting[1]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">iOS Driver App Force Update</label><br>
                                    <input type="checkbox" name="ios_update_driver" @if($setting[0]->rule_value) checked @endif value="1" class="js-switch" data-color="rgb(26, 180, 27)" data-size="small"/>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h3>GST Setting</h3>

                        <div class="row p-t-20">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">CGST (In %)</label>
                                    <input type="text" name="cgst" class="form-control mtg_deci" value="{{$setting[13]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">SGST (In %)</label><br>
                                    <input type="text" name="sgst" class="form-control mtg_deci" value="{{$setting[14]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">IGST (In %)</label>
                                    <input type="text" name="igst" class="form-control mtg_deci" value="{{$setting[15]->rule_value}}" required>
                                </div>
                            </div>
                        </div>
                            <hr>
                        <h3>Social Links</h3>

                        <div class="row p-t-20">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Twitter Link</label>
                                    <input type="text" name="twitter_social_link" class="form-control mtg_number" value="{{$setting[20]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Facebook Link</label>
                                    <input type="text" name="facebook_social_link" class="form-control mtg_deci" value="{{$setting[21]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">LinkedIn Link</label>
                                    <input type="text" name="linkedin_social_link" class="form-control" value="{{$setting[22]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Instagram Link</label>
                                    <input type="text" name="instagram_social_link" class="form-control" value="{{$setting[23]->rule_value}}" required>
                                </div>
                            </div>
                        </div>
                        <h3>Other Setting</h3>

                        <div class="row p-t-20">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Trusted Contacts Limit</label>
                                    <input type="text" name="trusted_contacts_limit" class="form-control mtg_number" value="{{$setting[16]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Referrer Amount</label>
                                    <input type="text" name="referrer_amount" class="form-control mtg_deci" value="{{$setting[17]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Refer User</label>
                                    <input type="checkbox" name="refer_user" @if($setting[18]->rule_value) checked @endif value="1" class="js-switch" data-color="rgb(26, 180, 27)" data-size="small"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Minimum Wallet Balance</label>
                                    <input type="text" name="minimum_wallet_balance" class="form-control mtg_deci" value="{{$setting[19]->rule_value}}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Application Update Message</label><br>
                                    <textarea name="app_update_msg" class="form-control" required>{{$setting[12]->rule_value}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn waves-effect waves-light btn-success"> <i class="fa fa-check"></i> Save</button>
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
$(document).on('keypress',".mtg_number",function (e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});
 $(document).on('keypress',".mtg_deci",function (e) {
     if ((e.which != 46 || $(this).val().indexOf('.') != -1) && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});
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