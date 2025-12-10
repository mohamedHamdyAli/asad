<section class="cirtifaction">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <div class="section__title section__title-three text-center mb-60">
                    <span class="sub-title"> {{ __('ISO CERTIFICATIONS') }} </span>
                    <h2 class="title">{{ __('International Standards Organization') }}</h2>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-lg-4">
                <div class="cirtifaction__item">
                    <div class="cirtifaction__icon">
                        <img src="{{ getImageassetWebsiteUrl(getSettingValue('home_cirtifaction01')) }}" alt="icon">
                    </div>
                    <div class="cirtifaction__content">
                        <h3 class="title">{{ __('ISO 9001_title') }}</h3>
                        <p>{{ __('ISO_9001_Desc') }}</p>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cirtifaction__item">
                    <div class="cirtifaction__icon">
                        <img src="{{ getImageassetWebsiteUrl(getSettingValue('home_cirtifaction02')) }}" alt="icon">
                    </div>
                    <div class="cirtifaction__content">
                        <h3 class="title">{{ __('OHSAS 18001_title') }}
                        </h3>
                        <p>{{ __('OHSAS_18001_Desc') }}</p>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cirtifaction__item">
                    <div class="cirtifaction__icon">
                        <img src="{{ getImageassetWebsiteUrl(getSettingValue('home_cirtifaction03'))}}" alt="icon">
                    </div>
                    <div class="cirtifaction__content">
                        <h3 class="title">{{ __('ISO 14001_title') }}</h3>
                        <p>{{ __('ISO_14001_Desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
</section>
