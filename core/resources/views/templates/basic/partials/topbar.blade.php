<div class="dashboard-nav d-flex flex-wrap align-items-center justify-content-between">
    <div class="nav-left d-flex gap-4 align-items-center">
        <div class="dash-sidebar-toggler d-xl-none" id="dash-sidebar-toggler">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="nav-right d-flex flex-wrap align-items-center gap-3">

        @if (gs('multi_language'))
            @php
                $language = App\Models\Language::all();
                $currentLang = session('lang') ? $language->where('code', session('lang'))->first() : $language->where('is_default', Status::YES)->first();
            @endphp

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

        @endif


        <ul class="nav-header-link d-flex flex-wrap gap-2">
            <li>
                <a class="link" href="javascript:void(0)">{{ getInitials(auth()->user()->fullname) }}</a>
                <div class="dropdown-wrapper">
                    <div class="dropdown-header">
                        <h6 class="name text--base">{{ auth()->user()->fullname }}</h6>
                        <p class="fs--14px">{{ auth()->user()->username }}</p>
                    </div>
                    <ul class="links">
                        <li><a href="{{ route('user.profile.setting') }}"><i class="las la-user"></i>
                                @lang('Profile')</a></li>
                        <li><a href="{{ route('user.change.password') }}"><i class="las la-key"></i>
                                @lang('Change Password')</a></li>
                        <li><a href="{{ route('user.logout') }}"><i class="las la-sign-out-alt"></i>
                                @lang('Logout')</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
@push('script')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endpush

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
            color: hsl(var(--white));
        }

        .language_text_select {
            font-size: 14px;
            font-weight: 400;
            color: hsl(var(--white));
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
