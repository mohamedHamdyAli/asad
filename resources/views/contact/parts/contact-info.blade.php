<div class="col-lg-4">
    <div class="contact__info-wrap">
        <div class="contact__info-item">
            <div class="contact__info-thumb">
                <img src="{{ getImageassetWebsiteUrl(getSettingValue('contact_service_station')) ?? asset('assets/img/images/contact_img01.jpg') }}" alt="img">
            </div>
            <div class="contact__info-content">
                <div class="icon"><i class="asad-map"></i></div>
                <div class="content">
                    <span>{{ __('Service Station') }}</span>
                    <h2 class="title">{{ __('258D Mark Avenue Berlin.') }}</h2>
                </div>
            </div>
        </div>

        <div class="contact__info-item">
            <div class="contact__info-thumb">
                <img src="{{ getImageassetWebsiteUrl(getSettingValue('contact_make_quote')) ?? asset('assets/img/images/contact_img02.jpg') }}" alt="img">
            </div>
            <div class="contact__info-content">
                <div class="icon"><i class="asad-envelope-open"></i></div>
                <div class="content">
                    <span>{{ __('Make A Quote') }}</span>
                    <h2 class="title"><a href="mailto:info@asad.com">info@asad.com</a></h2>
                </div>
            </div>
        </div>

        <div class="contact__info-item">
            <div class="contact__info-thumb">
                <img src="{{ getImageassetWebsiteUrl(getSettingValue('contact_call_us')) ?? asset('assets/img/images/contact_img03.jpg') }}" alt="img">
            </div>
            <div class="contact__info-content">
                <div class="icon"><i class="asad-headphone"></i></div>
                <div class="content">
                    <span>{{ __('Call Us 24/7') }}</span>
                    <h2 class="title"><a href="tel:0123456789">+2569 (25) 215868</a></h2>
                </div>
            </div>
        </div>
    </div>
</div>
