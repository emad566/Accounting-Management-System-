@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'تقرير المندوبين'])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تقرير المندوبين</h4>
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
                    <h6 class="card-title">تقرير المندوبين بفترة زمنية</h6>
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            {!! input(['errors'=>$errors, 'name'=>'date_from', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'من', 'label'=>true, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'date_to', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'إلي', 'label'=>true, 'cols'=>3]) !!}
                        </div>
                    </div>
                    <div class="row">
                        {!! buttonAction('', 'بحث <i class="fab fa-searchengin"></i>', 'searchbtn', false) !!}
                    </div>
                </form>
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
                                    <th>المندوب</th>
                                    <th>فواتير سجلت متأخرة</th>
                                    <th>عدد أيام تأخر تنزيل الفواتير</th>
                                    <th>عدد فواتير التنزيلات</th>
                                    <th>عدد التنزيلات</th>
                                    <th>عدد سندات المرتجعات</th>
                                    <th>عدد المرتجعات</th>
                                    <th>عدد سندات التحصيل</th>
                                    <th>التحصيلات</th>
                                    <th>المصروفات</th>
                                    <th>ايداعات</th>
                                    <th>الصندوق</th>
                                    <th>المديونية</th>
                                    <th>عدد الفواتير الغير مسددة</th>
                                    <th>عدد الفواتير المسددة جزئيا</th>
                                    <th>عدد الفواتير كاملة السداد</th>
                                    {{-- <th>مديونية بطيئ الحركة</th> --}}
                                </tr>
                            </thead>

                            <tbody></tbody>
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
                    "url": '{!! route('reports.yajrausersreport') !!}',
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
                        // alert(settings.json.fd)
                        // alert(settings.json.td)
                        // $('#invoicesCount').html(parseInt(settings.json.invoicesCount));
                        // $('#invoicesQuantitySum').html(parseFloat(settings.json.invoicesQuantitySum).toFixed(2));
                        // $('#returnsCount').html(parseInt(settings.json.returnsCount));
                        // $('#returnsQuantitySum').html(parseInt(settings.json.returnsQuantitySum));
                        // $('#getsCount').html(parseInt(settings.json.getsCount));
                        // $('#getsValue').html(parseFloat(settings.json.getsValue).toFixed(2));
                        // $('#spendsValue').html(parseFloat(settings.json.spendsValue).toFixed(2));
                        // $('#transactionsValue').html(parseFloat(settings.json.transactionsValue).toFixed(2));
                        // $('#userBoxSave').html(parseFloat(settings.json.userBoxSave).toFixed(2));
                        // $('#requiredNextValue').html(parseFloat(settings.json.requiredNextValue).toFixed(2));
                        // $('#invoicesNotPaiedCount').html(parseInt(settings.json.invoicesNotPaiedCount));
                        // $('#invoicesPartialPaiedCount').html(parseInt(settings.json.invoicesPartialPaiedCount));
                        // $('#invoicesFullPaiedCount').html(parseInt(settings.json.invoicesFullPaiedCount));
                    },


                    "columns":[
                        { data: 'fullName', name: 'fullName'},
                        { data: 'date_diff', name: 'date_diff'},
                        { data: 'date_diff_sum', name: 'date_diff_sum'},
                        { data: 'invoicesCount', name: 'invoicesCount'},
                        { data: 'invoicesQuantitySum', name: 'invoicesQuantitySum'},
                        { data: 'returnsCount', name: 'returnsCount'},
                        { data: 'returnsQuantitySum', name: 'returnsQuantitySum'},
                        { data: 'getsCount', name: 'getsCount'},
                        { data: 'getsValue', name: 'getsValue'},
                        { data: 'spendsValue', name: 'spendsValue'},
                        { data: 'transactionsValue', name: 'transactionsValue'},
                        { data: 'userBoxSave', name: 'userBoxSave'},
                        { data: 'requiredNextValue', name: 'requiredNextValue'},
                        { data: 'invoicesNotPaiedCount', name: 'invoicesNotPaiedCount'},
                        { data: 'invoicesPartialPaiedCount', name: 'invoicesPartialPaiedCount'},
                        { data: 'invoicesFullPaiedCount', name: 'invoicesFullPaiedCount'},
                    ],


                    "columnDefs": [
                        // { "orderable": false, "twargets": 0},
                        // { "width": "1%",  "targets": [4] },       //Show on all devices
                        // { "className": 'cellcode', "targets": [0,1,2] },       //Show on all devices
                        // { "className": 'not-mobile', "targets": [] },       //Show on all devices
                        // { "className": 'mobile', "targets": [] },       //Show on all devices
                    ],



                    "language": {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
                    },
                    "dom": 'Blfrtip',
                    "buttons": [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        'colvis'
                    ], 

                    "displayLength": 50,
                    "pageLength": 50,
                    "lengthMenu": [[50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [1, 2, 10, 20, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

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



    });
</script>
<style>
    #searchbtn{
        float: left;
    }
</style>
@endsection

