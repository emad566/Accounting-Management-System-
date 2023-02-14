
<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <?php $ver='1.11'; ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/dashboard/eliteadmin-theme/assets/images/favicon.png') }}">
    <?php $title = (isset($title))? $title : "Marvel Dashboard";?>
    <title> {{ $title }}</title>
    <!-- This page CSS -->

    @isset($charts)
        <!-- chartist CSS -->
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/morrisjs/morris.css') }}" rel="stylesheet">
    @endisset

    @isset($toast)
        <!--Toaster Popup message CSS -->
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
    @endisset

    @isset($form)
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/switchery/dist/switchery.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    @endisset

    <!-- Font Aweseme -->
    <script src="https://kit.fontawesome.com/c7125b87e6.js" crossorigin="anonymous"></script>

    <!-- <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/icons/font-awesome/css/fontawesome-all.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/icons/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/icons/weather-icons/css/weather-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/icons/themify-icons/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/icons/flag-icon-css/flag-icon.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/assets/icons/material-design-iconic-font/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Cairo&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('assets/dashboard/eliteadmin-theme/css/style.min.css?v=11') }}" rel="stylesheet">

    @isset($dashboard1)
        <!-- Dashboard 1 Page CSS -->
        <link href="{{ asset('assets/dashboard/eliteadmin-theme/css/pages/dashboard1.css') }}" rel="stylesheet">
    @endisset

    <!-- toaster -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    @isset($form)
        <!-- Strat Form CSS -->
        <link id="sleek-css" rel="stylesheet" href="{{ asset('assets/dashboard/eliteadmin-theme/css/perfect-scrollbar.min.css') }}" />
        <link id="sleek-css" rel="stylesheet" href="{{ asset('assets/dashboard/eliteadmin-theme/css/select2.min.css') }}" />
        <!-- /End Form CSS -->
    @endempty

    <!-- Emad CSS -->
    @empty($emad_rtl)
        <link id="sleek-css" rel="stylesheet" href="{{ asset('assets/dashboard/css/emad-rtl.css?v='.$ver) }}" />
    @endempty

    <link id="sleek-css" rel="stylesheet" href="{{ asset('assets/dashboard/css/emad-web.css?v='.$ver) }}" />

    {{-- DataTable CSS --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.25/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/r-2.2.9/datatables.min.css"/>
    {{-- DataTable CSS --}}
    @yield('style')

    <?
    $user = Auth::user();
    ?>
    <style>
        .dataTable th, .dataTable td {
            font-size: {{ Auth::user()->dt_font_size }}px;
        }
        body table.dataTable tbody th, body table.dataTable tbody td {
            padding: {{ intval(Auth::user()->dt_font_size*.40) }}px;
        }

    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js') }}"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') }}"></script>
<![endif]-->

@if($_SERVER['SERVER_NAME'] == 'test.marvel-inter.com')
<style>
    .page-wrapper{
        /* background-color: #f90; */
    }
</style>
@endif
</head>

