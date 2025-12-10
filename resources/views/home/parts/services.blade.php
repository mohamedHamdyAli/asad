<section class="cta__area cta__area-two fix homepage">
    <div class="cta__bg" data-background="{{ getImageassetWebsiteUrl(getSettingValue('home_Services_Banner')) }}"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <div class="cta__content-two">
                    <h2 class="title">{{ __('services_title') }}</h2>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="cta__btn">
                    <a href="{{ url('/our-services') }}" class="btn btn-two">
                        {{ __('View Our Services') }} <img src="{{ asset('assets/img/icons/long_right_arrow.svg') }}"
                            alt="" class="injectable" />
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="cta__shape">
        <img src="{{ asset('assets/img/images/cta_shape.png') }}" alt="shape" data-aos="fade-down-left"
            data-aos-delay="400" class="aos-init aos-animate">
    </div>
</section>
