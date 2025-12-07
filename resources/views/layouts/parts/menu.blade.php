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
                                <a href="{{ url('/') }}"><img src="{{ asset('assets/img/logo/logo-asad.svg') }}" alt="Logo"></a>
                            </div>

                            <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex">
                                <ul class="navigation">
                                    <li class="active "><a href="{{ url('/') }}">Home</a></li>
                                    <li><a href="{{ url('/about') }}">About Us</a></li>
                                    <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                                    <li><a href="{{ url('/project') }}">Portfolio</a></li>
                                </ul>
                            </div>

                            <div class="tgmenu__action">
                                <ul class="list-wrap">
                                    <li class="header-btn">
                                        <a href="{{ url('/appointment') }}" class="btn border-btn">Book Your
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
