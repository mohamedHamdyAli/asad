@php
    $logos = collect(\App\Models\Banner::getSliders('logos', 'get'));
@endphp

<section class="cirtifaction">
    <div class="container">
    <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <div class="section__title section__title-three text-center mb-60">
                    <span class="sub-title"> ISO CERTIFICATIONS </span>
                    <h2 class="title">International Standards Organization</h2>
                </div>
            </div>
        </div>
    <div class="row">
        
<div class="col-lg-4">
        <div class="cirtifaction__item">
            <div class="cirtifaction__icon">
                <img src="{{ asset('assets/img/bg/cirt1.png') }}" alt="icon">
            </div>
            <div class="cirtifaction__content">
                <h3 class="title">ISO 9001:2015</h3>
                  <p>ISO 9001 is a standard that sets out the requirements for a quality management 
system. It helps businesses and organizations to be more e cient and improve 
consumer satisfaction.</p>    

</div>
</div>
</div>
<div class="col-lg-4">
        <div class="cirtifaction__item">
            <div class="cirtifaction__icon">
                <img src="{{ asset('assets/img/bg/cirt2.png') }}" alt="icon">
            </div>
            <div class="cirtifaction__content">
                <h3 class="title">OHSAS 18001:2007  
</h3>
                <p>ISO 18001 is an internationally agreed Occupational Health and Safety           
Management Certification which provides a framework to identify, control and 
decrease the risks associated with health and safety within the workplace.
Implementing the standard will send a clear signal to stakeholders that the 
company views employee’s health and safety as a priority within the company.</p>     

</div>
</div>
</div>
<div class="col-lg-4">
        <div class="cirtifaction__item">
            <div class="cirtifaction__icon">
                <img src="{{ asset('assets/img/bg/cirt3.png') }}" alt="icon">
            </div>
            <div class="cirtifaction__content">
                <h3 class="title">ISO 14001:2015 

</h3>
                <p>ISO 14001 is an internationally agreed standard that sets out the requirements 
for an environmental management system. It helps organizations improve their 
environmental through more e cient use of resources and reduction of waste, 
gaining a competitive advantage and the trust of stakeholders.</p>    

</div>
</div>
</div>
</div>
</section>

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
