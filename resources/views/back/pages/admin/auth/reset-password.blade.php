@extends('back.layout.auth-layout')
@section('pageTitle', isset($pageTilte) ? $pageTilte : 'Admin Forget Password')
@section('content')
    <div class="row align-items-center">
        <div class="col-md-6">
            <img src="{{ asset('back/vendors/images/forgot-password.png') }}" alt="">
        </div>
        <div class="col-md-6">
            <div class="login-box bg-white box-shadow border-radius-10">
                <div class="login-title">
                    <h2 class="text-center text-primary">Reset Password</h2>
                </div>
                <h6 class="mb-20">Enter your new password, confirm and submit</h6>
                <form action="{{ route('admin.reset-password-handler', ['token' => request()->token]) }}" method="POST">
                    @csrf
                    @include('message_session.fail_message')

                    @include('message_session.success_message')

                    <div class="input-group custom">
                        <input type="password" class="form-control form-control-lg" placeholder="New Password"
                            name="new_password" value="{{ old('new_password') }}">
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                        </div>
                    </div>

                    @include('message_session.error_field_message', ['fieldName' => 'new_password'])

                    <div class="input-group custom">
                        <input type="password" class="form-control form-control-lg" placeholder="Confirm New Password"
                            name="confirm_new_password" value="{{ old('confirm_new_password') }}">
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                        </div>

                        @include('message_session.error_field_message', [
                            'fieldName' => 'confirm_new_password',
                        ])

                    </div>
                    <div class="row align-items-center">
                        <div class="col-5">
                            <div class="input-group mb-0">

                                <input class="btn btn-primary btn-lg btn-block" type="submit" value="Submit">


                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
