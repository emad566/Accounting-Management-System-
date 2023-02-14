@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'كشف حساب مصروف'])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">التقارير</h4>
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
                    <h6 class="card-title">كشف  حساب مصروف</h6>
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            {!! select(['errors'=>'', 'name'=>'cat_id', 'frkName'=>'cat_name', 'rows'=>$cats, 'transAttr'=>true, 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
                            {!! input(['errors'=>$errors, 'name'=>'spend_date_from', 'type'=>'date', 'value'=>(new Carbon\Carbon('2021-01-01'))->isoFormat('YYYY-MM-DD'), 'transval'=>'من', 'label'=>true, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'spend_date_to', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'إلي', 'label'=>true, 'cols'=>3]) !!}
                        </div>
                        <div id="city_idrow" class="row city_idrow">
                            {!! select(['errors'=>$errors, 'name'=>'state_id', 'frkName'=>'r_name', 'rows'=>$states, 'transval'=>'اختر المحافظة', 'selected'=>4, 'label'=>true, 'cols'=>2 ]) !!}
                        </div>
                        <div class="row">
                            {!! buttonAction('', 'بحث <i class="fab fa-searchengin"></i>', 'searchbtn', false) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">التقرير</h6>
                    <div class="row">
                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                    </div>

                    <div class="table-responsive m-t-40">
                        <table class="table table-hover table-bordered mobileTable table-striped" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>السند</th>
                                    <th>التفاصيل</th>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    <th>الرصيد</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">إجمالي</td>
                                    <td id="depit"></td>
                                    <td id="credit"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
    $('document').ready(function(){
        //Start yajra DataTable
        function yajraintialize(form = false){
            $(function() {
                var ajaxDec = {
                    "url": '{!! route('reports.yajracatbalance') !!}',
                    "type": "GET",
                    "data": function(d) {
                    var frm_data = $('#searchForm').serializeArray();
                        $.each(frm_data, function(key, val) {
                            d[val.name] = val.value;
                        });
                    }
                }
                var table = $('#yajraTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive" : true,
                    "searching": false,

                    "ajax": ajaxDec,

                    "dataSrc": function (json) {
                        json = JSON.parse(json);
                        return json.data;
                    },

                    drawCallback:function(settings, row, data, start, end, display){
                        $('#depit').html(parseFloat(settings.json.depit).toFixed(2));
                        $('#credit').html(parseFloat(settings.json.credit).toFixed(2));
                    },


                    "columns":[
                        { data: 'date_str', name: 'date_str'},
                        { data: 'spend_code', name: 'spend_code'},
                        { data: 'details', name: 'details'},
                        { data: 'depit', name: 'depit'},
                        { data: 'credit', name: 'credit'},
                        { data: 'cat_balance', name: 'cat_balance'}
                    ],


                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        // { "width": "1%",  "targets": [3] },       //Show on all devices
                        { "className": 'datetimeformat', "targets": [0] },       //Show on all devices
                        // { "className": 'not-mobile', "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16] },       //Show on all devices
                        // { "className": 'mobile', "targets": [0,1,2,3] },       //Show on all devices
                    ],



                    "language": {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
                    },
                    "dom": 'Blfrtip',
                    "buttons": [
                        {
                            extend: 'copyHtml5',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            footer: true,
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        'colvis'
                    ],

                    "displayLength": 20,
                    "pageLength": 20,
                    "lengthMenu": [[1, 2, 10, 20, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [1, 2, 10, 20, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

                });
            });
        }
        yajraintialize(false)


        //End yajra DataTable
        $(document).on('submit', '#searchForm', function(e){
            $(this).closest('#yajraTable input').remove();
            $("#yajraTable").dataTable().fnDestroy()
            yajraintialize(true)
            e.preventDefault();
            return false;
        })
        $('select').selectpicker();

        //Region JSCode
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
                        data = data.replace("col-md-12", "col-md-2 col-sm-12");
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
                        data = data.replace("col-md-12", "col-md-2 col-sm-12");
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
        //End Region JSCode

    });
</script>
<style>
    #searchbtn{
        float: left;
    }
</style>
@endsection

