@php
    $slider = \App\Models\Banner::getSliders( 'about', 'first');
@endphp
<!-- breadcrumb-area -->
<div class="breadcrumb__area breadcrumb__bg" data-background="{{ $slider ? getImageassetUrl($slider->image) : asset('assets/img/bg/breadcrumb_bg.jpg') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__content">
                    <h2 class="title">{{ __('QHSE') }}</h2>
                    <nav class="breadcrumb">
                        <span property="itemListElement" typeof="ListItem">
                            <a href="{{ url('/') }}">{{ __('Home') }}</a>
                        </span>
                        <span class="breadcrumb-separator">/</span>
                        <span property="itemListElement" typeof="ListItem">{{ __('QHSE') }}</span>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb-area-end -->
