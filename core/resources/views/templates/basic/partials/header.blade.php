@php
    $contactContent = getContent('contact.content', true);
@endphp

<section class="top-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="list list--row d-flex align-items-center">
                    <li class="list--row__item">
                        <ul class="list vf-info-list">
                            <li class="vf-info-list__item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
{{--                                        <img src="{{ asset($activeTemplateTrue . 'images/icon-envelop.png') }}">--}}
                                        <i class="fa fa-envelope icon"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="label mb-0 fw-bold text-uppercase t-text-white" style="color: #002147 !important;">
                                            @lang('Email')
                                        </p>

                                        <a href="mailto:{{ @$contactContent->data_values->email }}" class="mb-0 vf-info-list__text" style="color: #002147 !important;">{{ __(@$contactContent->data_values->email) }}</a>
                                    </div>

                                </div>
                            </li>
                            <li class="vf-info-list__item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
{{--                                        <img src="{{ asset($activeTemplateTrue . 'images/icon-location.png') }}">--}}
                                        <i class="fa fa-map-marker icon"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="label mb-0 fw-bold text-uppercase t-text-white" style="color: #002147 !important;">
                                            @lang('Address')
                                        </p>
                                        <p class="mb-0 vf-info-list__text" style="color: #002147 !important;">
                                            {{ __(@$contactContent->data_values->location) }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="vf-info-list__item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
{{--                                        <img src="{{ asset($activeTemplateTrue . 'images/icon-phone.png') }}">--}}
                                        <i class="fa fa-phone icon"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="label mb-0 fw-bold text-uppercase t-text-white" style="color: #002147 !important;">
                                            @lang('Phone')
                                        </p>
                                        <a href="tel:{{ @$contactContent->data_values->phone }}" class="mb-0 fw-md vf-info-list__text" style="color: #002147 !important;">
                                            {{ @$contactContent->data_values->phone }}</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>

                    @if (gs('multi_language'))
                        @php
                            $language = App\Models\Language::all();
                            $currentLang = session('lang') ? $language->where('code', session('lang'))->first() : $language->where('is_default', Status::YES)->first();
                        @endphp

                        <li class="ms-auto">
                            <div class="language dropdown">

                                <button class="language-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="language-content">
                                        <div class="language_flag">
                                            <img src="{{ getImage(getFilePath('language') . '/' . @$currentLang->image, getFileSize('language')) }}" alt="flag">
                                        </div>
                                        <p class="language_text_select">{{ __(@$currentLang->name) }}</p>
                                    </div>
                                    <span class="collapse-icon"><i class="las la-angle-down"></i></span>
                                </button>

                                <div class="dropdown-menu langList_dropdow py-2">
                                    <ul class="langList">
                                        @foreach ($language as $item)
                                            @if (session('lang') != $item->code)
                                                <li class="language-list languageList" data-code="{{ $item->code }}">
                                                    <div class="language_flag">
                                                        <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}" alt="flag">
                                                    </div>
                                                    <p class="language_text">{{ __($item->name) }}</p>
                                                </li>
                                            @endif
                                        @endforeach

                                    </ul>
                                </div>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>

