<!-- breadcrumb-area -->
<div class="breadcrumb__area breadcrumb__bg" data-background="{{ getImageassetUrl($unit->cover_image) }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__content">
                    <h2 class="title">{{ getLocalizedValueDashboard($unit, 'name') }}</h2>
                    <nav class="breadcrumb">
                        <span property="itemListElement" typeof="ListItem">
                            <a href="{{ url('/') }}">{{ __('Home') }}</a>
                        </span>
                        <span class="breadcrumb-separator">/</span>
                        <span property="itemListElement" typeof="ListItem">{{ getLocalizedValueDashboard($unit, 'name') }}</span>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb-area-end -->
