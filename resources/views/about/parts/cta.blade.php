<section class="cta__area cta__area-two fix">
    <div class="cta__bg" data-background="{{ asset('assets/img/bg/cta_bg.jpg') }}"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="cta__content-two">
                    <h2 class="title">{{ __('Looking for qualified professionals to do renovations to your existing home in Los Angeles?') }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="cta__shape">
        <img src="{{ getImageassetWebsiteUrl(getSettingValue('about_banner')) ?? asset('assets/img/images/cta_shape.png') }}" alt="shape" data-aos="fade-down-left" data-aos-delay="400">
    </div>
</section>
