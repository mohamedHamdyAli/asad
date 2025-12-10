@php
    $slider = \App\Models\Banner::getSliders( 'our-services', 'first');
@endphp
<!-- breadcrumb-area -->
<div class="breadcrumb__area breadcrumb__bg" data-background="{{ $slider ? getImageassetUrl($slider->image) : asset('assets/img/bg/breadcrumb_bg.jpg') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__content">
                    <h2 class="title">{{ __('Our Services') }}</h2>
                    <nav class="breadcrumb">
                        <span property="itemListElement" typeof="ListItem">
                            <a href="{{ url('/') }}">{{ __('Home') }}</a>
                        </span>
                        <span class="breadcrumb-separator">/</span>
                        <span property="itemListElement" typeof="ListItem">{{ __('Our Services') }}</span>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb-area-end -->


<section class="office__area">
            <div class="container">
                <div class="row gutter-24 justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Civil, Infrastructure and electromechanical Works</h3>
                           
                              
                             
                        </div>
                    </div>
                     
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Large scale residential developments </h3>
                            
                              
                             
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Residential and commercial high-rise towers </h3>
                            
                              
                             
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Luxurious villas</h3>
                            
                             
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Hospitals and clinics. </h3>
                              
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Hotels and hotel apartments </h3>
                           
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Mosques</h3>
                        
                             
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Retail and commercial fit-out </h3>
                            
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Development for Existing building,
Extensions and renovations.</h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Value engineering solutions </h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Construction program e‡ciencies </h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Cost planning</h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Construction management </h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Project funding assistance</h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Project feasibility studies </h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Design and build</h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Modeling and Simulation of construction operations
</h3>
                        
                             
                        </div>
                    </div>
                      <div class="col-lg-4 col-md-6">
                        <div class="office__item">
                            <h3 class="title">Machine learning applications in construction industry
</h3>
                        
                             
                        </div>
                    </div>
                   
                </div>
            </div>
        </section>
