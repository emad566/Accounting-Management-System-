@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل مخزن</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('stores.index') }}" class="btn btn-primary float-right">كل المخازن</a>
                <a href="{{ route('stores.show', $store->id) }}" class="btn btn-primary mx-2 float-right">عرض</a>
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
                    <h6>تعديل المخزن: {{ $store->Stroe_Name }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('stores.update', $store->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $store->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$store, 'name'=>'Store_Name', 'transval'=>'اسم المخزن', 'maxlength'=>50, 'required'=>'required', 'cols'=>12]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$store, 'name'=>'Store_Place', 'transval'=>'مكان المخزن', 'maxlength'=>50, 'required'=>'required', 'cols'=>12]) !!}
                        </div>

                        @if($store->id!=1)
                        <div class="row">
                            {!! checkbox(['errors'=>$errors, 'edit'=>$store, 'name'=>'is_active', 'trans'=>'Active', 'cols'=>12, 'class'=>'switcher']) !!}
                        </div>


                        <div class="relationElements">
                            <h5 class="col-xs-12 col-lg-12">أضف مناطق يعمل فيها المخزن:<hr> </h5>
                            <div id="city_idrow" class="row city_idrow">
                                {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'label'=>true, 'cols'=>12 ]) !!}
                            </div>
                            <div id="region_idrow" class="row region_idrow"></div>

                            <button type="button" id="addElement" class="btn btn-small btn-info my-1"><i class="fa fa-plus-circle"></i> أضف</button>

                            <div class="col-12 table-responsive">
                                <table id="productsTable" class="table table-hover table-bordered color-bordered-table purple-bordered-table">
                                    <thead>
                                        <th>#</th>
                                        <th>المحافظة</th>
                                        <th>المدينة</th>
                                        <th>المنطقة</th>
                                        <th>حذف</th>
                                    </thead>
                                    <tbody>
                                        <?php $i=0;
                                            $regions_ids = (old('regions'))? old('regions') : $store->regions->pluck('id');
                                        ?>
                                        @if ($regions_ids)
                                            @foreach ($regions_ids as $region)
                                                <?php $region = App\Models\Region::find($region); ?>
                                                @if($region)
                                                    <tr id="rowRegion{{ $region->id }}">
                                                        <td>{{ ++$i }}
                                                            <input type="hidden" name="regions[]" value="{{ $region->id }}" id="region_{{ $region->id }}" class="region_{{ $region->id }} regions">
                                                        </td>
                                                        <td>{{ $region->get_state_name() }}</td>
                                                        <td>{{ $region->get_city_name() }}</td>
                                                        <td>{{ $region->get_region_name() }}</td>
                                                        <td> <a href="#" delId="rowRegion{{ $region->id }}" class="elementDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                                    <tr>
                                                @endif
                                            @endforeach

                                        @endif
                                    </tbody>
                                </table>
                                <input type="hidden" value="{{ $i }}" id="lastCount">
                            </div>
                        </div>
                        @endif


                        <div class="row relationElements">
                            @if(!$users->isEmpty())
                            <h5 class="col-xs-12 col-lg-12">مستخدمين لهم صلاحيات علي هذا المخزن <hr> </h5>
                            <?php $i=0; ?>
                                @foreach ($users->sortBy('fullName') as $user)
                                    <?php
                                        if(old('users'))
                                            $check = (in_array($user->id ,old('users')) )? true : false;
                                        else
                                            $check = (in_array($user->id ,$store->users->pluck('id')->toArray()) )? true : false;
                                    ?>
                                    {!! checkbox(['errors'=>$errors, 'value'=>$user->id, 'name'=>'users[]', 'transval'=>$user->fullName . " (".$user->getRole("name").")", 'cols'=>3, 'check'=>$check]) !!}
                                @endforeach
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
                    data:{state_id:$('#state_id').val() @if(old('city_id')), edit_id:{{ old('city_id') }}@endif},
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


        $(document).on("click", "#addElement", function(e){
            lastCount = parseInt($("#lastCount").val()) +1;
            get_state_id = $("#state_id").val()
            if(get_state_id){
                get_city_id = $("#city_id").val()
                get_region_id = $("#region_id").val()

                state_txt = $("#state_id option:selected").text()
                city_txt = (get_city_id)? $("#city_id option:selected").text() : '';
                region_txt = (get_region_id)? $("#region_id option:selected").text() : '';

                if(!get_region_id && !get_city_id){
                    get_region_id = get_state_id
                }else if(!get_region_id && get_city_id){
                    get_region_id = get_city_id
                }

                if($("#rowRegion"+ get_region_id).length){
                    e.preventDefault();
                    return ;
                }
                $newRow = '<tr id="rowRegion'+ get_region_id +'">'
                            +    '<td>' + lastCount
                            +        '<input type="hidden" name="regions[]" value="'+ get_region_id +'" id="region_'+ get_region_id +'" class="region_'+ get_region_id +' regions">'
                            +    '</td>'
                            +    '<td>'+ state_txt +'</td>'
                            +    '<td>'+ city_txt +'</td>'
                            +    '<td>'+ region_txt +'</td>'
                            + '<td> <a href="#" delId="rowRegion'+  get_region_id +'" class="elementDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
                            +'<tr>'

                lastCount++;
                $("#lastCount").val(lastCount)
                tableBody = $("#productsTable tbody");
                tableBody.append($newRow);

                $("#state_id").val($("#state_id option:first").val());
                $("#city_id").remove()
                $("#region_id").remove()
            }else{
                alert("من فضلك إختر محافظة اولا، حتي تتمكن من إضافة منطقة")
            }
            $("#state_id").focus()
        });

        $(document).on("click", ".elementDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });

    });
</script>
@endsection