<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ siteLogo() }}" alt="viserhyip" class="img-fluid logo__is" style="width: 90px !important;" />
            </a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu align-items-lg-center">

                    @if (gs('multi_language'))
                        <li class="d-lg-none">
                            <div class="language dropdown sm-screen">
                                <button class="language-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="language-content">
                                        <div class="language_flag">
                                            <img src="{{ getImage(getFilePath('language') . '/' . @$currentLang->image, getFileSize('language')) }}" alt="flag">
                                        </div>
                                        <p class="language_text_select">{{ __(@$currentLang->name) }}</p>
                                    </div>
                                    <span class="collapse-icon"><i class="las la-angle-down"></i></span>
                                </button>

                                <div class="dropdown-menu langList_dropdow py-2">
                                    <ul class="langList">
                                        @foreach ($language as $item)
                                            @if (session('lang') != $item->code)
                                                <li class="language-list languageList" data-code="{{ $item->code }}">
                                                    <div class="language_flag">
                                                        <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}" alt="flag">
                                                    </div>
                                                    <p class="language_text">{{ __($item->name) }}</p>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('home') }}" aria-current="page" href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    @if (@$pages)
                        @foreach ($pages as $k => $data)
                            <li class="nav-item">
                                <a class="{{ menuActive('pages', [$data->slug]) }} nav-link" href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
                            </li>
                        @endforeach
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('loan') }}" href="{{ route('loan') }}">@lang('Plans')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('blog') }}" href="{{ route('blog') }}">@lang('Blogs')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ menuActive('contact') }}" href="{{ route('contact') }}">@lang('Contact')</a>
                    </li>
                    @auth
                        <li class="nav-item mt-2 d-block d-lg-none">
                            <div class="account mt-1">
                                <a href="{{ route('user.home') }}" class="btn btn--md btn--base fw-bold w-100">
                                    @lang('Dashboard')
                                </a>
                            </div>
                        </li>
                    @else
                        <li class="nav-item mt-2 d-block d-lg-none">
                            <div class="account mt-1">
                                <a href="{{ route('user.login') }}" class="btn btn--md btn--base fw-bold w-100">
                                    @lang('Join Now')
                                </a>
                            </div>
                        </li>
                    @endauth
                </ul>
            </div>

            @auth
                <div class="account d-none d-lg-block">
                    <a href="{{ route('user.home') }}" class="btn btn--md btn--base fw-bold w-100">
                        @lang('Dashboard')
                    </a>
                </div>
            @else
                <div class="account d-none d-lg-block">
                    <a href="{{ route('user.login') }}" class="btn btn--md btn--base fw-bold w-100">
                        @lang('Login')
                    </a>
                </div>
            @endauth
        </nav>
    </div>
</header>


@push('style')
    <style>
        .language-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 5px 12px;
            border-radius: 4px;
            width: max-content;
            background-color: transparent;
            border: 1px solid hsl(var(--white) / .5) !important;
            height: 38px;
        }

        .sm-screen {
            max-width: 130px;
        }

        .sm-screen .language-wrapper {
            border: 1px solid hsl(var(--dark) / .5) !important;
        }

        .sm-screen .language_text_select {
            color: hsl(var(--dark));
        }

        .sm-screen .collapse-icon {
            color: hsl(var(--dark));
        }


        .language_flag {
            flex-shrink: 0
        }

        .language_flag img {
            height: 20px;
            width: 20px;
            object-fit: cover;
            border-radius: 50%;
        }

        .language-wrapper.show .collapse-icon {
            transform: rotate(180deg)
        }

        .collapse-icon {
            font-size: 14px;
            display: flex;
            transition: all linear 0.2s;
            color: #002147;
        }

        .language_text_select {
            font-size: 14px;
            font-weight: 400;
            color: #002147;
            margin-bottom: 0;
        }

        .language-content {
            display: flex;
            align-items: center;
            gap: 6px;
        }


        .language_text {
            color: hsl(var(--white));
            margin-bottom: 0;
        }

        .langList {
            padding: 0;
        }

        .language-list {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            cursor: pointer;
        }

        .language .dropdown-menu {
            position: absolute;
            -webkit-transition: ease-in-out 0.1s;
            transition: ease-in-out 0.1s;
            opacity: 0;
            visibility: hidden;
            top: 100%;
            display: unset;
            background: hsl(var(--base));
            -webkit-transform: scaleY(1);
            transform: scaleY(1);
            min-width: 150px;
            padding: 7px 0 !important;
            border-radius: 8px;
            border: 1px solid rgb(255 255 255 / 10%);
        }

        .language .dropdown-menu.show {
            visibility: visible;
            opacity: 1;
            inset: unset !important;
            margin: 0px !important;
            transform: unset !important;
            top: 100% !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            const $mainlangList = $(".langList");
            const $langBtn = $(".language-content");
            const $langListItem = $mainlangList.children();

            $langListItem.each(function() {
                const $innerItem = $(this);
                const $languageText = $innerItem.find(".language_text");
                const $languageFlag = $innerItem.find(".language_flag");

                $innerItem.on("click", function(e) {
                    $langBtn.find(".language_text_select").text($languageText.text());
                    $langBtn.find(".language_flag").html($languageFlag.html());
                });
            });

        })(jQuery);
    </script>
@endpush
