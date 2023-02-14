@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كشف حساب عميل</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                {{-- <a href="{{ route('clients.index') }}" class="btn btn-primary float-right">كل العملاء</a> --}}
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">كشف الحساب</h6>
                    <hr>
                    @include('dashboard.clients.accountingModel')

                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        function state_id(){
            if($('#state_id').val()){
                $.ajax({
                    url: '{{ route('regions.cities') }}',
                    data:{state_id:$('#state_id').val() @if(old('city_id')), edit_id:{{ old('city_id') }}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#city_idrow>.city_id').remove()
                        data = data.replace("col-md-12", "col-md-4 col-sm-12");
                        $('div.region_id').remove()
                        $('#city_idrow').append(data)


                    },
                    error: function (xhr) {
                        alert("Error: - " + xhr.status + " " + xhr.statusText);
                    }
                });
            }

        }

        state_id()
        $('#state_id').change(function(){
            $("#region_id").remove();
            state_id()
        })

        after = '<div id="region_idrow" class="region_idrow col-md-6 col-sm-12 region_idrow"></div>';
        function city_id(){
            if($('#city_id').val()){
                $.ajax({
                    url: '{{ route('cities.regions') }}',
                    data:{city_id:$('#city_id').val() @if(old('region_id')), edit_id:{{ old('region_id') }}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#region_idrow>.region_id').remove()
                        data = data.replace("col-md-12", "col-md-4 col-sm-12");
                        $('div.region_id').remove()
                        $('#city_idrow .region_idrow').remove()
                        $('#city_idrow').append(after)
                        $('#city_idrow #region_idrow').replaceWith(data)
                    },
                    error: function (xhr) {
                        alert("Error: - " + xhr.status + " " + xhr.statusText);
                    }
                });
            }
        }

        city_id()
        $(document).on("change", "#city_id", function(e){
            city_id()

        })

        function getclients(region_id, type) {
            $.ajax({
                url: '{{ route('clients.getclients') }}',
                data:{region_id:region_id, type:type},
                type: "get",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token()}}'
                },
                cache: false,

                success: function (data) {
                   $("#client_idrow").html()
                   $("#client_idrow").html(data)
                   $("#client_id").attr("data-live-search", true)
                   $('select#client_id').selectpicker();
                //    $('select').selectpicker();
                },
                error: function (xhr) {
                    alert("Error: - " + xhr.status + " " + xhr.statusText);
                }
            });
        }

        $(document).on('change', '#state_id, #city_id, #region_id', function (params) {
            if($("select#region_id").val()){
                getclients($("select#region_id").val(), "region_id")
                // alert($("select#region_id").val())
            }else if($("select#city_id").val()){
                getclients($("select#city_id").val(), "city_id")
                // alert($("select#city_id").val())
            }else{
                // alert($("select#state_id").val())
                getclients($("select#state_id").val(), "state_id")
            }
        })
        // var client_id = ''
        // $(document).on('DOMSubtreeModified', '.filter-option-inner-inner', function (params) {
        //     if($("select#client_id").val() && client_id!==$("select#client_id").val()){
        //         client_id = $("select#client_id").val()

        //         window.location.href = "{{ route('clients.accounting') }}/"+client_id;
        //     }
        // })

        $(document).on('change', '.is_period', function () {
            if($(this).prop("checked") == true){
                $("#start_date, #end_date").removeAttr('disabled')
            }else{
                $("#start_date, #end_date").attr('disabled', 'disabled')

            }
        })

        $('select#client_id').selectpicker();

        $(document).on('click', '.head', function () {
            $(this).next('.acountantDiv:first').toggle();
        })
    });
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
@endsection

