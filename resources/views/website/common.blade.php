@extends('website.layouts.app')

@section('content')
	<div class="about-page faq-page wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="sec-title clr-black text-center"> {!! $page->name !!}</h1>
                   
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="termsContent">
                        {!! $page->content !!}

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
	<script>
        $(document).ready(function(){
            $("body").addClass("innerPage")
        });
    </script>
@endpush