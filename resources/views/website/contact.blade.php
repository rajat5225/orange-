@extends('website.layouts.app')

@section('content')
	<div class="contact-page wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="sec-title clr-black text-center"> Contact Us</h1>

                </div>
            </div>
            <div class="row">
                   <div class="col-lg-12 col-md-12">
                    <div class="formContent bg-white ">
                        <div class="thnx-block">
                            <h3>BHATIYANI ENTERPRISE PRIVATE LIMITED</h3>
                            <div class="adress">
                                <p class="text-uppercase"><strong>ADDRESS</strong></p>
                                <p>No 5-6,Lower Ground OK Plus Mall, Madanganj Kishangarh Ajmer 305801</p>
                            </div>
                            <div class="contact-phone">
                                <p class="text-uppercase"><strong>call us on</strong></p>
                                <p>Mobile No :- 9925311247</p>
                            </div>
                            <div class="email">
                                <p class="text-uppercase"><strong>Email</strong></p>
                                <p>NXG Charge@info.com</p>
                            </div>
                            <!-- <a href="home.html">Back to home</a> -->
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