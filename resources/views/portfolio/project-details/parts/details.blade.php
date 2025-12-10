<!-- project-details-area -->
<section class="project__details-area section-py-120">
    <div class="container">
        <div class="project__details-top-content">
            <div class="row">
                <div class="col-29">
                    <div class="project__details-info">
                        <h3 class="info-title">{{ __('Project Information') }}</h3>
                        <div class="project__info-item-wrap">

                            <div class="project__info-item">
                                <div class="icon"><i class="fas fa-user"></i></div>
                                <div class="content">
                                    <span>{{ __('Clients') }}:</span>
                                    <h4 class="title">
                                        {{ $unit->user ? getLocalizedValueDashboard($unit->user, 'name') : '-' }}
                                    </h4>
                                </div>
                            </div>

                            <div class="project__info-item">
                                <div class="icon"><i class="fas fa-layer-group"></i></div>
                                <div class="content">
                                    <span>{{ __('Status') }}:</span>
                                    <h4 class="title">{{ $unit->status ?? '-' }}</h4>
                                </div>
                            </div>

                            <div class="project__info-item">
                                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="content">
                                    <span>{{ __('Date') }}:</span>
                                    <h4 class="title">
                                        {{ $unit->end_date ? \Carbon\Carbon::parse($unit->end_date)->format('F Y') : '-' }}
                                    </h4>
                                </div>
                            </div>

                            <div class="project__info-item">
                                <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="content">
                                    <span>{{ __('Location') }}:</span>
                                    <h4 class="title">{{ $unit->location ?? '-' }}</h4>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-71">
                    <div class="project__details-content">
                        <h2 class="title">{{ __('Description') }}</h2>
                        <p class="info-one">{{ getLocalizedValueDashboard($unit, 'description') ?? '-' }}</p>

                        <section class="project__area project__bg" data-background="assets/img/bg/project__bg.jpg" style="background-image: url(&quot;assets/img/bg/project__bg.jpg&quot;);">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-5 col-lg-6 col-md-8">
                        <div class="section__title white-title mb-70">
                            <h2 class="title">Our Gallery</h2>
                           
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4">
                        <div class="project__nav">
                            <button class="project-button-prev" tabindex="0" aria-label="Previous slide" aria-controls="swiper-wrapper-0b39c090c10bd2c12"><i class="asad-left-arrow"></i></button>
                            <button class="project-button-next" tabindex="0" aria-label="Next slide" aria-controls="swiper-wrapper-0b39c090c10bd2c12"><i class="asad-right-arrow"></i></button>
                        </div>
                    </div>
                </div>
                <div class="swiper project-active swiper-initialized swiper-horizontal swiper-backface-hidden">
                    <div class="swiper-wrapper" id="swiper-wrapper-0b39c090c10bd2c12" aria-live="polite">
                        <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 3" data-swiper-slide-index="0" style="width: 678px; margin-right: 24px;">
                            <div class="project__item">
                                <div class="project__thumb">
                                    <a href="project-details.html"><img src="assets/img/project/project_img01.jpg" alt="img"></a>
                                </div>
                                <div class="project__content">
                                    <div class="content">
                                        <h2 class="title"><a href="project-details.html">Retro Service Company Building</a></h2>
                                        <span>December 2025 - March 2026</span>
                                    </div>
                                    <div class="project__icon">
                                        <a href="project-details.html"><i class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 3" data-swiper-slide-index="1" style="width: 678px; margin-right: 24px;">
                            <div class="project__item">
                                <div class="project__thumb">
                                    <a href="project-details.html"><img src="assets/img/project/project_img02.html" alt="img"></a>
                                </div>
                                <div class="project__content">
                                    <div class="content">
                                        <h2 class="title"><a href="project-details.html">Retro Service Company Building</a></h2>
                                        <span>December 2025 - March 2026</span>
                                    </div>
                                    <div class="project__icon">
                                        <a href="project-details.html"><i class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide" role="group" aria-label="3 / 3" data-swiper-slide-index="2" style="width: 678px; margin-right: 24px;">
                            <div class="project__item">
                                <div class="project__thumb">
                                    <a href="project-details.html"><img src="assets/img/project/project_img01.jpg" alt="img"></a>
                                </div>
                                <div class="project__content">
                                    <div class="content">
                                        <h2 class="title"><a href="project-details.html">Retro Service Company Building</a></h2>
                                        <span>December 2025 - March 2026</span>
                                    </div>
                                    <div class="project__icon">
                                        <a href="project-details.html"><i class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
            </div>
        </section>
                    </div>
                </div>
            </div>
        </div>

        <div class="project__details-img">
            <div class="row">
                @forelse ($unit->homeUnitGallery as $gallery)
                    <div class="col-33">
                        <img src="{{ getImageassetUrl($gallery->image) }}" alt="img">
                    </div>
                @empty
                    <p>{{ __('No images available') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
<!-- project-details-area-end -->
