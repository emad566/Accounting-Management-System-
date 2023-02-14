@extends('dashboard.master-login')
<!-- Cairo Fonts -->
<link href="https://fonts.googleapis.com/css?family=Cairo&display=swap" rel="stylesheet">

@section('content')
<?php $start_time = microtime(true); ?>
<style>
    body {
    --first-color:#36a265;
    --second-color:#161f6a;
    --third-color:#7d50de;
    font-family: 'Cairo', Georgia, 'Times New Roman', Times, serif;
    background-color:#f5f7fb
    }

    .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
        font-family: 'Cairo', Georgia, 'Times New Roman', Times, serif;
    }

</style>
    <div class="col-xl-5 col-lg-6 col-md-10">
        <div class="card">
            <div class="card-header bg-primary">
                <div class="app-brand">
                    <a href="https://www.emadeldeen.com" style="width:100%">
                        <svg class="brand-icon" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="30"
                            height="33" viewBox="0 0 30 33">
                            <g fill="none" fill-rule="evenodd">
                                <path class="logo-fill-blue" fill="#7DBCFF" d="M0 4v25l8 4V0zM22 4v25l8 4V0z" />
                                <path class="logo-fill-white" fill="#FFF" d="M11 4v25l8 4V0z" />
                            </g>
                        </svg>
                        <span class="brand-name" style="width:100%">Emadeldeen Dashboard</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-5">

                <h4 class="text-dark mb-5">{{ trans('main.sendverify') }}</h4>

                <div class="mb-4 text-sm text-gray-600">
                    {{ trans('main.Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ trans('main.A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                @include('dashboard.includes.alerts.success')
                @include('dashboard.includes.alerts.errors')
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <div class="row">

                        <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">{{ trans('main.Resend Verification Email') }}</button>

                    </div>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                        {{ trans('main.Logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection


