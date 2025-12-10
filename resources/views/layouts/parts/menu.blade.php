<header>
    <div class="tg-header__top">
        <div class="container custom-container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="tg-header__top-delivery">
                        <p>Express delivery and free returns within 24 hours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="header-fixed-height"></div>
    <div id="sticky-header" class="tg-header__area">
        <div class="container custom-container">
            <div class="row">
                <div class="col-12 container ">
                    <div class="tgmenu__wrap">
                        <nav class="tgmenu__nav">
                            <div class="logo">
                                <a href="{{ url('/') }}"><img src="{{ asset('assets/img/logo/logo-asad.svg') }}"
                                        alt="Logo"></a>
                            </div>

                            <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex">
                                <ul class="navigation">
                                    <li class="{{ Request::is('/') ? 'active' : '' }}">
                                        <a href="{{ url('/') }}">{{ __('Home') }}</a>
                                    </li>

                                    <li class="{{ Request::is('about') ? 'active' : '' }}">
                                        <a href="{{ url('/about') }}">{{ __('About Us') }}</a>
                                    </li>

                                    <li class="{{ Request::is('project') ? 'active' : '' }}">
                                        <a href="{{ url('/project') }}">{{ __('Portfolio') }}</a>
                                    </li>

                                    <li class="{{ Request::is('our-services') ? 'active' : '' }}">
                                        <a href="{{ url('/our-services') }}">{{ __('Our Services') }}</a>
                                    </li>

                                    <li class="{{ Request::is('contact') ? 'active' : '' }}">
                                        <a href="{{ url('/contact') }}">{{ __('Contact Us') }}</a>
                                    </li>

                                </ul>
                            </div>

                            <div class="tgmenu__action">
                                <ul class="list-wrap">
                                    <li class="header-btn">
                                        <a href="{{ url('/contact') }}" class="btn border-btn">Book Your
                                            Service</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="mobile-nav-toggler"><i class="tg-flaticon-menu-1"></i></div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.parts.menu-mobile')
</header>
