


<!-- work-area -->
<section class="work__area-two section-pt-120 section-pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8">
                <div class="section__title section__title-three text-center white-title mb-60">
                    <span class="sub-title">{{ __('work_process_title') }}</span>
                    <h2 class="title"> {{ __('work_process_desc') }} </h2>
                </div>
            </div>
        </div>
        <div class="work__item-wrap">
            <div class="row">
                @php
                    $work_steps = [
                        [
                            'icon' => 'asad-note',
                            'title' => __('Project_Research'),
                            'text' => __('Project_Research_desc'),
                        ],
                        [
                            'icon' => 'asad-construction',
                            'title' => __('Development'),
                            'text' => __('Development_desc'),
                        ],
                        [
                            'icon' => 'asad-unity',
                            'title' => __('Team_Works'),
                            'text' => __('Team_Works_desc'),
                        ],
                        [
                            'icon' => 'asad-rocket-2',
                            'title' => __('Quality_Finished'),
                            'text' => __('Quality_Finished_desc'),
                        ],
                    ];

                @endphp
                @foreach ($work_steps as $key => $step)
                    <div class="col-lg-3 col-md-6">
                        <div class="work__item">
                            <div class="work__icon">
                                <i class="{{ $step['icon'] }}"></i>
                                <span class="number">0{{ $key + 1 }}</span>
                            </div>
                            <div class="work__content-two">
                                <h4 class="title"><a href="{{ url('contact') }}">{{ $step['title'] }}</a></h4>
                                <p>{{ $step['text'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- work-area-end -->
