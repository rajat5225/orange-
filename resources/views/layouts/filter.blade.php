<?php
$users = App\Model\User::select("users.name", "users.id")->join("bookings", "users.id", "=", "bookings.user_id")->groupBy("bookings.user_id")->get();
$drivers = App\Model\User::select("users.name", "users.id")->join("bookings", "users.id", "=", "bookings.driver_id")->groupBy("bookings.driver_id")->get();

?>
<div class="row">
    <div class="col-12">
        <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Filters</h4>
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" class="form-control" placeholder="Enter from date" value='{{ (isset($_GET['from_date'])) ? ($_GET["from_date"]) ? $_GET["from_date"] : "" : "" }}'>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" placeholder="Enter end date" value='{{ (isset($_GET['end_date'])) ? ($_GET["end_date"]) ? $_GET["end_date"] : "" :"" }}'>
                            </div>
                        </div>
                    @if(Request::segment(2)=="rides" || Request::segment(2)=="transactions")
                        <div class="col-3">
                            <div class="form-group">
                                <label for="user">Users</label>
                                <select class="form-control user" name="user">
                                    <option value="">Select User</option>
                                    @if(!empty($users))
                                    @foreach($users as $item)
                                        <option value="{{ $item->id }}" {{ (isset($_GET['user'])) ? ($_GET['user']==$item->id) ? "selected" : "" : "" }}>{{ $item->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    @endif
                    @if(Request::segment(2)=="rides")
                        <div class="col-3">
                            <div class="form-group">
                                <label for="driver">Drivers</label>
                                <select class="form-control driver" name="driver">
                                    <option value="">Select Driver</option>
                                    @if(!empty($drivers))
                                    @foreach($drivers as $item)
                                        <option value="{{ $item->id }}" {{ (isset($_GET['driver'])) ? ($_GET['driver']==$item->id) ? "selected" : "" : "" }}>{{ $item->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="booking_status">Booking Status</label>
                                <select class="form-control booking_status" name="booking_status">
                                    <option value="" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']=="") ? "selected" : "" : "" }}>Select Booking Status</option>
                                    <option value="0" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==0) ? "selected" : "" : "" }}>Declined</option>
                                    <option value="1" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==1) ? "selected" : "" : "" }}>Accepted</option>
                                    <option value="2" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==2) ? "selected" : "" : "" }}>Arrived</option>
                                    <option value="3" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==3) ? "selected" : "" : "" }}>Start</option>
                                    <option value="5" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==5) ? "selected" : "" : "" }}>End</option>
                                    <option value="7" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==7) ? "selected" : "" : "" }}>Finished</option>
                                    <option value="8" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==8) ? "selected" : "" : "" }}>Pending</option>
                                    <option value="4" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==4) ? "selected" : "" : "" }}>Cancelled</option>
                                    <option value="6" {{ (isset($_GET['booking_status'])) ? ($_GET['booking_status']==6) ? "selected" : "" : "" }}>Scheduled</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    @if(Request::segment(2)=="users" || Request::segment(2)=="drivers")
                        <div class="col-3">
                            <div class="form-group">
                                <label for="booking_status">User / Driver Status</label>
                                <select class="form-control user_status" name="user_status">
                                    <option value="">Select Status</option>
                                    <option value="9" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==9) ? "selected" : "" : "" }}>Available</option>
                                    <option value="0" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==0) ? "selected" : "" : "" }}>Declined</option>
                                    <option value="1" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==1) ? "selected" : "" : "" }}>Accepted</option>
                                    <option value="2" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==2) ? "selected" : "" : "" }}>Arrived</option>
                                    <option value="3" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==3) ? "selected" : "" : "" }}>Start</option>
                                    <option value="5" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==5) ? "selected" : "" : "" }}>End</option>
                                    <option value="7" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==7) ? "selected" : "" : "" }}>Finished</option>
                                    <option value="8" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==8) ? "selected" : "" : "" }}>Pending</option>
                                    <option value="4" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==4) ? "selected" : "" : "" }}>Cancelled</option>
                                    <option value="6" {{ (isset($_GET['user_status'])) ? ($_GET['user_status']==6) ? "selected" : "" : "" }}>Scheduled</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    @if(Request::segment(2)=="transactions")
                        <div class="col-3">
                            <div class="form-group">
                                <label for="transaction_status">Transaction Status</label>
                                <select class="form-control transaction_status" name="transaction_status">
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ (isset($_GET['transaction_status'])) ? ($_GET['transaction_status']=="pending") ? "selected" : "" : "" }}>pending</option>
                                    <option value="failed" {{ (isset($_GET['transaction_status'])) ? ($_GET['transaction_status']=="failed") ? "selected" : "" : "" }}>failed</option>
                                    <option value="success" {{ (isset($_GET['transaction_status'])) ? ($_GET['transaction_status']=="success") ? "selected" : "" : "" }}>success</option>
                                </select>
                            </div>
                        </div>
                    @endif

                        <div class="col-3">
                            <div class="form-group" style="margin-top: 8%">
                                <input type="submit" class="btn btn-success" value="Filter">
                                <a href="{{ URL::to('/') }}/{{ Request::segment(1) }}/{{ Request::segment(2) }}" class="btn waves-effect waves-light btn-primary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
