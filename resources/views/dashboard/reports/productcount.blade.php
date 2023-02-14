@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'أرصدة الأصناف بالمخازن'])

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
                    <h6 class="card-title">أرصدة الأصناف بالمخازن</h6>
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            {!! select(['errors'=>'', 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
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
                                    <th>المخزن</th>
                                    <th>الأصناف</th>
                                    <th>داخل المخزن</th>
                                    <th>محجوز للصرف</th>
                                    <th>محجوز للشحن</th>
                                    <th>متاح</th>
                                    <th>قيد الشحن وارد</th>
                                    <th>قيد الشحن صادر</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">إجمالي</td>
                                    <td id="q_in_stores"></td>
                                    <td id="q_reverseds"></td>
                                    <td id="transfer_q_reserveds"></td>
                                    <td id="store_q_nets"></td>
                                    <td id="transfer_ins"></td>
                                    <td id="transfer_outs"></td>
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
                    "url": '{!! route('reports.yajraproductcount') !!}',
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
                        $('#q_in_stores').html(settings.json.q_in_stores)
                        $('#q_reverseds').html(settings.json.q_reverseds)
                        $('#transfer_q_reserveds').html(settings.json.transfer_q_reserveds)
                        $('#store_q_nets').html(settings.json.store_q_nets)
                        $('#transfer_ins').html(settings.json.transfer_ins)
                        $('#transfer_outs').html(settings.json.transfer_outs)
                    },


                    "columns":[
                        { data: 'Store_Name', name: 'Store_Name'},
                        { data: 'Product_Name', name: 'Product_Name'},
                        { data: 'q_in_store', name: 'q_in_store'},
                        { data: 'q_reversed', name: 'q_reversed'},
                        { data: 'transfer_q_reserved', name: 'transfer_q_reserved'},
                        { data: 'store_q_net', name: 'store_q_net'},
                        { data: 'transfer_in', name: 'transfer_in'},
                        { data: 'transfer_out', name: 'transfer_out'},
                    ],


                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        // { "width": "1%",  "targets": [3] },       //Show on all devices
                        // { "className": 'datetimeformat', "targets": [0] },       //Show on all devices
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

