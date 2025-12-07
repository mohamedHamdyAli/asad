<!-- cta-area / tabs -->
<section class="cta__area fix dtls-sec">
    <div class="tabs">
        @for ($i = 1; $i <= 11; $i++)
            <div class="tab {{ $i == 1 ? 'active' : '' }}" data-tab="{{ $i }}">
                {{ ['Documents','Gallery','Drawings','Reports','Phases','Payments','Timeline','Phases','Consultant','Live cam','Contractors'][$i-1] }}
            </div>
        @endfor
    </div>

    @for ($i = 1; $i <= 11; $i++)
        <div id="tab-{{ $i }}" class="tab-content {{ $i == 1 ? 'active' : '' }}">
            <h3>{{ ['Overview','Inspection','Finishes','Utilities','Payments','Documents','Snag List','Notes','Support','Live cam','Contractors'][$i-1] }}</h3>
            <p>Content for {{ ['Overview','Inspection','Finishes','Utilities','Payments','Documents','Snag List','Notes','Support','Live cam','Contractors'][$i-1] }} goes here.</p>
        </div>
    @endfor
</section>
<!-- cta-area-end -->
