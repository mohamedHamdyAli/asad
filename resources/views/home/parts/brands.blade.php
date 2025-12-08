@php
    $logos = collect(\App\Models\Banner::getSliders('logos', 'get'));
@endphp

<div class="brand__area section-pb-120">
    <div class="container">
        <div class="swiper brand-active">
            <div class="swiper-wrapper">

                @if($logos->isNotEmpty())
                    {{-- Show logos from DB --}}
                    @foreach ($logos as $logo)
                        <div class="swiper-slide">
                            <div class="brand__item">
                                <img src="{{ getImageassetUrl($logo->image) }}" alt="logo">
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Default logos if DB is empty --}}
                    <div class="swiper-slide"><div class="brand__item"><img src="{{ asset('assets/img/brand/brand_img02.png') }}" alt="img"></div></div>
                    <div class="swiper-slide"><div class="brand__item"><img src="{{ asset('assets/img/brand/brand_img03.png') }}" alt="img"></div></div>
                    <div class="swiper-slide"><div class="brand__item"><img src="{{ asset('assets/img/brand/brand_img04.png') }}" alt="img"></div></div>
                    <div class="swiper-slide"><div class="brand__item"><img src="{{ asset('assets/img/brand/brand_img05.png') }}" alt="img"></div></div>
                    <div class="swiper-slide"><div class="brand__item"><img src="{{ asset('assets/img/brand/brand_img06.png') }}" alt="img"></div></div>
                    <div class="swiper-slide"><div class="brand__item"><img src="{{ asset('assets/img/brand/brand_img03.png') }}" alt="img"></div></div>
                @endif

            </div>
        </div>
    </div>
</div>
