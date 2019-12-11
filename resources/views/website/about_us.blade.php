@extends('website.layouts.app')

@section('content')
	<div class="about-page wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="sec-title clr-black text-center">About Us</h1>
                    <p class="font24 content-clr light text-center">Redefining Mobility For Billions</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="abt-bnr">
                        <img src="{{URL::asset('/images/abt-bnr.jpg')}}" alt="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="carImg text-center">
                        <img src="{{URL::asset('/images/car.png')}}" alt="">
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="carCntent font18 normal content-clr">
                        {!! $page->content !!}
                     </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-lg-12">
                    <div class="othrInfo">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="icnImg float-left">
                                    <img src="{{URL::asset('/images/car2icon.png')}}" alt="">
                                </div>
                                <div class="othrContent">
                                    <div class="numbers font18 clr-black bold">10,000</div>
                                    <div class="font16 content-clr">
                                            Vehicles serving millions of customers everyday
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="icnImg float-left">
                                    <img src="{{URL::asset('/images/cities.png')}}" alt="">
                                </div>
                                <div class="othrContent">
                                    <div class="numbers font18 clr-black bold">50+</div>
                                    <div class="font16 content-clr">
                                            Cities serviced by the NXG Charge fleet to get you to your
                                            destination on time, every time
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="font16 content-clr moreContent">
                        <p>
                                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.
                        </p>
                        <p>
                            Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.
                        </p>
                    </div>
                </div>
            </div> -->
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