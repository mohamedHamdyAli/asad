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
                    </div>
                </div>
            </div>
        </div>

        <section class="project__area project__bg" {{ asset('assets/img/bg/project__bg.jpg') }}>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-5 col-lg-6 col-md-8">
                        <div class="section__title white-title mb-70">
                            <h2 class="title">{{ __('Unit_Gallery') }}</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4">
                        <div class="project__nav">
                            <button class="project-button-prev" aria-label="Previous slide"><i
                                    class="asad-left-arrow"></i></button>
                            <button class="project-button-next" aria-label="Next slide"><i
                                    class="asad-right-arrow"></i></button>
                        </div>
                    </div>
                </div>

                <div class="swiper project-active">
                    <div class="swiper-wrapper">

                        @forelse ($unit->homeUnitGallery as $gallery)
                            <div class="swiper-slide">
                                <div class="project__item">
                                    <div class="project__thumb">
                                        <a href="{{ getImageassetUrl($gallery->image) }}">
                                            <img src="{{ getImageassetUrl($gallery->image) }}" alt="img">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <p class="text-white">{{ __('No images available') }}</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
<!-- project-details-area-end -->
