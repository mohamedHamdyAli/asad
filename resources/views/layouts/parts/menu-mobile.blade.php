<!-- Mobile Menu  -->
<div class="tgmobile__menu">
    <nav class="tgmobile__menu-box">
        <div class="close-btn"><i class="tg-flaticon-close-1"></i></div>
        <div class="nav-logo">
            <a href="{{ url('/') }}"><img src="{{ asset('assets/img/logo/logo-asad.svg') }}" alt="Logo"></a>
        </div>
        <div class="tgmobile__search">
            <form action="#">
                <input type="text" placeholder="Search here..." />
                <button><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="tgmobile__menu-outer">
            <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->

            
        </div>
        <div class="social-links">
            <ul class="list-wrap">
                <li><a href="{{ getSettingValue('Facebook_Url') }}" target="_blank"><i
                            class="fab fa-facebook-f"></i></a></li>
                <li><a href="{{ getSettingValue('Twitter_Url') }}" target="_blank"><i class="fab fa-twitter"></i></a>
                </li>
                <li><a href="{{ getSettingValue('LinkedIn_Url') }}" target="_blank"><i
                            class="fab fa-linkedin-in"></i></a></li>
                <li><a href="{{ getSettingValue('Instagram_Url') }}" target="_blank"><i class="fab fa-youtube"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<div class="tgmobile__menu-backdrop"></div>
<!-- End Mobile Menu -->

<!-- header-search -->
<div class="search__popup">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="search__wrapper">
                    <div class="search__close">
                        <button type="button" class="search-close-btn">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 1L1 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <path d="M1 1L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="search__form">
                        <form action="#">
                            <div class="search__input">
                                <input class="search-input-field" type="text" placeholder="Type keywords here" />
                                <span class="search-focus-border"></span>
                                <button>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M9.55 18.1C14.272 18.1 18.1 14.272 18.1 9.55C18.1 4.82797 14.272 1 9.55 1C4.82797 1 1 4.82797 1 9.55C1 14.272 4.82797 18.1 9.55 18.1Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path d="M19.0002 19.0002L17.2002 17.2002" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="search-popup-overlay"></div>
<!-- header-search-end -->

<!-- offCanvas-menu -->
<div class="offCanvas__info">
    <div class="offCanvas__close-icon menu-close">
        <button><i class="far fa-window-close"></i></button>
    </div>
    <div class="offCanvas__logo mb-30">
        <a href="index.html"><img src="{{ asset('assets/img/logo/w_logo-asad.svg') }}" alt="Logo" /></a>
    </div>
    <div class="offCanvas__side-info mb-30">
        <div class="contact-list mb-30">
            <h4>{{ __('Office_Address') }}</h4>
            <p>
                {{ getLocalizedSettingValue('Location') }}
            </p>
        </div>
        <div class="contact-list mb-30">
            <h4>{{ __('Phone_Number') }}</h4>
            <p>{{ getLocalizedSettingValue('Phone') }}</p>
        </div>
        <div class="contact-list mb-30">
            <h4>{{ __(key: 'Email_Address') }}</h4>
            <p>{{ getLocalizedSettingValue('Email') }}</p>
        </div>
    </div>
    <div class="offCanvas__social-icon mt-30">
        <a href="{{ getSettingValue('Facebook_Url') }}"><i class="fab fa-facebook-f"></i></a>
        <a href="{{ getSettingValue('Twitter_Url') }}"><i class="fab fa-twitter"></i></a>
        <a href="{{ getSettingValue('Instagram_Url') }}"><i class="fab fa-instagram"></i></a>
    </div>
</div>
<div class="offCanvas__overly"></div>
<!-- offCanvas-menu-end -->
