@extends('website.layouts.app')

@section('content')
	<div class="about-page faq-page wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="sec-title clr-black text-center">Frequently Asked Questions</h1>
                    <p class="font24 content-clr light text-center">{!! $page->content !!}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="faqContent">
                        <div id="accordion">
                            @php
                                $i = 1;
                            @endphp
                            @foreach($faqs as $faq)
                                <div class="card">
                                    <div class="card-header" id="heading{{$i}}">
                                        <h5 class="mb-0">
                                            <a class="btn btn-link clr-black font16 bold" data-toggle="collapse" data-target="#collapse{{$i}}" aria-expanded="@if($i == 1) true @else false @endif" aria-controls="collapse{{$i}}">
                                                {!! $faq->question !!}
                                            </a>
                                        </h5>
                                    </div>

                                    <div id="collapse{{$i}}" class="collapse @if($i == 1) show @endif" aria-labelledby="heading{{$i}}" data-parent="#accordion">
                                        <div class="card-body">
                                            {!! $faq->answer !!}
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </div>
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