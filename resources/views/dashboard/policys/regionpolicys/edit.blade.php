@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"> تعديل سياسات منطقة</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('products.index') }}" class="btn btn-primary float-right">كل الأصناف</a>
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
                    <h6> تعديل سياسات المنطقة</h6>
                    <hr>
                    <form method="POST" action="{{ route('regionpolicys.update') }}" class="" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input id="edit_id" type='hidden' name='edit_id' value="{{ old('state_id') }}">

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div id="city_idrow" class="row city_idrow">
                            {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'selected'=>4, 'label'=>true, 'required'=>'required', 'cols'=>4 ]) !!}
                        </div>

                        <div id="regionpolicy" class="row">
                            @if(old('state_id'))
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'client_due_limit', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="1000000" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من السياسات العامة']) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'paid_discount', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من السياسات العامة']) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'due_discount', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="0" max="100" step="0.01"', 'view'=>'اترك الحقل فارخ ليرث من السياسات العامة']) !!}
                            {!! select(['errors'=>$errors, 'name'=>'is_multi_due_inherit_id', 'frkName'=>'name', 'rows'=>$isinherits, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
                            @endif
                        </div>


                        <div class="row">
                            {!! buttonAction() !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

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

        function getclients(region_id) {
            var urlStr = '{{ url('/dashboard/policys/regionpolicys/featch/') }}/' + region_id
            // alert(urlStr)
            $.ajax({
                url: urlStr,
                type: "get",
                cache: false,

                success: function (data) {
                   $("#regionpolicy").html(data)
                   $("#city_id").attr("data-live-search", true)
                   $("#region_id").attr("data-live-search", true)
                   $('.city_idrow select').selectpicker();
                },
                error: function (xhr) {
                    alert("Error: - " + xhr.status + " " + xhr.statusText);
                }
            });
        }

        $(document).on('change', '#state_id, #city_id, #region_id', function (params) {
            if($("select#region_id").val()){
                getclients($("select#region_id").val())
                $('#edit_id').val($("select#region_id").val())
            }else if($("select#city_id").val()){
                getclients($("select#city_id").val())
                $('#edit_id').val($("select#city_id").val())
            }else{
                getclients($("select#state_id").val())
                $('#edit_id').val($("select#state_id").val())
            }
        })



        $('.city_idrow select').selectpicker();
    });
</script>
@endsection

