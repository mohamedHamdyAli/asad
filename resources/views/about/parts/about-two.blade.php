{{-- <section class="about__area-three section-py-120">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6 col-md-9 order-0 order-lg-2">
                <div class="about__img-three">
                    <img src="{{ getImageassetWebsiteUrl(getSettingValue('about_more_about_us')) ?? asset('assets/img/images/inner_about04.jpg') }}"
                        alt="img" class="wow img-custom-anim-right animated" data-wow-duration="1.5s"
                        data-wow-delay="0.2s">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about__content-three">
                    <div class="section__title section__title-three mb-30">
                        <span class="sub-title">{{ __('More About Us') }}</span>
                        <h2 class="title">{{ __('Wonderful visionary dream we see for our client’s') }}</h2>
                    </div>
                    <p>{{ __('Nec platea est metus imperdiet litora natoque eros posuere ac venenatis...') }}</p>
                    <div class="counter__item-wrap-two">
                        <div class="counter__item-two">
                            <h2 class="count"><span class="odometer" data-count="216"></span>+</h2>
                            <div class="icon"><i class="asad-glove"></i></div>
                            <h4 class="title">{{ __('Global Country') }}</h4>
                        </div>
                        <div class="counter__item-two">
                            <h2 class="count"><span class="odometer" data-count="692"></span>+</h2>
                            <div class="icon"><i class="asad-rank"></i></div>
                            <h4 class="title">{{ __('Company Growth') }}</h4>
                        </div>
                        <div class="counter__item-two">
                            <h2 class="count"><span class="odometer" data-count="12"></span>K</h2>
                            <div class="icon"><i class="asad-feedback"></i></div>
                            <h4 class="title">{{ __('Satisfied Customers') }}</h4>
                        </div>
                        <div class="counter__item-two">
                            <h2 class="count"><span class="odometer" data-count="99"></span>%</h2>
                            <div class="icon"><i class="asad-happy"></i></div>
                            <h4 class="title">{{ __('Customer Rating') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}
<section class="about__area section-pb-120">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-9" style="margin: auto">
                <div class="section__title mb-20">
                    <h2 class="title" style="text-align: center;">
                        {{ __('Mission, vision and Values') }}
                    </h2>
                    <h4 class="number">{{ __('Our') }}</h4>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="about__content">
                    <div class="about__list-box-wrap">
                        <div class="about__list-box aos-init aos-animate col-lg-4" data-aos="fade-up"
                            data-aos-delay="400">
                            <div class="content">
                                <div class="icon">
                                    <img src="{{ asset('assets/img/icons/shared-vision.png') }}">
                                </div>
                                <h4 class="title">{{ __('VISION') }}</h4>
                                <p>{{ __('Be the MENA’s premier engineering, construction and project Management Company') }}
                                </p>
                            </div>
                        </div>
                        <div class="about__list-box aos-init aos-animate col-lg-4" data-aos="fade-up"
                            data-aos-delay="600">
                            <div class="content">
                                <div class="icon">
                                    <img src="{{ asset('assets/img/icons/mission.png') }}">
                                </div>
                                <h4 class="title">{{ __('MISSION') }}</h4>
                                <ul>
                                    <li>
                                        <p>{{ __('Add value for all our stakeholders') }}</p>
                                    </li>

                                    <li>
                                        <p>{{ __('Attain astonishing outcomes for our clients') }}</p>
                                    </li>
                                    <li>
                                        <p>{{ __('Build satisfying careers for our people') }}</p>
                                    </li>
                                    <li>
                                        <p>{{ __('Earn a fair return on the value we provide') }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="about__list-box aos-init aos-animate col-lg-4" data-aos="fade-up"
                            data-aos-delay="600">
                            <div class="content">
                                <div class="icon">

                                    <img src="{{ asset('assets/img/icons/diamond.png') }}">
                                </div>
                                <h4 class="title">{{ __('Values') }}</h4>
                                <div class="values">
                                    <ul>
                                        <li>
                                            <p>{{ __('Ethics') }}</p>
                                        </li>
                                        <li>
                                            <p>{{ __('Safety & Health') }}</p>
                                        </li>
                                        <li>
                                            <p>{{ __('Quality') }}</p>
                                        </li>
                                        <li>
                                            <p>{{ __('People') }}</p>
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            <p>{{ __('Culture') }}</p>
                                        </li>
                                        <li>
                                            <p>{{ __('Relationships') }}</p>
                                        </li>
                                        <li>
                                            <p>{{ __('Innovation') }}</p>
                                        </li>
                                        <li>
                                            <p>{{ __('Sustainability') }}</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
