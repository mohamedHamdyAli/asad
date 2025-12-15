<!-- footer-area -->
<footer class="footer__area footer__area-two">
    <div class="container">
        <div class="footer__top footer__top-two">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                    <div class="footer__widget">
                        <div class="footer__logo">
                            <a href="{{ url('/') }}"><img src="{{ asset('assets/img/logo/w_logo-asad.svg') }}" alt="logo"></a>
                        </div>
                        <div class="footer__content">
                            <p>{{ getLocalizedSettingValue('About') }}</p>
                        </div>
                        <div class="footer__social">
                            <ul class="list-wrap">
                                <li><a href="{{ getSettingValue('Facebook_Url') }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="{{ getSettingValue('Twitter_Url') }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="{{ getSettingValue('LinkedIn_Url') }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="{{ getSettingValue('Instagram_Url') }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-3 col-sm-4">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title">{{ __('Useful Links') }}</h4>
                        <div class="footer__widget-link">
                            <ul class="list-wrap">
                                <li><a href="{{ url('about') }}"><i class="asad-right-arrow"></i><span>{{ __('About Us') }}</span></a></li>
                                <li><a href="{{ url('services') }}"><i class="asad-right-arrow"></i><span>{{ __('Our Services') }}</span></a></li>
                                <li><a href="{{ url('project') }}"><i class="asad-right-arrow"></i><span>{{ __('Our Projects') }}</span></a></li>
                                <li><a href="{{ url('contact') }}"><i class="asad-right-arrow"></i><span>{{ __('Contact Us') }}</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title">Quick Info</h4>
                        <div class="footer__widget-link">
                            <ul class="list-wrap">
                                <li><a href="{{ url('about') }}"><i class="asad-right-arrow"></i><span>{{ __('Our Story') }}</span></a></li>
                                <li><a href="{{ url('/qhse-policy') }}"><i class="asad-right-arrow"></i><span>{{ __('QHSE Policy') }}</span></a></li>
                                <li><a href="{{ url('project') }}"><i class="asad-right-arrow"></i><span>{{ __('Project Showcase') }}</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-8">
                    <div class="footer__widget">
                        <h4 class="footer__widget-title">{{ __('Newsletter') }}</h4>
                        <div class="footer__newsletter">
                            <p>{{ __('Aplications prodize before front end ortals visualize front end') }}</p>
                            <form action="#" class="footer__newsletter-form footer__newsletter-form-two">
                                <input type="email" placeholder="{{ __('Email Address') }}">
                                <button type="submit" class="btn btn-two">{{ __('Subscribe') }}</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="footer__bottom footer__bottom-two">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="copyright-text">
                        <p>{{ __('rights_reserved') }} <a href="https://nahrdev.com/">{{ __('nahrdev') }}</a></p>
                    </div>
                </div>
                {{-- <div class="col-md-5">
                    <div class="footer__bottom-menu footer__bottom-menu-two">
                        <ul class="list-wrap">
                            <li><a href="{{ url('contact') }}">Terms and Conditions</a></li>
                            <li><a href="{{ url('contact') }}">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div> --}}
            </div>
        </div>

    </div>
</footer>
<!-- footer-area-end -->
