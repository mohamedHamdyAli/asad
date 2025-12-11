    @php
        $languages = \App\Models\Language::getLanguageList();
        $firstLanguage = $languages->first();
        $locale = Session::get('locale');
        $locale_name = Session::get('locale_name');
        $locale_flag = Session::get('locale_flag');
    @endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.parts.head-css')
    @include('layouts.parts.head-scripts')
</head>

<body>

    <!--Preloader-->
    <div id="preloader">
        <div id="loader" class="loader">
            <div class="loader-container">
                <div class="loader-icon"><img src="{{ asset('assets/img/logo/preloader.svg') }}" alt="Preloader"></div>
            </div>
        </div>
    </div>
    <!--Preloader-end -->

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="asad-up-arrow"></i>
    </button>
    <!-- Scroll-top-end-->

    @include('layouts.parts.menu')

    <main class="main-area fix">
        @yield('content')
    </main>

    @include('layouts.parts.footer')
    @include('layouts.parts.script')

</body>

</html>
