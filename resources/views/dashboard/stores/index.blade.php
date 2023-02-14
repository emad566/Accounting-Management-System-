@extends('dashboard.master', ['datatable'=>1, 'form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">المخازن</h4>
        </div>
        <div class="col-md-7 align-self-center text-right" dir="rtl">
            @if(Auth::user()->can(['Cud stores']))
            <div class="d-flex justify-content-end align-items-center">
                <div id="verticalcenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="vcenter">أضف مخزن</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body text-left">
                                @php $cols = 12; @endphp
                                @include('dashboard.stores.createModel')
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">{{ trans('main.Close') }}</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <a href="{{ route('stores.create') }}" data-toggle="modal" data-target="#verticalcenter" class="btn btn-info  m-l-15"><i class="fa fa-plus-circle"></i> {{ trans('main.Add New') }}</a>
            </div>
            @endif
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
                    <h4 class="card-title">المخازن</h4>

                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('stores.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $indexEditDelActive = (Auth::user()->can(['Cud stores']))? true : false ;
                            $fields = [
                                // ['id', 'transval'=>'المعرف'],
                                ['Store_Name', 'transval'=>'الإسم'],
                                // ['Store_Place', 'transval'=>'المكان'],
                                // ['created_at->diffForHumans()', 'transval'=>'وقت الإنشاء'],
                                ];
                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$stores,
                                'table'=>'stores',
                                'title'=>'Store_Name',
                                'trans'=>'',
                                'transval'=>':المخزن',
                                'active'=>$indexEditDelActive,
                                'indexEdit'=>$indexEditDelActive,
                                'indexDel'=>$indexEditDelActive,
                                'isread'=>false,
                                'view'=>true,
                                'vars'=>false,
                                'fields'=>$fields,
                            ]) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End PAge Content -->
    <!-- ============================================================== -->
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

        lastCount = 1
        $(document).on("click", "#addElement", function(e){
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

