<!-- about-area -->
<section class="about__area-two section-py-120">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="about__img-wrap-two">
                    <img src="{{ asset('assets/img/images/inner_about01.jpg') }}" alt="img" class="wow img-custom-anim-right animated" data-wow-duration="1.5s" data-wow-delay="0.2s">
                    <img src="{{ asset('assets/img/images/inner_about02.jpg') }}" alt="img" class="wow img-custom-anim-left animated" data-wow-duration="1.5s" data-wow-delay="0.4s">
                    <img src="{{ asset('assets/img/images/inner_about03.jpg') }}" alt="img" class="wow img-custom-anim-top animated" data-wow-duration="1.5s" data-wow-delay="0.6s">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about__content-two">
                    <div class="section__title section__title-three mb-25">
                        <span class="sub-title">{{ __('More About Us') }}</span>
                        <h2 class="title">{{ __('Our mission is save people from pollution') }}</h2>
                    </div>
                    <p>{{ __('Consequat risus cum congue facilisis in egestas etiam senectus...') }}</p>
                    <div class="experience__wrap">
                        <div class="experience__box">
                            <h3 class="title">{{ __('25+') }}</h3>
                            <span>{{ __('Years') }} <br> {{ __('Experience') }}</span>
                        </div>
                        <div class="about__list-box-two">
                            <ul class="list-wrap">
                                <li>{{ __('Eamlessly conceptualize go forward total linkage') }}</li>
                                <li>{{ __('Whiteboard multifuional applications rather than') }}</li>
                                <li>{{ __('Applications rather than lived reliable functionale') }}</li>
                                <li>{{ __('Leverage other quality ideas synestic outsourcing') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="about__content-bottom">
                        <div class="about__customer-box">
                            <h4 class="title">{{ __('7.2M+ User') }}</h4>
                            <ul class="list-wrap customer">
                                <li><img src="{{ asset('assets/img/images/author01.png') }}" alt="img"></li>
                                <li><img src="{{ asset('assets/img/images/author02.png') }}" alt="img"></li>
                                <li><img src="{{ asset('assets/img/images/author03.png') }}" alt="img"></li>
                                <li><img src="{{ asset('assets/img/images/author04.png') }}" alt="img"></li>
                            </ul>
                        </div>
                        <a href="{{ url('services') }}" class="btn btn-two">{{ __('Book Your Service') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- about-area-end -->
