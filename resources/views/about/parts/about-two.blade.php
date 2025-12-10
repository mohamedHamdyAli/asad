<section class="about__area-three section-py-120">
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
</section>
<section class="about__area section-pb-120">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-9" style="margin: auto">
                <div class="section__title mb-20">
                    <h2 class="title" style="
    text-align: center;
"> Mission, vision and Values</h2>
                    <h4 class="number">Our</h4>
                </div>
            </div>
        {{-- <div class="col-lg-6 col-md-9">
                        <div class="about__img-wrap wow img-custom-anim-left  animated" data-wow-duration="1.5s" data-wow-delay="0.1s" style="visibility: visible; animation-duration: 1.5s; animation-delay: 0.1s; animation-name: img-anim-left;">
                            <img src="assets/img/images/about_img.jpg" alt="img">
                            <div class="about__img-content">
                                <h4 class="title">Let’s work together</h4>
                                <a href="contact.html" class="btn">Contact Us <img src="assets/img/icons/right_arrow.svg" alt="" class="injectable"></a>
                            </div>
                        </div>
                    </div> --}}
            <div class="col-lg-12">
                <div class="about__content">

                    <div class="about__list-box-wrap">
                        <div class="about__list-box aos-init aos-animate col-lg-4" data-aos="fade-up" data-aos-delay="400">
                          
                            <div class="content">
                                  <div class="icon">
                                {{-- <i class="asad-user-rating"></i> --}}

                                <img src="{{ asset('assets/img/icons/shared-vision.png') }}">
                            </div>
                                <h4 class="title">VISION</h4>
                                <p>Be the MENA’s premier engineering, construction and project Management Company.
                                </p>
                            </div>
                        </div>
                        <div class="about__list-box aos-init aos-animate col-lg-4" data-aos="fade-up" data-aos-delay="600">
                            {{-- <div class="icon">
                                <i class="asad-brick"></i>
                            </div> --}}
                            <div class="content">
                                  <div class="icon">
                                {{-- <i class="asad-user-rating"></i> --}}

                                <img src="{{ asset('assets/img/icons/mission.png') }}">
                            </div>
                                <h4 class="title">MISSION</h4>
                                <ul>
                                    <li>
                                        <p>Add value for all our stakeholders. </p>
                                    </li>

                                    <li>
                                        <p>Attain astonishing outcomes for our clients
                                        </p>
                                    </li>
                                    <li>
                                        <p>Build satisfying careers for our people

                                        </p>
                                    </li>
                                    <li>
                                        <p>Earn a fair return on the value we provide.

                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                         <div class="about__list-box aos-init aos-animate col-lg-4" data-aos="fade-up" data-aos-delay="600">
                            {{-- <div class="icon">
                                <i class="asad-brick"></i>
                            </div> --}}
                            <div class="content">
                                  <div class="icon">
                                {{-- <i class="asad-user-rating"></i> --}}

                                <img src="{{ asset('assets/img/icons/diamond.png') }}">
                            </div>
                                <h4 class="title">Values</h4>
                                <div class="values">
                                <ul>
                                    <li>
                                        <p>Ethics </p>
                                    </li>

                                    <li>
                                        <p>Safety & Health
                                        </p>
                                    </li>
                                    <li>
                                        <p>Quality

                                        </p>
                                    </li>
                                    <li>
                                        <p>People

                                        </p>
                                    </li>
                                </ul>
                                   <ul>
                                    <li>
                                        <p>Culture </p>
                                    </li>

                                    <li>
                                        <p>Relationships
                                        </p>
                                    </li>
                                    <li>
                                        <p>Innovation

                                        </p>
                                    </li>
                                    <li>
                                        <p>Sustainability

                                        </p>
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
<section class="choose__area">
    <div class="choose__inner-wrap">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-5 col-lg-6">
                    <div class="choose__content">
                        <div class="section__title section__title-three">
                            <span class="sub-title">Why Choose Us</span>
                            <h2 class="title">Wherever we go and whatever we do:
                            </h2>
                            <img src="{{ asset('assets/img/logo/preloader.svg') }}" alt="img">

                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6">
                    <div class="progress__wrap row">
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Demonstrate Integrity:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Exercise the highest level of professional and ethical
                                    behavior.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Learn It, Do It, Share It:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Be curious. Seek, share and build upon experiences
                                    and lessons learned.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Are Respectful:

                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Treat people with respect and dignity. Listen actively.
                                    Communicate in a timely and forthright manner. Never
                                    undermine colleagues.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Live Our Culture:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Embrace, embody and actively contribute to our
                                    Vision, Values & Missions. Develop a proud legacy.</p>
                            </div>
                        </div>

                        <div class="progress__item col-lg-6">
                            <h6 class="title">Collaborate:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Ask for and welcome help; oŠer and give it freely.
                                    Mutually resolve ambiguity and conflict.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Compete Fittingly:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>The ability to compete selectively focusing on projects
                                    with the best balance between available resources
                                    and risk/reward profile.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Build Trust:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Make commitments responsibly and always keep our
                                    word. Be candid while building shared understanding.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Hold Solid Financial Structure:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Strong commitment to grow while maintaining
                                    supportive financial resources. Highly liquid balance
                                    sheet. Low net debt/equity ratio. E„cient use of
                                    capital. Bank facilities.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Deliver:

                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Set high aspirations, plan reliably and honor all
                                    commitments.</p>
                            </div>
                        </div>
                        <div class="progress__item col-lg-6">
                            <h6 class="title">Build Our Future:
                            </h6>
                            <div class="progres" role="progressbar" aria-label="Example with label" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                                <p>Able to penetrate new markets in GCC. A very strong
                                    focus on market opportunities while managing risk.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>