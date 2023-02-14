@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'إذونات الصرف', 'report'=>true])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">كل إذونات الصرف</h4>
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
                    <h4 class="card-title">كل الأذونات</h4>

                    <div class="col-md-12 align-self-center text-right" dir="rtl">
                        <div class="d-flex justify-content-end align-items-center">
                            @if(Auth::user()->can(['Create Vouchers']))
                            @php
                                $btncolor = (Auth::user()->voucher_id)? 'danger' : 'primary';
                            @endphp
                            <a href="{{ route('vouchers.create') }}" class="btn btn-{{ $btncolor }}  m-l-15"><i class="fa fa-plus-circle"></i> إنشاء إذن صرف</a>
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
                                    <th>كود  الأذن</th>
                                    <th>الحالة</th>
                                    <th>من</th>
                                    <th>المندوب</th>
                                    <th>التاريخ</th>
                                    <th>الاجراءات</th>
                                </tr>
                            </thead>
                            @if($vouchers == null)
                            <tbody>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: red;">لا يوجد اذونات صرف</td>
                                </tr>
                            </tbody>
                            @endif
                            <tfoot>
                                <tr>
                                    <th data-th="كود  الأذن"></th>
                                    <th data-th="الحالة"></th>
                                    <th data-th="من"></th>
                                    <th data-th="المندوب"></th>
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
        @if($vouchers !== null)
        $(function() {
            $(function() {
                var table = $('#yajraTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive" : true,
                    "ajax": '{!! route('vouchers.yajravouchers') !!}',

                    "initComplete": function () {
                        i = 0;
                        this.api().columns().every(function () {
                            if(i<5){
                                var column = this;
                                if(i==1){
                                    input = '<select name="status_name" id="status_name">'
                                        + '<option value="">إختر الحالة</option>'
                                            @foreach ($voucher_status as $status)
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
                        { data: 'voucher_code', name: 'voucher_code'},
                        { data: 'status_name', name: 'status_name'},
                        { data: 'store_name', name: 'store_name'},
                        { data: 'user_rep_fullName', name: 'user_rep_fullName'},
                        { data: 'voucher_date', name: 'voucher_date'},
                        { data: 'actions', name: 'actions' },
                    ],

                    'createdRow': function( row, data, dataIndex ) {
                        $( row ).find('td:eq(0)').attr('data-th', 'كود  الأذن');
                        $( row ).find('td:eq(1)').attr('data-th', 'الحالة');
                        $( row ).find('td:eq(2)').attr('data-th', 'من');
                        $( row ).find('td:eq(3)').attr('data-th', 'المندوب');
                        $( row ).find('td:eq(4)').attr('data-th', 'التاريخ');
                        $( row ).find('td:eq(5)').attr('data-th', 'الاجراءات');
                    },

                     "columnDefs": [
                        { "orderable": false, "targets": 0},
                        { "width": "1%",  "targets": [3] },       //Show on all devices
                        { "className": 'all', "targets": 3 },       //Show on all devices
                        { "className": 'all', "targets": 5 },       //Show on all devices
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
        @endif


    });
</script>
@endsection
