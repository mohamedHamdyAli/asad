<!-- project-area -->
<section class="project__area-two">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="project__menu-nav">
                    <button class="active" data-filter="*">ALL WORKS</button>
                    <button data-filter=".cat-one">ALFA PROJECT</button>
                    <button data-filter=".cat-two">CONSTRUCTION PROJECT</button>
                    <button data-filter=".cat-three">LORENCE PROJECT</button>
                </div>
            </div>
        </div>
        <div class="row gutter-24 project-active-two">
            @for ($i = 1; $i <= 9; $i++)
                <div class="col-lg-4 col-md-6 grid-item grid-sizer {{ $i % 3 == 0 ? 'cat-three' : ($i % 2 == 0 ? 'cat-two' : 'cat-one') }}">
                    <div class="project__item-two">
                        <div class="project__thumb-two">
                            <a href="project-details.html"><img src="{{ asset('assets/img/project/inner_project0'.$i.'.jpg') }}" alt="img"></a>
                            <span class="shape"></span>
                        </div>
                        <div class="project__content-two">
                            <h2 class="title"><a href="project-details.html">Service Company Building</a></h2>
                            <span>December 2025 - March 2026</span>
                        </div>
                        <div class="project__icon-two">
                            <a href="project-details.html"><i class="asad-plus"></i></a>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>
<!-- project-area-end -->
