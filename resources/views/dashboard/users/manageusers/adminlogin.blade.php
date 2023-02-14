<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/dashboard/eliteadmin-theme/assets/images/favicon.png') }}">
    <title>{{ trans('main.Sign In') }}</title>

    <!-- page css -->
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/css/pages/login-register-lock.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/css/style.min.css') }}" rel="stylesheet">
    <!-- fontawesome-all -->
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/icons/font-awesome/css/fontawesome-all.css') }}" rel="stylesheet">

    <!-- Cairo Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Cairo&display=swap" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') }}"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') }}"></script>
<![endif]-->

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

    .login-register input{
        text-align: left;
        direction: ltr;
        padding-left: 5px !important;
    }

</style>
</head>

<body class="skin-default card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Marvel Dashboard</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <section id="wrapper">
        <div class="login-register" style="background-image:url({{ asset('assets/dashboard/eliteadmin-theme/assets/images/background/login-register.jpg') }});">
            <div class="login-box card">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="loginform" method="POST" action="{{ route('manageusers.adminlogin') }}">
                        @csrf
                        <h3 class="text-center m-b-20">{{ trans('main.Sign In') }}</h3>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input name="UserEmail" class="form-control" type="email" required="required"  placeholder="بريد العضو"
                                    aria-describedby="UserEmailHelp" placeholder="UserEmail" required value="{{ old('UserEmail') }}" required autofocus
                                >

                                @error('UserEmail')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group ">
                            <div class="col-xs-12">
                                <input name="email" class="form-control" type="email" required="required"  placeholder="{{ trans('main.email') }}"
                                    aria-describedby="emailHelp" placeholder="Email" required value="{{ old('email') }}" required autofocus
                                >

                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <input name="password" class="form-control" type="password" required="" placeholder="{{ trans('validation.attributes.password') }}" required autocomplete="current-password">
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <div class="col-xs-12 p-b-20">
                                <button class="btn btn-block btn-lg btn-info btn-rounded" type="submit">{{ trans('main.Log In') }}</button>
                            </div>
                        </div>

                        <div class="d-flex no-block align-items-center">
                            <div class="ml-auto">
                                <a href="{{ route('login') }}" id="to-recover" class="text-muted"><i
                                        class="fas fa-sign-in-alt m-r-5"></i>تسجيل الدخول  كمستخدم</a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!--Custom JavaScript -->
    <script type="text/javascript">
        $(function () {
            $(".preloader").fadeOut();
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
        // ==============================================================
        // Login and Recover Password
        // ==============================================================
        $('#to-recover').on("click", function () {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
    </script>

</body>

</html>