@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'المرتجعات'])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كل المرتجعات</h4>
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
                    <h4 class="text-themecolor card-title">كل المرتجعات</h4>


                    <div class="table-responsive m-t-40">
                        <table class="table table-bordered mobileTable" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>كود  المرتجع</th>
                                    <th>المندوب</th>
                                    <th>العميل</th>
                                    <th>كود الفاتورة</th>
                                    <th>تاريخ النظام</th>
                                    <th>التاريخ</th>
                                    <th>الاجراءات</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th data-th="كود  المرتجع"></th>
                                    <th data-th="المندوب"></th>
                                    <th data-th="العميل"></th>
                                    <th data-th="كود الفاتورة"></th>
                                    <th data-th="تاريخ النظام"></th>
                                    <th data-th="التاريخ"></th>
                                    <th data-th="الاجراءات"></th>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>
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
        $(function() {
            $(function() {
                var table = $('#yajraTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive" : true,
                    "ajax": '{!! route('returns.yajrareturns') !!}',

                    "initComplete": function () {
                        i = 0;
                        this.api().columns().every(function () {
                            if(i<4){
                                var column = this;

                                var input = "<input type='text' placeholder='بحث... ب"+ $(column.header()).text() +"'>";

                                $(input).appendTo($(column.footer()))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                });
                            }
                            i++

                        });
                    },

                    "columns":[
                        { data: 'return_code', name: 'return_code'},
                        { data: 'fullName', name: 'fullName'},
                        { data: 'client_name', name: 'client_name'},
                        { data: 'invoice_code', name: 'invoice_code'},
                        { data: 'created_at', name: 'created_at'},
                        { data: 'return_date', name: 'return_date'},
                        { data: 'actions', name: 'actions' },
                    ],

                    'createdRow': function( row, data, dataIndex ) {
                        $( row ).find('td:eq(0)').attr('data-th', 'كود  الفاتورة');
                        $( row ).find('td:eq(1)').attr('data-th', 'المندوب');
                        $( row ).find('td:eq(2)').attr('data-th', 'العميل');
                        $( row ).find('td:eq(3)').attr('data-th', 'كود الفاتورة');
                        $( row ).find('td:eq(4)').attr('data-th', 'تاريخ النظام');
                        $( row ).find('td:eq(5)').attr('data-th', 'التاريخ');
                        $( row ).find('td:eq(6)').attr('data-th', 'الاجراءات');
                    },

                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        // { "width": "1%",  "targets": [] },       //Show on all devices
                        // { "className": 'cellcode', "targets": [0] },       //Show on all devices
                        { "className": 'all', "targets": [0] },       //Show on all devices
                    ],



                    "language": {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
                    },
                    "dom": 'Blfrtip',
                    "buttons": ['copy', 'excel','print'],

                    "displayLength": 50,
                    "pageLength": 50,
                    "lengthMenu": [[100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [10, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

                });
            });


        });


    });
</script>
@endsection
