<!-- work-area -->
<section class="work__area-two section-pt-120 section-pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8">
                <div class="section__title section__title-three text-center white-title mb-60">
                    <span class="sub-title">We strive to be a step ahead</span>
                    <h2 class="title">The four steps working process to get amazing works</h2>
                </div>
            </div>
        </div>
        <div class="work__item-wrap">
            <div class="row">
                @php
                    $work_steps = [
                        ['icon'=>'renova-note','title'=>'Project Research','text'=>'One of the key components of best logistics solutions industy'],
                        ['icon'=>'renova-construction','title'=>'Development','text'=>'One of the key components of best logistics solutions industy'],
                        ['icon'=>'renova-unity','title'=>'Team Works','text'=>'One of the key components of best logistics solutions industy'],
                        ['icon'=>'renova-rocket-2','title'=>'Quality Finished','text'=>'One of the key components of best logistics solutions industy'],
                    ];
                @endphp
                @foreach($work_steps as $key => $step)
                <div class="col-lg-3 col-md-6">
                    <div class="work__item">
                        <div class="work__icon">
                            <i class="{{ $step['icon'] }}"></i>
                            <span class="number">0{{ $key+1 }}</span>
                        </div>
                        <div class="work__content-two">
                            <h4 class="title"><a href="{{ url('contact') }}">{{ $step['title'] }}</a></h4>
                            <p>{{ $step['text'] }}</p>
                            <a href="{{ url('contact') }}" class="link-btn">Learn More <i class="renova-right-arrow-2"></i></a>
                        </div>
                        <div class="work__arrow">
                            <img src="{{ asset('assets/img/icons/right_arrow02.svg') }}" alt="" class="injectable">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- work-area-end -->
