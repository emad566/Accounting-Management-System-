@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل عميل</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('clients.index') }}" class="btn btn-primary float-right">كل العملاء</a>
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
                    <h6>تعديل العميل: {{ $client->client_Name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('clients.update', $client->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $client->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <?php
                            $readonly = (Auth::user()->can(['Accountant']) || Auth::user()->can(['SupperAdmin'])) ? '' : ' readonly="readonly" ' ;
                        ?>

                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'text', 'name'=>'client_name', 'transAttr'=>true, 'maxlength'=>50, 'required'=>'required', 'attr'=>$readonly, 'cols'=>6]) !!}
                            {!! select(['errors'=>$errors, 'edit'=>$client, 'name'=>'client_type_id', 'frkName'=>'name', 'rows'=>$client_types, 'transval'=>'النوع', 'label'=>true, 'required'=>'required', 'attr'=>$readonly, 'cols'=>3 ]) !!}
                        </div>

                        <div id="city_idrow" class="row city_idrow">
                            {!! select(['errors'=>$errors, 'edit'=>$client->clientarea, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'label'=>true, 'required'=>'required', 'attr'=>$readonly, 'cols'=>12 ]) !!}
                        </div>

                        <div id="region_idrow" class="row region_idrow"></div>

                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'text', 'name'=>'client_address', 'transAttr'=>true, 'maxlength'=>100, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'number', 'name'=>'client_phone', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="111" max="99999999999"']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'text', 'name'=>'client_manager_name', 'transAttr'=>true, 'maxlength'=>50, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'number', 'name'=>'client_manager_phone', 'transAttr'=>true, 'cols'=>3, 'attr'=>'min="111" max="99999999999"']) !!}
                        </div>

                        <div class="row">
                            <?php  $is_first_add = $client->is_first_add ? 'نعم' : "لا" ; ?>
                            {!! printData(['label'=>'موافقة الأضافة: ', 'data'=>$is_first_add, 'cols'=>6, 'id'=>'', 'class'=>'', ]) !!}
                            @if(Auth::user()->can(['Delete_Client']))
                                {!! input(['errors'=>$errors, 'edit'=>$client, 'type'=>'', 'name'=>'initial_balance', 'transval'=>'رصيد إفتتاحي', 'maxlength'=>50, 'required'=>'required', 'cols'=>3]) !!}
                                {!! checkbox(['errors'=>$errors, 'edit'=>$client, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>3, 'class'=>'switcher']) !!}
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
<script>
    $(document).ready(function(){
        function state_id(){
            if($('#state_id').val()){
                $.ajax({
                    url: '{{ route('regions.cities') }}',
                    data:{state_id:$('#state_id').val() @if(old('city_id')), edit_id:{{ old('city_id') }}@elseif($client), edit_id:{{$client->clientarea->city_id}}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#city_idrow>.city_id').remove()
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

        city_idNum = ''
        function city_id(){
            <?php $oldRegion_id = (old('region_id'))? old('region_id') : 'false'; ?>
            city_idNum = '{{ $client->clientarea->city_id }}'

            if($('#city_id').val()){
                city_idNum = $('#city_id').val()
            }else if({{ $oldRegion_id }}){
                city_idNum = {{ $oldRegion_id }}
            }

            if(city_idNum){
                $.ajax({
                    url: '{{ route('cities.regions') }}',
                    data:{city_id:city_idNum @if(old('region_id')), edit_id:{{ old('region_id') }}@elseif($client->clientarea->region_id), edit_id:{{$client->clientarea->region_id}}@endif},
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token()}}'
                    },
                    cache: false,

                    success: function (data) {
                        $('#region_idrow>.region_id').remove()
                        $('#region_idrow').append(data)
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

    });
</script>
@endsection

