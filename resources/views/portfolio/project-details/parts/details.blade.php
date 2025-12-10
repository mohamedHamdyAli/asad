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

        <div class="project__details-img">
            <div class="row">
                @forelse ($unit->homeUnitGallery as $gallery)
                    <div class="col-33">
                        <img src="{{ getImageassetWebsiteUrl($gallery->image) }}" alt="img">
                    </div>
                @empty
                    <p>{{ __('No images available') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
<!-- project-details-area-end -->
