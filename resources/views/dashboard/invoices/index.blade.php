@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'فواتير العملاء'])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كل الفواتير</h4>
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
                    <h4 class="text-themecolor card-title">كل الفواتير</h4>

                    <div class="col-md-12 align-self-center text-right" dir="rtl">
                        <div class="d-flex justify-content-end align-items-center">
                            @if(Auth::user()->voucher && Auth::user()->can('Create Invoices') && !Auth::user()->voucher->user_keeper_return_id && !Auth::user()->voucher->user_accountant_return_id && Auth::user()->voucher->voucher_status == 3)
                            <a href="{{ route('invoices.create', Auth::user()->voucher->id) }}" class="btn btn-info float-right">أضف جديد</a>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                    </div>

                    <div class="table-responsive m-t-40">
                        <table class="table table-bordered mobileTable" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>كود  الفاتورة</th>
                                    <th>المندوب</th>
                                    <th>العميل</th>
                                    <th>تحصيل ج م</th>
                                    <th>باقي ج م</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الاجراءات</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th data-th="كود  الفاتورة"></th>
                                    <th data-th="المندوب"></th>
                                    <th data-th="العميل"></th>
                                    <th data-th="تحصيل ج م"></th>
                                    <th data-th="باقي ج م"></th>
                                    <th data-th="الحالة"></th>
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
<?php $next = ($next)? $next : 'next'; ?>

<script>
    $(document).ready(function(){
        $(function() {
            $(function() {
                var table = $('#yajraTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive" : true,
                    "ajax": '{!! route('invoices.yajrainvoices', $next) !!}',

                    "initComplete": function () {
                        i = 0;
                        this.api().columns().every(function () {
                            if(i<7){
                                var column = this;
                                if(i==1){
                                    input = '<select name="fullName" id="fullName">'
                                        + '<option value="">إختر المندوب</option>'
                                            @foreach ($users as $user)
                                                + '<option value="{{ $user->fullName }}">{{ $user->fullName }}</option>'
                                            @endforeach
                                        +'</select>'
                                }else if(i==5){
                                    input = '<select name="status_name" id="status_name">'
                                        + '<option value="">إختر الحالة</option>'
                                            @foreach ($invoice_satatus as $status)
                                                + '<option value="{{ $status->name }}">{{ $status->name }}</option>'
                                            @endforeach
                                        +'</select>'
                                }else{
                                    var input = "<input type='text' placeholder='بحث... ب"+ $(column.header()).text() +"'>";
                                }
                                $(input).appendTo($(column.footer()))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                });
                            }
                            i = i+1

                        });
                    },

                    "columns":[
                        { data: 'invoice_code', name: 'invoice_code'},
                        { data: 'user_rep_name', name: 'user_rep_name'},
                        { data: 'client_name', name: 'client_name'},
                        { data: 'get_paids', name: 'get_paids'},
                        { data: 'get_nexts', name: 'get_nexts'},
                        { data: 'status_name', name: 'status_name'},
                        { data: 'invoice_date', name: 'invoice_date'},
                        { data: 'actions', name: 'actions' },
                    ],

                    'createdRow': function( row, data, dataIndex ) {
                        $( row ).find('td:eq(0)').attr('data-th', 'كود  الفاتورة');
                        $( row ).find('td:eq(1)').attr('data-th', 'المندوب');
                        $( row ).find('td:eq(2)').attr('data-th', 'العميل');
                        $( row ).find('td:eq(3)').attr('data-th', 'تحصيل ج م');
                        $( row ).find('td:eq(4)').attr('data-th', 'باقي ج م');
                        $( row ).find('td:eq(5)').attr('data-th', 'الحالة');
                        $( row ).find('td:eq(6)').attr('data-th', 'التاريخ');
                        $( row ).find('td:eq(7)').attr('data-th', 'الاجراءات');
                    },

                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        { "width": "1%",  "targets": [4] },       //Show on all devices
                        { "className": 'cellcode', "targets": [0] },       //Show on all devices
                        { "className": 'all', "targets": [0,2,5,7] },       //Show on all devices
                    ],



                    "language": {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
                    },
                    "dom": 'Blfrtip',
                    "buttons": ['copy', 'excel','print'],

                    "displayLength": 10,
                    "pageLength": 10,
                    "lengthMenu": [[10, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [10, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]

                });
            });


        });


    });
</script>
@endsection
