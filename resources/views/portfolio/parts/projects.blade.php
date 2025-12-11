@php
    $units = \App\Models\Unit::allUnit();
@endphp
<!-- project-area -->
<section class="project__area-two">
    <div class="container">
        <div class="row gutter-24 project-active-two">
            @foreach ($units as $unit)
                <div class="col-lg-4 col-md-6 grid-item grid-sizer cat-three cat-one">
                    <div class="project__item-two">
                        <div class="project__thumb-two">
                            <a href="{{ url("project-details/{$unit->id}") }}">
                                <img src="{{ getImageassetWebsiteUrl($unit->cover_image) }}" alt="img">
                            </a>
                            <span class="shape"></span>
                        </div>
                        <div class="project__content-two">
                            <h2 class="title"><a href="{{ url("project-details/{$unit->id}") }}">{{ __('Service Company Building') }}</a></h2>
                            <span>{{ \Carbon\Carbon::parse($unit->start_date)->format('F Y') }} - {{  \Carbon\Carbon::parse($unit->end_date)->format('F Y')}}</span>
                        </div>
                        <div class="project__icon-two">
                            <a href="{{ url("project-details/{$unit->id}") }}"><i class="asad-plus"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Pagination -->
        <div class="mt-4">
            {{ $units->links() }}
        </div>
    </div>
</section>
<!-- project-area-end -->