<body class="skin-default fixed-layout rtl">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    @include('dashboard.layouts.preloader')

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                @include('dashboard.layouts.navbar-header')

                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        {{-- <li class="nav-item">
                            <form class="app-search d-none d-md-block d-lg-block">
                                <input type="text" class="form-control" placeholder="Search & enter">
                            </form>
                        </li> --}}
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->

                        <!-- Headicons-->
                        @include('dashboard.layouts.headicons')
                        <!-- End headicons-->

                        <!-- Comment -->
                        @include('dashboard.layouts.comments')
                        <!-- End Comment -->


                        <!-- ============================================================== -->

                        <!-- ============================================================== -->
                        <!-- Messages -->
                        <!-- ============================================================== -->
                        {{-- @include('dashboard.layouts.messages') --}}
                        <!-- ============================================================== -->
                        <!-- End Messages -->
                        <!-- ============================================================== -->

                        <!-- ============================================================== -->
                        <!-- mega menu -->
                        <!-- ============================================================== -->
                        {{-- @include('dashboard.layouts.mega-dropdown') --}}
                        <!-- ============================================================== -->
                        <!-- End mega menu -->
                        <!-- ============================================================== -->

                        <!-- ============================================================== -->
                        <!-- User Profile -->
                        <!-- ============================================================== -->
                        {{-- @include('dashboard.layouts.user-profile') --}}
                        <!-- ============================================================== -->
                        <!-- End User Profile -->
                        <!-- ============================================================== -->
                        {{-- <li class="nav-item right-side-toggle"> <a class="nav-link  waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a></li> --}}
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        @include('dashboard.layouts.sidebar')

        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper" @if(App\Models\ViewCheckQuantities::where('in_deff', '<>', 0)->count()>0)style="background: #f90"@endif>
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            @if(App\Models\ViewCheckQuantities::where('in_deff', '<>', 0)->count()>0)<p style="background: blue">{{ App\Models\ViewCheckQuantities::where('in_deff', '<>', 0)->count() }}</p>
            @if(auth::id() == 1)
                <h1>الرجاء الإتصال بمطور الموقع حالا من فضلك.</h1>
                @yield('content')
            @else
                <h1>الرجاء الإتصال بمطور الموقع حالا من فضلك.</h1>
                <?php webErrorNotif(); ?>
            @endif
            @else 
                <p id="token" style="display:none;"></p>
                {{-- <h1>جاري العمل علي السيستم</h1> --}}
                
                @yield('content')
            @endif
            {{-- @if(auth::id() == 1)
                @else
            جاري التحيث  --}}
            {{-- @endif --}}
            {{-- @include('dashboard.errors.alarm') --}}
            {{-- @if(App\Models\ViewCheckQuantities::where('in_deff', '<>', 0)->count()>0 && !isset($report)) --}}
                
                {{-- @include('dashboard.errors.alarm') --}}
                {{-- @yield('content')
            @else 
                    @yield('content')
            @endif --}}

             
            @include('dashboard.layouts.right-sidebar')
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        @include('dashboard.layouts.footer')
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>

    <script>
        // var x = document.getElementById("demo");
        // function getLocation() {
        //     if (navigator.geolocation) {
        //         navigator.geolocation.getCurrentPosition(showPosition);
        //     } else {
        //         x.innerHTML = "Geolocation is not supported by this browser.";
        //     }
        // }
        
        // function showPosition(position) {
        //     x.innerHTML = "Latitude: " + position.coords.latitude +
        //     " | Longitude: " + position.coords.longitude;
        // }
        // getLocation()
    </script>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
    <!-- Bootstrap popper Core JavaScript -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap/dist/js/bootstrap.min.js?v=2') }}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme//js/perfect-scrollbar.jquery.min.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme//js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme//js/sidebarmenu.js') }}"></script>
    @empty($ishome)
        <!--stickey kit -->
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/sparkline/jquery.sparkline.min.js') }}"></script>
    @endempty
    <!--Custom JavaScript -->
    <script src="{{ asset('assets/dashboard/eliteadmin-theme//js/custom.min.js') }}"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    @isset($morris)
        <!--morris JavaScript -->
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/raphael/raphael-min.js') }}"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/morrisjs/morris.min.js') }}"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
    @endisset

    @isset($toast)
        <!-- Popup message jquery -->
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>
    @endisset

    @isset($dashboard1)
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/js/dashboard1.js?v='.$ver) }}"></script>
    @endisset

    @isset($charts)
        <!-- Chart JS -->
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>
    @endisset



    @isset($datatable)


        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>

        <!-- dataTable Libs -->
        <!-- This is data table -->
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/datatables/datatables.min.js') }}"></script>
        <!-- start - This is for export functionality only -->
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
        <!-- end - This is for export functionality only -->
        <!-- datadtable JS Commands -->
        {{-- DataTable JS --}}
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.25/b-1.7.1/b-colvis-1.7.1/b-html5-1.7.1/b-print-1.7.1/r-2.2.9/datatables.min.js"></script>
        {{-- DataTable JS --}}
        <script>
            // $(function() {

            //     // $('.datatable').DataTable();
            //     $(function() {
            //         var table = $('.datatable').DataTable({
            //             "responsive" : true,
            //             "language": {
            //                 url: '{{ asset('json/Arabic.json') }}',
            //             },
            //             "dom": 'Blfrtip',
            //             "buttons": [
            //                 'copy', 'excel', 'print',
            //             ],

            //             "displayLength": 50,
            //             "lengthMenu": [[2,4,8,10,20, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [2,4,8,10,20, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]
            //         });
            //         // Order by the grouping
            //         $('.datatable tbody').on('click', 'tr.group', function() {
            //             var currentOrder = table.order()[0];
            //             if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
            //                 table.order([2, 'desc']).draw();
            //             } else {
            //                 table.order([2, 'asc']).draw();
            //             }
            //         });
            //     });


            // });
            // $('.buttons-copy, .buttons-print, .buttons-excel').addClass('btn btn-primary mr-1');

        </script>

    @endisset <!-- /End isset DataTable -->
    <script>
        $(document).ready(function () {
            //Style MobileTable mobile
            $(".datatable, .mobileTable").parent('div').addClass('responsive-container')
            txt = $(".datatable, .mobileTable").parents('div.card-body').children('.card-title')
            txt.append(' <label class="labelshowastable" for="showastable">(<input checked id="showastable" type="checkbox">  عرض كجدول  )</label>')
            txtMobile = $(".mobileTable").parents('div.modal-content').children('div.modal-header')
            txtMobile.append(' <label class="labelshowastable" for="showastable">(<input id="showastable" checked type="checkbox">  عرض كجدول  )</label>')
            $i=1;
            $(".datatable thead tr th, .mobileTable  thead tr th").each(function () {
                $(".responsive-container table tr td:nth-child("+ $i++ +")").attr('data-th', $(this).text())
            })

            $(document).on("change", "#showastable", function () {
                if($(this).prop("checked") == true){
                    $(".responsive-container").removeClass("responsive-container")
                    $(".mobileTable  thead").show()
                }else{
                    $(".mobileTable  thead").hide()
                    $(".datatable, .mobileTable").parent('div').addClass('responsive-container')
                }
            })

            $(".responsive-container").removeClass("responsive-container")
            $(".mobileTable  thead").show()
        })
    </script>

    @isset($form)
        <!-- ============================================================== -->
        <!-- This page plugins -->
        <!-- ============================================================== -->
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/js/highlight.pack.js') }}"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/js/select2.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script> --}}
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/dff/dff.js') }}" type="text/javascript"></script>
        <script type="text/javascript" src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/multiselect/js/jquery.multi-select.js') }}"></script>
        <script>
            /** Start Select2 */
            $(function(){

                'use strict';

                $('.select2').select2({
                minimumResultsForSearch: Infinity
                });

                // Select2 by showing the search
                $('.select2-show-search').select2({
                minimumResultsForSearch: ''
                });

                // Select2 with tagging support
                $('.select2-tag').select2({
                    tags: true,
                    tokenSeparators: [',', ' ']
                });
            });
            /** /End Select2 */

            $(function () {
                // Switchery
                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                $('.js-switch').each(function () {
                    new Switchery($(this)[0], $(this).data());
                });
                // For select 2
                $(".select2").select2();
                $('.selectpicker').selectpicker();
                //Bootstrap-TouchSpin
                $(".vertical-spin").TouchSpin({
                    verticalbuttons: true
                });
                var vspinTrue = $(".vertical-spin").TouchSpin({
                    verticalbuttons: true
                });
                if (vspinTrue) {
                    $('.vertical-spin').prev('.bootstrap-touchspin-prefix').remove();
                }
                $("input[name='tch1']").TouchSpin({
                    min: 0,
                    max: 100,
                    step: 0.1,
                    decimals: 2,
                    boostat: 5,
                    maxboostedstep: 10,
                    postfix: '%'
                });
                $("input[name='tch2']").TouchSpin({
                    min: -1000000000,
                    max: 1000000000,
                    stepinterval: 50,
                    maxboostedstep: 10000000,
                    prefix: '$'
                });
                $("input[name='tch3']").TouchSpin();
                $("input[name='tch3_22']").TouchSpin({
                    initval: 40
                });
                $("input[name='tch5']").TouchSpin({
                    prefix: "pre",
                    postfix: "post"
                });
                // For multiselect
                $('#pre-selected-options').multiSelect();
                $('#optgroup').multiSelect({
                    selectableOptgroup: true
                });
                $('#public-methods').multiSelect();
                $('#select-all').click(function () {
                    $('#public-methods').multiSelect('select_all');
                    return false;
                });
                $('#deselect-all').click(function () {
                    $('#public-methods').multiSelect('deselect_all');
                    return false;
                });
                $('#refresh').on('click', function () {
                    $('#public-methods').multiSelect('refresh');
                    return false;
                });
                $('#add-option').on('click', function () {
                    $('#public-methods').multiSelect('addOption', {
                        value: 42,
                        text: 'test 42',
                        index: 0
                    });
                    return false;
                });
                $(".ajax").select2({
                    ajax: {
                        url: "https://api.github.com/search/repositories",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            params.page = params.page || 1;
                            return {
                                results: data.items,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    minimumInputLength: 1,
                    //templateResult: formatRepo, // omitted for brevity, see the source of this page
                    //templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
                });
            });
        </script>
    @endisset <!-- /End isset Form -->

    @isset($textEeditor)
        <script src="https://cdn.tiny.cloud/1/6te8nxu8ugbz1akvw3cxy805yq5paofquv20a2vtc50ksxd2/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
        {{-- <script src="{{ asset('assets/dashboard/eliteadmin-theme/assets/node_modules/tinymce/tinymce.min.js') }}"></script> --}}
        <script>
           tinymce.init({
                selector: ".textEeditor",
                plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter permanentpen pageembed charmap tinycomments mentions quickbars linkchecker emoticons advtable',
  tinydrive_token_provider: 'URL_TO_YOUR_TOKEN_PROVIDER',
  tinydrive_dropbox_app_key: 'YOUR_DROPBOX_APP_KEY',
  tinydrive_google_drive_key: 'YOUR_GOOGLE_DRIVE_KEY',
  tinydrive_google_drive_client_id: 'YOUR_GOOGLE_DRIVE_CLIENT_ID',
  mobile: {
    plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker textpattern noneditable help formatpainter pageembed charmap mentions quickbars linkchecker emoticons advtable'
  },
  menu: {
    tc: {
      title: 'TinyComments',
      items: 'addcomment showcomments deleteallconversations'
    }
  },
  menubar: 'file edit view insert format tools table tc help',
  toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
  autosave_ask_before_unload: true,
  autosave_interval: '30s',
  autosave_prefix: '{path}{query}-{id}-',
  autosave_restore_when_empty: false,
  autosave_retention: '2m',
  image_advtab: true,
  link_list: [
    { title: 'My page 1', value: 'https://www.tiny.cloud' },
    { title: 'My page 2', value: 'http://www.moxiecode.com' }
  ],
  image_list: [
    { title: 'My page 1', value: 'https://www.tiny.cloud' },
    { title: 'My page 2', value: 'http://www.moxiecode.com' }
  ],
  image_class_list: [
    { title: 'None', value: '' },
    { title: 'Some class', value: 'class-name' }
  ],
  importcss_append: true,
  templates: [
        { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
    { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
    { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
  ],
  template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
  template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
  height: 300,
  image_caption: true,
  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
  noneditable_noneditable_class: 'mceNonEditable',
  toolbar_mode: 'sliding',
  spellchecker_whitelist: ['Ephox', 'Moxiecode'],
  tinycomments_mode: 'embedded',
  content_style: '.mymention{ color: gray; }',
  contextmenu: 'link image imagetools table configurepermanentpen',
  a11y_advanced_options: true,
//   skin: useDarkMode ? 'oxide-dark' : 'oxide',
//   content_css: useDarkMode ? 'dark' : 'default',
  /*
  The following settings require more configuration than shown here.
  For information on configuring the mentions plugin, see:
  https://www.tiny.cloud/docs/plugins/premium/mentions/.
  */
  mentions_selector: '.mymention',
//   mentions_fetch: mentions_fetch,
//   mentions_menu_hover: mentions_menu_hover,
//   mentions_menu_complete: mentions_menu_complete,
//   mentions_select: mentions_select
            });
        </script>
    @endisset


    <!-- swal alert Files -->
    <script src="{{ asset('https://unpkg.com/sweetalert/dist/sweetalert.min.js')}}"></script>
    <!-- /swal alert Files -->

    <!-- toaster -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- Rotate Lib --}}
    {{-- <script type="text/javascript" src="https://raw.githubusercontent.com/wilq32/jqueryrotate/master/jQueryRotate.js"></script> --}}
    <script src="{{ asset('js/jquery.rotate.1-1.js?ver='.$ver) }}"></script>
    <!-- manual Scripts -->
    @empty($manualScripts)
        <script>

            @if(Session::has('message') || Session::has('error') )
                var type = "{{ Session::get('alert-type', 'info') }}"
                switch(type){
                    case 'info':
                        toastr.info("{{ Session::get('message') }}")
                    break;

                    case 'success':
                        toastr.success("{{ Session::get('message') }}")
                    break;

                    case 'warning':
                        toastr.warning("{{ Session::get('message') }}")
                    break;

                    case 'error':
                        toastr.error("{{ Session::get('message') }}")
                    break;

                    default:
                        toastr.info("ALERT");
                    break;
                }
            @endif

            $(document).ready(function(){
                /* ======================================
                priew image before uploaded
                ===================================== */
                $('.uploadbtn').change(function(){
                    if ($(this).prop('files') && $(this).prop('files')[0]) {
                        var reader = new FileReader();
                        $imgId = $(this).attr('img');
                        reader.onload = function (e) {

                            $('.uploadbtnDiv img.'+$imgId).attr('src', e.target.result);
                            $('.uploadbtnDiv img.'+$imgId).addClass('showimg');
                        }
                        imgsize = $(this).prop('files')[0]['size']
                        imgsize = Math.round(imgsize / 1024, 4) + ' KB';

                        imgname = $(this).prop('files')[0]['name']

                        $('div.'+$imgId+' #image_size').html(imgsize);
                        $('div.'+$imgId+' #image_name').html(imgname);

                        reader.readAsDataURL($(this).prop('files')[0]);
                    }
                })

                $('.pdfuploadbtn').change(function(){
                    if ($(this).prop('files') && $(this).prop('files')[0]) {
                        var reader = new FileReader();
                        $imgId = $(this).attr('img');
                        reader.onload = function (e) {

                            // $('.pdfuploadbtn img.'+$imgId).attr('src', https://mwjood.emadeldeen.com/assests/images/pdf.png);
                            $('.pdfuploadbtn img.'+$imgId).addClass('showimg');
                        }
                        imgsize = $(this).prop('files')[0]['size']
                        imgsize = Math.round(imgsize / 1024, 4) + ' KB';

                        imgname = $(this).prop('files')[0]['name']

                        $('div.'+$imgId+' #image_size').html(imgsize);
                        $('div.'+$imgId+' #image_name').html(imgname);

                        reader.readAsDataURL($(this).prop('files')[0]);
                    }
                })



                //Delete Verfy
                $(document).on("click", ".deleteMe", function(e){
                    e.preventDefault();
                    var link = $(this).attr("href");
                    var msg = $(this).attr("msg");
                    var formId = $(this).attr("formId");
                    if(!msg) msg = "هل حقا تريد الحذف، سيتم الحذف بشكل نهائي.!"
                    swal({
                        title: "هل حقا تريد الحذف؟",
                        text: msg,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                        if(formId)
                            document.getElementById(formId).submit();
                        else
                            window.location.href = link;
                        } else {
                        swal("لم يتم الحذف.");
                        }
                    });
                });


                //UpdateIsActive
                $(document).on("change", ".updateIsActive", function(e){
                    action = $(this).attr('action')
                    window.location.href = action;
                })

                // MultiSelect checkbox for delete
                $(document).on("change", '#allItems', function(event) {
                    if($(this).is(':checked')){
                        $('.boxItem').prop('checked', true)
                    }else{
                        $('.boxItem').prop('checked', false)
                    }
                });

                //Prevent form submit on Enter
                $("form").on("keypress", function (event) {
                    console.log("aaya");
                    var keyPressed = event.keyCode || event.which;
                    if (keyPressed === 13) {
                        // alert("You pressed the Enter key!!");
                        event.preventDefault();
                        return false;
                    }
                });
                $("#rotateImg").on('click', function(e){
                    e.preventDefault();
                    // alert("#"+$("#rotateImg").attr('rotatedId'))
                    $("#"+$("#rotateImg").attr('rotatedId')).rotateRight(90);
                    // $("#"+$("#rotateImg").attr('rotatedId')).rotate(45);
                })

            })
        </script>
    @endempty <!-- /end empty manual Scripts -->

    {{-- Strart Pusher scripts --}}
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>

        // Enable pusher logging - don't include this in production
        // Pusher.logToConsole = true;

        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
          cluster: 'mt1'
        });

    </script>
    {{-- <script src="{{ asset('js/notify-emad.js?ver='.$ver) }}"></script> --}}
    
    @include('dashboard.includes.js.notifyjs')
    {{-- End Strart Pusher scripts --}}

    {{-- Notif Link --}}
    <script>
        $(document).on('click', '.notiflink', function(e){
            $.ajax({
                url: "{{ url("dashboard/notifsreaded/") }}/" + $(this).attr('notId'),

                type: 'GET',
                cache:false,
                success: function(data){

                    },
                error: function(xhr){
                        alert(xhr.status+' '+xhr.statusText);
                    }
            });
        })

    </script>
    {{-- /Notif Link  --}}

    {{-- FireBase --}}
    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js"></script>
    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyAsundhx-g7706V6iqu0awyjLcrspTnpeM",
            authDomain: "marvel-sale.firebaseapp.com",
            projectId: "marvel-sale",
            storageBucket: "marvel-sale.appspot.com",
            messagingSenderId: "731502628563",
            appId: "1:731502628563:web:3686971b5582ad29274e26"
        };
        
        firebase.initializeApp(firebaseConfig);
        const messaging=firebase.messaging();

        var device_token = localStorage.getItem("device_token");
        var tokendone = localStorage.getItem("tokendone");
        function IntitalizeFireBaseMessaging() {
            localStorage.setItem('uid', "{{ Auth::id() }}")
            messaging
                .requestPermission()
                .then(function () {
                    console.log("Notification Permission");
                    return messaging.getToken();
                })
                .then(function (token) {
                    
                    if(token){
                        console.log("Unstorge")
                        
                        $.ajax({
                            url: '{{ route('manageusers.usertokens') }}',
                            data:{token:token},
                            type: "post",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token()}}'
                            },
                            cache: false,

                            success: function (data) {
                                console.log("registered =======================")
                                localStorage.setItem("device_token", token);
                                localStorage.setItem("tokendone", token);
                            },
                            error: function (xhr) {
                                console.log("Error: {{ route('manageusers.usertokens') }} - " + xhr.status + " " + xhr.statusText);
                            }
                        });
                    }
                    
                    console.log("Token : "+token);
                    document.getElementById("token").innerHTML=token;
                })
                .catch(function (reason) {
                    console.log(reason);
                    document.getElementById("token").innerHTML=reason;
                });
        }

        messaging.onMessage(function (payload) {
            console.log(payload);
            const notificationOption={
                body:payload.notification.body,
                icon:payload.notification.icon
            };

            if(Notification.permission==="granted"){
                var notification=new Notification(payload.notification.title,notificationOption);

                notification.onclick=function (ev) {
                    ev.preventDefault();
                    window.open(payload.notification.click_action,'_blank');
                    notification.close();
                }
            }

        });

        messaging.onTokenRefresh(function () {
            messaging.getToken()
                .then(function (newtoken) {
                    console.log("New Token : "+ newtoken);
                })
                .catch(function (reason) {
                    console.log(reason);
                    //alert(reason);
                })
        })

        if(!tokendone){
            document.querySelectorAll('#sidebarnav a').forEach(elm => {
                elm.addEventListener('click', function(e) {
                    IntitalizeFireBaseMessaging();
                    // e.preventDefault()
                });
            });
        }
    </script>
    {{-- #End FireBase --}}

    @yield('script')

   
    

</body>

</html>
