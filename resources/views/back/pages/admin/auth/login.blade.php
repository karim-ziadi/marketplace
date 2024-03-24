@extends('back.layout.auth-layout')
@section('pageTitle', isset($pageTilte) ? $pageTilte : 'Admin Login')
@section('content')
    <div class="row align-items-center">
        <div class="col-md-6 col-lg-7">
            <img src="{{ asset('back/vendors/images/login-page-img.png') }}" alt="" />
        </div>
        <div class="col-md-6 col-lg-5">
            <div class="login-box bg-white box-shadow border-radius-10">
                <div class="login-title">
                    <h2 class="text-center text-primary">Admin Login</h2>
                </div>
                <form action="{{ route('admin.login_handler') }}" method='post'>
                    @csrf

                    @include('message_session.fail_message')

                    @include('message_session.success_message')
                    <div class="select-role">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            {{-- <label class="btn">
											<input type="radio" name="options" id="admin">
											<div class="icon">
												<img src="{{asset('back/vendors/images/briefcase.svg')}}" class="svg" alt="">
											</div>
											<span>I'm</span>
											Manager
										</label>
										<label class="btn">
											<input type="radio" name="options" id="user">
											<div class="icon">
												<img src="{{asset('back/vendors/images/person.svg')}}" class="svg" alt="">
											</div>
											<span>I'm</span>
											Employee
										</label> --}}
                        </div>
                    </div>
                    <div class="input-group custom">
                        <input type="text" name="login_id"
                            value="{{ old('login_id') }}"class="form-control form-control-lg" placeholder="Email/Username">
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                        </div>
                    </div>
                    @include('message_session.error_field_message', [
                        'fieldName' => 'login_id',
                    ])
                    <div class="input-group custom">
                        <input type="password" name="password" class="form-control form-control-lg"
                            placeholder="**********">
                        <div class="input-group-append custom">
                            <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                        </div>
                    </div>
                    @include('message_session.error_field_message', [
                        'fieldName' => 'password',
                    ])
                    <div class="row pb-30">
                        <div class="col-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                <label class="custom-control-label" for="customCheck1">Remember</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="forgot-password">
                                <a href="{{ route('admin.forget-password') }}">Forgot Password</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group mb-0">
                                <!--
                               use code for form submit
                               <input class="btn btn-primary btn-lg btn-block" type="submit" value="Sign In">
                              -->
                                <input class="btn btn-primary btn-lg btn-block" type="submit" value="Sign In">
                                {{-- <a class="btn btn-primary btn-lg btn-block" href="index.html">Sign In</a> --}}
                            </div>
                            {{-- <div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373" style="color: rgb(112, 115, 115);">
											OR
										</div>
										<div class="input-group mb-0">
											<a class="btn btn-outline-primary btn-lg btn-block" href="register.html">Register To Create Account</a>
										</div> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
