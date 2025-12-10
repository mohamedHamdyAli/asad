<section class="company__area company__bg">
    <div class="container">
        <div class="row gutter-24 align-items-center justify-content-center">
            <div class="col-lg-7 col-md-10 order-0 order-lg-2">
                <div class="company__img">
                    <img src="{{ getImageassetWebsiteUrl(getSettingValue('home_Great_Company_Backup01')) ?? asset('assets/img/images/company_img01.jpg') }}" alt="img">
                    <img src="{{ getImageassetWebsiteUrl(getSettingValue('home_Great_Company_Backup02')) ?? asset('assets/img/images/company_img02.jpg') }}" alt="img">
                    <img src="{{ getImageassetWebsiteUrl(getSettingValue('home_Great_Company_Backup03')) ?? asset('assets/img/images/company_img03.jpg') }}" alt="img">
                </div>
            </div>
            <div class="col-lg-5">
                <div class="company__content">
                    <div class="section__title section__title-three white-title mb-25">
                        <span class="sub-title">{{ __('Great_Company_Backup') }}</span>
                        <h2 class="title">{{ __('We_Are_Professional_Factory_Experts') }}</h2>
                    </div>
                    <p>{{ __('Great_Company_Backup_desc') }}</p>
                    <div class="company__list-wrap">
                        <div class="company__list-item">
                            <div class="icon">
                                <i class="renova-idea-2"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">{{ __('Innovative Solution') }}</h4>
                                <p>{{ __('Innovative_Solution_Desc') }}</p>
                            </div>
                        </div>
                        <div class="company__list-item">
                            <div class="icon">
                                <i class="renova-user-rating-2"></i>
                            </div>
                            <div class="content">
                                <h4 class="title">{{ __('Experienced Team') }}</h4>
                                <p>{{ __('Experienced_Team_Desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
