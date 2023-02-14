<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <?php $v=".1.8s"; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Marvel Login</title>
    
    <link href="{{ asset('css/normalize.css') }}" rel="stylesheet">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"> --}}
    <link href="{{ asset('css/login.css?v='.$v) }}" rel="stylesheet">

    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400&display=swap" rel="stylesheet">

</head>
<body>

    <!-- Start: Login -->
    <div id="loginwraber">
        <div class="container">
            <form class="form-horizontal form-material" id="loginform" method="get" action="{{ route('mainUser.userloged') }}">
                @csrf
                <h3 class="text-center m-b-20">{{ trans('main.Sign In') }}</h3>

                @include('dashboard.includes.alerts.success')
                @include('dashboard.includes.alerts.errors')

                <div class="form-group ">
                    <input name="email" class="form-control" type="text" required="required"
                        aria-describedby="emailHelp" placeholder="رقم الهاتف" required value="{{ old('email') }}" required autofocus
                    >

                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                        <input name="password" class="form-control" type="password" required="" placeholder="{{ trans('validation.attributes.password') }}" required autocomplete="current-password">
                </div>

                <div class="form-group row">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" checked value="1" class="custom-control-input checkbox" id="customCheck1">
                        <label class="custom-control-label" for="customCheck1">{!! trans('main.Remember me') !!}</label>
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn" type="submit">{{ trans('main.Log In') }}</button>
                </div>


            </form>
        </div><!-- .container -->
    </div><!-- #loginwraber -->
    <!-- End: Login -->

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> --}}
    <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>

    <script src="{{ asset('js/login.js') }}"></script>
    <script>
        /* ===============================
        ||  Start: login by broweser user token
        ================================== */
        $(document).ready(function(){
            device_token = localStorage.getItem("device_token");
            uid = localStorage.getItem("uid");
            if(device_token){
                $.ajax({
                    url: "{{ route('mainUser.logintoken') }}" ,
                    type: "post",
                    data:{device_token:device_token, uid:uid},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    cache: false,
        
                    success: function (data) {
                        if(data == 1)
                            window.location.replace("{{ route('dashboard') }}");
                    },
                    error: function (xhr) {
                        alert("Error: - " + xhr.status + " " + xhr.statusText);
                    }
                });
            }
            
        })
        /* End: login by broweser user token */ 
        
    </script>
</body>
</html>