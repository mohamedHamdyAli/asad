<!-- about-area -->
<section class="about__area-five grey-bg-two section-py-120">
    <div class="container">
        <div class="row gutter-24 align-items-center justify-content-center">
            <div class="col-lg-6 col-md-9">
                <div class="about__img-wrap-five">
                    <img src="{{ getImageassetWebsiteUrl(getSettingValue('about_inner_about01')) ?? asset('assets/img/images/inner_about01.jpg') }}"
                        alt="img" class="wow img-custom-anim-right animated" data-wow-duration="1.5s"
                        data-wow-delay="0.2s">
                    <img src="{{ getImageassetWebsiteUrl(getSettingValue('about_inner_about02')) ?? asset('assets/img/images/inner_about02.jpg') }}"
                        alt="img" class="wow img-custom-anim-left animated" data-wow-duration="1.5s"
                        data-wow-delay="0.4s">
                    <img src="{{ getImageassetWebsiteUrl(getSettingValue('about_inner_about03')) ?? asset('assets/img/images/inner_about03.jpg') }}"
                        alt="img" class="wow img-custom-anim-top animated" data-wow-duration="1.5s"
                        data-wow-delay="0.6s">
                    <div class="experience__box-two wow img-custom-anim-top animated" data-wow-duration="1.5s"
                        data-wow-delay="0.8s">
                        <h2 class="title">{{ __('number_of_Experience') }}</h2>
                        <span>{{ __('Years') }} <br> {{ __('Of Experience') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about__content-five">
                    <div class="section__title section__title-three mb-30">
                        <span class="sub-title">{{ __('About Our Company') }}</span>
                        <h2 class="title">{{ __('About_Title') }}</h2>
                    </div>
                    <p>{{ __('About_Desc') }}</p>
                    <ul class="list-wrap banner__list-box">
                        <li><i class="fas fa-check"></i>{{ __('Point1') }}</li>
                        <li><i class="fas fa-check"></i>{{ __('Point2') }}</li>
                        <li><i class="fas fa-check"></i>{{ __('Point3') }}</li>
                    </ul>
                    <div class="about__content-bottom about__content-bottom-two">
                        <div class="about__customer-box">
                            <ul class="list-wrap customer">
                                <li><img src="{{ getImageassetWebsiteUrl(getSettingValue('about_inner_client01')) ?? asset('assets/img/images/author01.png') }}"
                                        alt="img"></li>
                            </ul>
                            <h4 class="title">{{ __('Loyal_Customer') }}</h4>
                        </div>
                        <a href="{{ url('about') }}" class="btn btn-two">{{ __('More_About_Us') }}
                            <img src="{{ asset('assets/img/icons/right_arrow.svg') }}" alt=""
                                class="injectable">
                        </a>
                        <span>{{ __('Customer_Posation') }}</span>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- about-area-end -->
