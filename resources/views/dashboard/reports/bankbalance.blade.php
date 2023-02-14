@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'كشف حساب بنكي'])

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
                    <h6 class="card-title">كشف  حساب بنكي</h6>
                    <form id="searchForm" class="searchForm">
                        <div class="row">
                            {!! select(['errors'=>'', 'name'=>'bank_id', 'frkName'=>'bank_name', 'rows'=>$banks, 'transAttr'=>true, 'label'=>true, 'cols'=>3, 'attr'=>'data-live-search="true"']) !!}
                            {!! input(['errors'=>$errors, 'name'=>'created_at_from', 'type'=>'date', 'value'=>(new Carbon\Carbon($bank_created_date))->isoFormat('YYYY-MM-DD'), 'transval'=>'من', 'label'=>true, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'name'=>'created_at_to', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transval'=>'إلي', 'label'=>true, 'cols'=>3]) !!}
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
                                    <th>التاريخ</th>
                                    <th>نوع العملية</th>
                                    <th>من</th>
                                    <th>إلي</th>
                                    <th>البيان</th>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    <th>الرصيد</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">إجمالي</td>
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
                    "url": '{!! route('reports.yajrabankbalance') !!}',
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
                        { data: 'created_at', name: 'created_at'},
                        { data: 'trans_type', name: 'trans_type'},
                        { data: 'from_bank_name', name: 'from_bank_name'},
                        { data: 'to_bank_name', name: 'to_bank_name'},
                        { data: 'details', name: 'details'},
                        { data: 'depit', name: 'depit'},
                        { data: 'credit', name: 'credit'},
                        { data: 'bank_balance', name: 'bank_balance'}
                    ],


                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        // { "width": "1%",  "targets": [3] },       //Show on all devices
                        { "className": 'datetimeformat', "targets": [0] },       //Show on all devices
                        { "className": 'all', "targets": [0,1,2,3,4,5,6,7] },       //Show on all devices
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

