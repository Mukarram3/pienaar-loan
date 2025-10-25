@extends('admin.layouts.app')
@section('panel')

    <!-- Create Update Modal -->
    <div class="modal fade cuModal" id="cuModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New User')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <form action="{{ route('admin.users.save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="user_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Username')</label>
                            <input type="text" class="form-control" name="username" id="username" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Email')</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Password')</label>
                            <div class="input-group">
                                <input class="form-control" name="password" id="password" type="text" required>
                                <button class="input-group-text generatePassword" type="button">@lang('Generate')</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Email-Mobile')</th>
                                <th>@lang('Country')</th>
                                <th>@lang('Joined At')</th>
                                <th>@lang('Balance')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{$user->fullname}}</span>
                                    <br>
                                    <span class="small">
                                    <a href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                    </span>
                                </td>


                                <td>
                                    {{ $user->email }}<br>{{ $user->mobileNumber }}
                                </td>
                                <td>
                                    <span class="fw-bold" title="{{ @$user->country_name }}">{{ $user->country_code }}</span>
                                </td>



                                <td>
                                    {{ showDateTime($user->created_at) }} <br> {{ diffForHumans($user->created_at) }}
                                </td>


                                <td>
                                    <span class="fw-bold">

                                    {{ showAmount($user->balance) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="button--group">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="las la-desktop"></i> @lang('Details')
                                        </a>
                                        <button class="btn btn-sm btn-outline--primary editbtn"
                                                data-firstname="{{ $user->firstname }}"
                                                data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-id="{{ $user->id }}"
                                                >
                                            <i class="la la-pencil"></i> @lang('Edit')
                                        </button>
                                        @if (request()->routeIs('admin.users.kyc.pending'))
                                        <a href="{{ route('admin.users.kyc.details', $user->id) }}" target="_blank" class="btn btn-sm btn-outline--dark">
                                            <i class="las la-user-check"></i>@lang('KYC Data')
                                        </a>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($users->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($users) }}
                </div>
                @endif
            </div>
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Email" />

{{--    @can('admin.staff.save')--}}
        <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" id="addNewBtn" data-modal_title="@lang('Add New Staff')">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
{{--    @endcan--}}
@endpush

@push('script-lib')
{{--    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>--}}
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.generatePassword').on('click', function() {
                $(this).siblings('[name=password]').val(generatePassword());
            });

            $('.editbtn').on('click', function() {

                var id = $(this).data('id');
                var name = $(this).data('firstname');
                var username = $(this).data('username');
                var email = $(this).data('email');
                let baseUrl = "{{ route('admin.users.save', '') }}";

                $('#cuModal').find('#user_id').val(id);
                $('#cuModal').find('#name').val(name);
                $('#cuModal').find('#username').val(username);
                $('#cuModal').find('#email').val(email);
                $('#cuModal').find('.modal-title').text('@lang("Edit User")');
                $('#cuModal').find('form').attr('action', baseUrl + '/' + id);


                let passwordField = $('#cuModal').find($('[name=password]'));
                let label = passwordField.parents('.form-group').find('label')
                if ($(this).data('resource')) {
                    passwordField.removeAttr('required');
                    label.removeClass('required')
                } else {
                    passwordField.attr('required', 'required');
                    label.addClass('required')
                }

                $('#cuModal').modal('show');

            });


            function generatePassword(length = 12) {
                let charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+<>?/";
                let password = '';
                for (var i = 0, n = charset.length; i < length; ++i) {
                    password += charset.charAt(Math.floor(Math.random() * n));
                }

                return password
            }

            if (new URLSearchParams(window.location.search).has('addnew')) {
                let cuModal = new bootstrap.Modal(document.getElementById('cuModal'));
                cuModal.show();
            }

            $('#addNewBtn').on('click', function() {
                var modal = $('.cuModal');
                let baseUrl = "{{ route('admin.users.save', '') }}";
                $('#cuModal').find('form').attr('action', baseUrl);
                modal.find('form')[0].reset();
                modal.find('#user_id').val('');
                modal.find('.modal-title').text('@lang("Add New User")');
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
