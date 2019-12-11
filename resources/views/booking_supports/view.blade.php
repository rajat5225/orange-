@extends('layouts.app')
@section('title', 'Ride Support - '.$support->booking->user->name)

@section('content')
	<div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-primary">Ride Support</h3> </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('rideSupportRequests')}}">Ride Supports</a></li>
                    <li class="breadcrumb-item active">Ride Support</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">

            <div class="col-lg-12 col-xlg-12 col-md-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <h6 class="p-t-30 db">{{$support->booking->user->name}} sent you the request</h6><a href="/admin/user/{{$support->booking->user->id}}" target="_blank"><h6 class="text-primary">{{$support->booking->user->email}}</h6></a>
                        </div>
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-30 db">Support Code</small>
                            <h6>{{$support->support_code}}</h6>
                        </div>
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-30 db">User Name</small>
                            <h6> {{$support->booking->user->name}}</h6>
                            </div>
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-30 db">User Mobile Number</small>
                            <h6> {{$support->booking->user->mobile_number}}</h6>
                        </div>
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-30 db">Driver Name</small>
                            <h6> {{$support->booking->driver->name}}</h6>
                        </div>
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-30 db">Driver Mobile Number</small>
                            <h6> {{$support->booking->driver->mobile_number}}</h6>
                        </div>
                        <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-30 db">Support Subject</small>
                            <h6>{{$support->subject->subject}}</h6>
                        </div>
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-30 db">Message</small>
                            <h6>{{$support->comment}}</h6>
                            </div>
                            <div class="col-lg-4 col-xlg-4 col-md-4">
                            <small class="p-t-20 db">Status</small>
                            @if($type == 'view')
                                <h6>{{ucfirst(config('constants.STATUS.'.$support->status))}}</h6>
                            @else
                                <select class="form-control col-md-4 m-t-10" name="status" id="status">
                                    @foreach(config('constants.STATUS') as $key => $status)
                                        <option value="{{$key}}" @if($support->status==$key) selected @endif>{{$status}}</option>
                                    @endforeach
                                </select>
                            @endif
                            @if($type != 'view')
                            </div>
                            <div class="col-lg-12 col-xlg-12 col-md-12">
                            <small class="p-t-20 db">Your Reply</small>
                            <div contenteditable="false" placeholder="Send a Reply" class="supportReply m-t-10 m-b-40 p-10" id="click2edit_{{$support->id}}">
                                {!! $support->reply !!}
                            </div>
                            <button id="reply_{{$support->id}}" class="btn btn-info replyBtn btn-rounded m-t-10" type="button">Reply</button>
                            <button id="send_{{$support->id}}" class="btn btn-success sendBtn btn-rounded m-t-10" type="button">Send</button>
                            <button id="cancel_{{$support->id}}" class="btn btn-warning cancelBtn btn-rounded m-t-10" type="button" style="display: none;">Cancel</button>
                        </div>
                           @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function(){
            @if($type == 'edit')
                $(".supportReply").addClass('editableDiv');
                $(".supportReply").attr('contenteditable', true);
                $('.replyBtn').hide();
                $('.cancelBtn').show();
            @endif

            $('.replyBtn').click(function(){
                var id = $(this).attr('id').split('_')[1];
                // $("#click2edit_"+id).summernote();
                $(".supportReply").addClass('editableDiv');
                $(".supportReply").attr('contenteditable', true);
                $('#reply_'+id).hide();
                $('#cancel_'+id).show();
            });

            $('.cancelBtn').click(function(){
                var id = $(this).attr('id').split('_')[1];
                // $("#click2edit_"+id).summernote('destroy');
                $(".supportReply").removeClass('editableDiv');
                $(".supportReply").attr('contenteditable', false);
                $('#reply_'+id).show();
                $('#cancel_'+id).hide();
            });

            $('.sendBtn').click(function(){
                var id = $(this).attr('id').split('_')[1];
                $("#send_"+id).prop('disabled', true);
                $("#cancel_"+id).prop('disabled', true);
                if($('#status').is(":visible"))
                    status = $('#status').val();
                else
                    status = '';
                $.ajax({
                    url: "{{route('replyRideSupportRequest')}}"+'/'+id,
                    type: "POST",
                    data: {content: $('.supportReply').text(), status: status},
                    success: function(res){
                        var data = JSON.parse(res);
                        if(data.status == 1)
                        {
                            $('#status').val(status);
                            // $("#click2edit_"+id).summernote('destroy');
                            $(".supportReply").removeClass('editableDiv');
                            $(".supportReply").attr('contenteditable', false);
                            $('#reply_'+id).show();
                            $('#cancel_'+id).hide();
                            $("#send_"+id).prop('disabled', false);
                            $("#cancel_"+id).prop('disabled', false);
                            toastr.success(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                        else
                        {
                            toastr.error(data.message,"Status",{
                                timeOut: 5000,
                                "closeButton": true,
                                "debug": false,
                                "newestOnTop": true,
                                "progressBar": true,
                                "positionClass": "toast-top-right",
                                "preventDuplicates": true,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut",
                                "tapToDismiss": false

                            });
                        }
                    }
                })

            });
        });

    </script>
@endpush
