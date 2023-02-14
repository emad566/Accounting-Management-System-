@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'حركة الأصناف بالمخازن'])

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
                    <h6 class="card-title">حركة الأصناف بالمخازن</h6>
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            {!! select(['errors'=>'', 'name'=>'store_id', 'frkName'=>'Store_Name', 'rows'=>$stores, 'transAttr'=>true, 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
                            {!! select(['errors'=>'', 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
                            {!! input(['errors'=>$errors, 'name'=>'invoice_date_from', 'type'=>'date', 'value'=>$fd, 'transval'=>'من', 'label'=>true, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'invoice_date_to', 'type'=>'date', 'value'=>$ld, 'transval'=>'إلي', 'label'=>true, 'cols'=>3]) !!}
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
                        <div class="row">
                            <div class="col-md-12 col-lg-12 "><p class=""><span id="Label" class="printDataLable">رصيد المحفظة</span>:  <span id="clientOverPrice" class="printDataVal "></span></p></div>
                        </div>
                        <table class="table table-hover table-bordered mobileTable table-striped" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>السند</th>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>من</th>
                                    <th>إلي</th>
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
                    "url": '{!! route('reports.yajrastoreproducttransfer') !!}',
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



                    "columns":[
                        { data: 'created_at', name: 'created_at'},
                        { data: 'transfer_code', name: 'transfer_code'},
                        { data: 'Product_Name', name: 'Product_Name'},
                        { data: 'Quantity', name: 'Quantity'},
                        { data: 'from_store_name', name: 'client_pays'},
                        { data: 'to_store_name', name: 'to_store_name'}
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
    });
</script>
<style>
    #searchbtn{
        float: left;
    }
</style>
@endsection

