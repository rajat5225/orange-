<footer class="wrapper bg-darkGray">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12">
				<div class="footerNavs">
					<div class="row">
						<div class="col-lg-8 col-md-4 col-sm-12">
							<ul class="ftrLinks d-flex align-item-center">
								<li>
									<a href="{{route('about')}}" class="font16 normal">About Us</a>
								</li>
								<li>
									<a href="{{route('faq')}}" class="font16 normal">FAQ</a>
								</li>
								<li>
									<a href="{{route('terms')}}" class="font16 normal">Terms & Conditions</a>
								</li>
								<li>
									<a href="{{route('privacy')}}" class="font16 normal">Privacy Policy</a>
								</li>
								<li>
									<a href="{{route('refund')}}" class="font16 normal">Refund Policy</a>
								</li>
								<li>
									<a href="{{route('contact')}}" class="font16 normal">Contact Us</a>
								</li>
							</ul>
						</div>
						<div class="col-lg-2 col-md-4 col-sm-12">
							<div class="becomeDriver text-center">
								<a href="{{route('mobileVerify')}}" class="btn-red">Become Driver</a>
							</div>

						</div>
						<div class="col-lg-2 col-md-4 col-sm-12">
							<div class="ftrsocial d-flex justify-content-end">
								<ul class="">
		                            <li>
		                                <a href="{{$social[1]->rule_value}}" class="font15" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>
		                            </li>
		                            <li>
		                                <a href="{{$social[0]->rule_value}}"" class="font15" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a>
		                            </li>
		                            <li>
		                                <a href="{{$social[2]->rule_value}}"" class="font15" target="_blank"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
		                            </li>
		                            <li>
		                                <a href="{{$social[3]->rule_value}}"" class="font15" target="_blank"><i class="fa fa-instagram"></i></a>
		                            </li>
		                        </ul>
							</div>
						</div>
					</div>
				</div>
				<div class="copyApp text-center">
					<div class="applinks">
						<a href="https://play.google.com/store/apps/details?id=com.NXG Charge&hl=en" target="_blank"><img src="{{URL::asset('/images/gplay.png')}}" alt=""></a>
						{{-- <a href="#!"><img src="{{URL::asset('/images/macapp.png')}}" alt=""></a> --}}
					</div>
					<p>Copyright © 2018 NXG Charge. All rights reserved.</p>
				</div>
			</div>
		</div>
	</div>
</footer>