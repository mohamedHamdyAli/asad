<header>
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

                                    <li class="menu-item-has-children">
                                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                                            href="#" id="langDropdown" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">

                                            <img src="{{ getImageassetUrl($locale_flag ?? $firstLanguage->icon) }}"
                                                class="lang-flag" alt="{{ $locale_name ?? $firstLanguage->name }}"
                                                style="width:20px;">

                                            <span class="lang-text">
                                                {{ $locale_name ?? $firstLanguage->name }}
                                            </span>

                                        </a>

                                        <ul class="sub-menu" aria-labelledby="langDropdown" style="min-width: 180px;">

                                            @foreach ($languages as $language)
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 @if ($locale && $locale === $language->code) active-language @endif"
                                                        href="{{ url("lang/{$language->code}") }}">

                                                        <img src="{{ getImageassetUrl($language->icon) }}"
                                                            style="width:20px;" alt="{{ $language->name }}">

                                                        {{ $language->name }}

                                                        @if ($locale && $locale === $language->code)
                                                            <span class="ms-auto text-success fw-bold">✔</span>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>

                                </ul>
                            </div>

                            <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-xl-flex lang-selector">
                                <ul class="navigation">
                                    {{-- language switcher --}}
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                                            href="#" id="langDropdown" role="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">

                                            <img src="{{ getImageassetUrl($locale_flag ?? $firstLanguage->icon) }}"
                                                class="lang-flag" alt="{{ $locale_name ?? $firstLanguage->name }}"
                                                style="width:20px;">

                                            <span class="lang-text">
                                                {{ $locale_name ?? $firstLanguage->name }}
                                            </span>

                                        </a>

                                        <ul class="sub-menu dropdown-menu" aria-labelledby="langDropdown"
                                            style="min-width: 180px;">

                                            @foreach ($languages as $language)
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 @if ($locale && $locale === $language->code) active-language @endif"
                                                        href="{{ url("lang/{$language->code}") }}">

                                                        <img src="{{ getImageassetUrl($language->icon) }}"
                                                            style="width:20px;" alt="{{ $language->name }}">

                                                        {{ $language->name }}

                                                        @if ($locale && $locale === $language->code)
                                                            <span class="ms-auto text-success fw-bold">✔</span>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>

                            </div>
                            <div class="tgmenu__action">
                                <ul class="list-wrap">

                                    <li class="header-btn">
                                        <a href="{{ url('/contact') }}"
                                            class="btn border-btn">{{ __('Book Your Service') }}</a>
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
