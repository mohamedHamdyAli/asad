<div class="contact__form-wrap">
    <div class="section__title section__title-three mb-40">
        <span class="sub-title">{{ __('Get In Touch') }}</span>
        <h2 class="title">{{ __('Needs Help? Let’s Get in Touch') }}</h2>
    </div>
    <form id="contact-form" action="{{ asset('assets/mail.php') }}" method="post" class="contact__form">
        <div class="row gutter-20">
            <div class="col-md-6">
                <div class="form-grp">
                    <input type="text" name="name" placeholder="Your Name">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-grp">
                    <input type="email" name="email" placeholder="Email Address">
                </div>
            </div>
        </div>
        <div class="form-grp">
            <input type="text" name="subject" placeholder="Your Subject">
        </div>
        <div class="form-grp">
            <textarea name="message" placeholder="Type Your Message"></textarea>
        </div>
        <button type="submit" class="btn btn-two">{{ __('Send Message') }}</button>
        <p class="ajax-response mb-0"></p>
    </form>
</div>
