<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>تسجيل الدخول - مارفل</title>

        <!-- GOOGLE FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500"
            rel="stylesheet" />
        <link href="https://cdn.materialdesignicons.com/3.0.39/css/materialdesignicons.min.css" rel="stylesheet" />

        <!-- PLUGINS CSS STYLE -->
        <link href="{{ asset('assets/dashboard/plugins/toaster/toastr.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/plugins/nprogress/nprogress.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/plugins/flag-icons/css/flag-icon.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/plugins/jvectormap/jquery-jvectormap-2.0.3.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/plugins/ladda/ladda.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" />

        <!-- SLEEK CSS -->
        <link id="sleek-css" rel="stylesheet" href="{{ asset('assets/dashboard/css/sleek.rtl.css') }}" />

        <!-- FAVICON -->
        <link href="{{ asset('assets/dashboard/img/favicon.png') }}" rel="shortcut icon" />

        <!--
    HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries
  -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min') }}></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min') }}></script>
  <![endif]-->
        <script src="{{ asset('assets/dashboard/plugins/nprogress/nprogress.js') }}"></script>
    </head>

</head>

<body class="bg-light-gray" id="body">
    <div class="container d-flex flex-column justify-content-between vh-100">
        <div class="row justify-content-center mt-5">
            @yield('content')
        </div>
        <div class="copyright pl-0">
            <p class="text-center">&copy; 2018 Copyright Emadeldeen Dashboard Bootstrap
                <a class="text-primary" href="http://www.emadeldeen.com/" target="_blank">Emadeldeen</a>.
            </p>
        </div>
    </div>
</body>

</html>
