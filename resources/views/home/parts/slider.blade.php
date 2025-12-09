@php
    $sliders = collect(\App\Models\Banner::getSliders( 'home', 'first'));
@endphp

<!-- slider-area -->
<section class="slider__area-two">
    <div class="swiper slider-active-two">
        <div class="swiper-wrapper">
            @if ($sliders->isEmpty())
                <!-- Default sliders if DB is empty -->
                @for ($i = 1; $i <= 4; $i++)
                    <div class="swiper-slide slider__bg-two"
                        data-background="{{ asset('assets/img/slider/h3_slider_bg0' . $i . '.jpg') }}">
                        <div class="container">
                            <div class="row justify-content-end">
                                <div class="col-xl-7 col-lg-7">
                                    <div class="slider__content-two">
                                        <span class="sub-title">We’re working since 1985 in this field</span>
                                        <h2 class="title">World’s Most leading building provider</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="slider__shape" data-background="{{ asset('assets/img/slider/slider_shape.png') }}">
                        </div>
                    </div>
                @endfor
            @else
                <!-- Sliders from DB -->
                @foreach ($sliders as $slider)
                    <div class="swiper-slide slider__bg-two" data-background="{{ getImageassetUrl($slider->image) }}">
                        <div class="container">
                            <div class="row justify-content-end">
                                <div class="col-xl-7 col-lg-7">
                                    <div class="slider__content-two">
                                        <span
                                            class="sub-title">{{ getLocalizedValueDashboard($slider, 'name') ?? '-' }}</span>
                                        <h2 class="title">
                                            {{ getLocalizedValueDashboard($slider, 'description') ?? '-' }}
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="slider__shape" data-background="{{ asset('assets/img/slider/slider_shape.png') }}">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="slider__nav">
            <button class="slider-button-prev">
                <img src="{{ asset('assets/img/icons/long_left_arrow.svg') }}" alt="" class="injectable" />
            </button>
            <button class="slider-button-next">
                <img src="{{ asset('assets/img/icons/long_right_arrow.svg') }}" alt="" class="injectable" />
            </button>
        </div>
    </div>
</section>
<!-- slider-area-end -->
