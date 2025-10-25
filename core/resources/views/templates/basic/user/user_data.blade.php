@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="container section">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-8">
                <div class="card custom--card">
                    <div class="card-body">
                        <h4 class="text-capitalize text-center mb-4 mt-0">
                            @lang('Please complete your profile')
                        </h4>
                        <form method="POST" action="{{ route('user.data.submit') }}" class="row g-4">
                            @csrf

                            <div class="col-12">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="la la-user"></i>
                                    </span>
                                    <input type="text" name="username" class="auth-form__input checkUser" value="{{ old('username') }}" placeholder="@lang('Username')" required autofocus="off" />
                                </div>
                                <small class="text--danger usernameExist"></small>
                            </div>

                            <div class="col-sm-6">
                                <div class="custom--nice-select auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="la la-globe-asia"></i>
                                    </span>
                                    <select name="country">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">
                                                {{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon mobile-code">

                                    </span>
                                    <input type="hidden" name="mobile_code">
                                    <input type="hidden" name="country_code">
                                    <input type="number" name="mobile" class="auth-form__input checkUser" value="{{ old('mobile') }}" placeholder="@lang('Your Mobile Number')" required autofocus="off" />
                                </div>
                                <small class="text--danger mobileExist"></small>
                            </div>

                            <div class="col-sm-6">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-globe-asia"></i>
                                    </span>
                                    <input type="text" name="address" class="auth-form__input checkUser" value="{{ old('address') }}" placeholder="@lang('Address')" required autofocus="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-gopuram"></i>
                                    </span>
                                    <input type="text" name="state" class="auth-form__input checkUser" value="{{ old('state') }}" placeholder="@lang('State')" autofocus="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-sort-numeric-down"></i>
                                    </span>
                                    <input type="text" name="zip" class="auth-form__input checkUser" value="{{ old('zip') }}" placeholder="@lang('Zip')" autofocus="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="auth-form__input-group">
                                    <span class="auth-form__input-icon">
                                        <i class="las la-city"></i>
                                    </span>
                                    <input type="text" name="city" class="auth-form__input checkUser" value="{{ old('city') }}" placeholder="@lang('City')" autofocus="off" />
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn--base btn--xxl w-100 text-capitalize xl-text">
                                    @lang('Submit')
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {

            @if($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected','');
            @endif

            $('select[name=country]').on('change',function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value,name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value,name);
            });

            function checkUser(value,name){
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code:$('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                     if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
