    @php
        $loginContent = getContent('login.content', true);
    @endphp

    @extends($activeTemplate . 'layouts.frontend')
    @section('content')
        <div class="section container">
            <div class="row g-lg-0">
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="h-100 auth-form__bg" style="background-image: url({{ frontendImage('login', @$loginContent->data_values->image, '800x1100') }});">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="auth-form__content">
                        <h3 class="text-capitalize text-center mt-0 mb-4">
                            @lang('Welcome To') {{ __(gs('site_name')) }}
                        </h3>
                        <form method="POST" action="{{ route('user.register') }}" class="row g-4 verify-gcaptcha">
                            @csrf

                            <div class="col-sm-6">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-user"></i>
                                    </span>
                                    <input type="text" name="firstname" class="auth-form__input checkUser" value="{{ old('firstname') }}" placeholder="@lang('Firstname')" required autofocus="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-user"></i>
                                    </span>
                                    <input type="text" name="lastname" class="auth-form__input checkUser" value="{{ old('lastname') }}" placeholder="@lang('Lastname')" required autofocus="off" />
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-envelope-open-text"></i>
                                    </span>
                                    <input type="text" name="email" class="auth-form__input checkUser" value="{{ old('email') }}" placeholder="@lang('Email Address')" required autofocus="off" />
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="auth-form__input-group form-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-lock"></i>
                                    </span>
                                    <input type="password" name="password" class="auth-form__input @if (gs('secure_password')) secure-password @endif" placeholder="@lang('Your password')" required />

                                    <span class="auth-form__input-icon auth-form__toggle-pass">
                                        <i class="bx bxs-hide"></i>
                                    </span>

                                </div>
                            </div>

                            <div class="col-12">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-lock"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" class="auth-form__input" placeholder="@lang('Confirm password')" required />
                                    <span class="auth-form__input-icon auth-form__toggle-pass">
                                        <i class="bx bxs-hide"></i>
                                    </span>
                                </div>
                            </div>

                            <x-captcha />

                            @if (gs('agree'))
                                @php
                                    $policyPages = getContent('policy_pages.element', false, null, true);
                                @endphp
                                <div class="form-group">
                                    <input type="checkbox" id="agree" @checked(old('agree')) name="agree" required>
                                    <label for="agree">@lang('I agree with')</label> <span>
                                        @foreach ($policyPages as $policy)
                                            <a class="text-decoration-none" href="{{ route('policy.pages', $policy->slug) }}" target="_blank">{{ __(@$policy->data_values->title) }}</a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </span>
                                </div>
                            @endif

                            <div class="col-12">
                                <button type="submit" id="recaptcha" class="btn btn--base btn--xxl w-100 text-capitalize xl-text">
                                    @lang('Register')
                                </button>
                            </div>
                            <div class="col-12">
                                <p class="mb-0 text-capitalize text-center">
                                    @lang('Already have an account yet')?
                                    <a href="{{ route('user.login') }}" class="t-link border-0 bg--light btn-link t-link--primary">
                                        @lang('Login Now')
                                    </a>
                                </p>
                            </div>

                            @php
                                $credentials = gs('socialite_credentials');
                            @endphp
                            @if ($credentials->google->status == Status::ENABLE || $credentials->facebook->status == Status::ENABLE || $credentials->linkedin->status == Status::ENABLE)
                                <div class="col-12">
                                    <div class="auth-form__divider">
                                        <span class="d-block text-center text-capitalize auth-form__divider-text">
                                            @lang(' or')
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="mb-0 text-capitalize text-center">
                                        @lang('Continue with social media')
                                    </p>
                                </div>

                                <div class="col-12">
                                    <ul class="list list--row justify-content-center">
                                        @if ($credentials->facebook->status == Status::ENABLE)
                                            <li class="list--row__item">
                                                <a href="{{ route('user.social.login', 'facebook') }}" class="t-link icon icon--circle icon--md bg--primary t-text-white t-link--light">
                                                    <i class="bx bxl-facebook"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($credentials->google->status == Status::ENABLE)
                                            <li class="list--row__item">
                                                <a href="{{ route('user.social.login', 'google') }}" class="t-link icon icon--circle icon--md bg--danger t-text-white t-link--light">
                                                    <i class="bx bxl-google"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if ($credentials->linkedin->status == Status::ENABLE)
                                            <li class="list--row__item">
                                                <a href="{{ route('user.social.login', 'linkedin') }}" class="t-link icon icon--circle icon--md bg--info t-text-white t-link--light">
                                                    <i class="bx bxl-linkedin"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                        <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                        <a href="{{ route('user.login') }}" class="btn btn--base btn-sm">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @if (gs('secure_password'))
        @push('script-lib')
            <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
        @endpush
    @endif

    @push('script')
        <script>
            "use strict";
            (function($) {

                $('.checkUser').on('focusout', function(e) {
                    var url = '{{ route('user.checkUser') }}';
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data != false) {
                            $('#existModalCenter').modal('show');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush
