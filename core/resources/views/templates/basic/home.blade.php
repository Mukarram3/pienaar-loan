@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $bannerContent = getContent('banner.content', true);
    @endphp

    <div class="hero" style="background-image: url({{ frontendImage('banner' , @$bannerContent->data_values->image, '1920x960') }});">
        <div class="hero__content">
            <div class="container">
                <div class="row g-3 justify-content-center">
                    <div class="col-lg-9 col-xl-7 text-center">
                        <h1 class="hero__content-title text-capitalize t-text-white">
                            {{ __(@$bannerContent->data_values->heading) }}
                        </h1>
                        <a href="{{ @$bannerContent->data_values->button_link }}"
                            class="btn btn--xl l-text btn--base btn--outline mt-3">
                            {{ __(@$bannerContent->data_values->button_name) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
