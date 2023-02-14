@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'التحويلات المخزنية', 'report'=>true])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">التحويل بين المخازن</h4>
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
                    <h4 class="text-themecolor card-title">كل التحويلات</h4>

                    <div class="col-md-12 align-self-center text-right" dir="rtl">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('transfers.create') }}" class="btn btn-info  m-l-15"><i class="fa fa-plus-circle"></i> إنشاء أمر تحويل</a>
                        </div>
                    </div>


                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('transfers.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        <div class="row">
                            @include('dashboard.includes.alerts.success')
                            @include('dashboard.includes.alerts.errors')
                        </div>

                        <div class="table-responsive m-t-40">
                            <table class="table table-bordered mobileTable" id="yajraTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="allItems" value="1" id="allItems" class="allItems"></th>
                                        <th>رقم السند</th>
                                        <th>من</th>
                                        <th>إلي</th>
                                        <th>مسئول الشحن</th>
                                        <th>الحالة</th>
                                        <th>التاريخ</th>
                                        <th>الاجراءات</th>
                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th data-th="رقم السند"></th>
                                        <th data-th="من"></th>
                                        <th data-th="إلي"></th>
                                        <th data-th="مسئول الشحن"></th>
                                        <th data-th="الحالة"></th>
                                        <th data-th="التاريخ"></th>
                                        <th data-th="الاجراءات"></th>
                                    </tr>
                                </tfoot>
                                <tbody></tbody>
                            </table>
                        </div>

                        <a href="" formid="delete-formMulti" class="deleteMe btn btn-outline-danger btn-min-width box-shadow-3 mr-1 mb-1">حذف</a>
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
        $(function() {
            $(function() {
                var table = $('#yajraTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "ajax": '{!! route('transfers.yajratransfers') !!}',

                    "initComplete": function () {
                        i = 0;
                        this.api().columns().every(function () {
                            if(i>0 && i<7){
                                var column = this;
                                if(i==5){
                                    input = '<select name="status_name" id="status_name">'
                                        + '<option value="">إختر الحالة</option>'
                                            @foreach ($transfer_status as $status)
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
                        { data: 'DT_RowData.data-id', name: 'id' },
                        { data: 'transfer_code', name: 'transfer_code'},
                        { data: 'from_store_name', name: 'from_store_name'},
                        { data: 'to_store_name', name: 'to_store_name'},
                        { data: 'transfer_name', name: 'transfer_name'},
                        { data: 'status_name', name: 'status_name'},
                        { data: 'transfer_date', name: 'transfer_date'},
                        { data: 'actions', name: 'actions' },
                    ],

                    'createdRow': function( row, data, dataIndex ) {

                        $( row ).find('td:eq(0)').attr('data-th', '');
                        $( row ).find('td:eq(1)').attr('data-th', 'رقم السند');
                        $( row ).find('td:eq(2)').attr('data-th', 'من');
                        $( row ).find('td:eq(3)').attr('data-th', 'إلي');
                        $( row ).find('td:eq(4)').attr('data-th', 'مسئول الشحن');
                        $( row ).find('td:eq(5)').attr('data-th', 'الحالة');
                        $( row ).find('td:eq(6)').attr('data-th', 'التاريخ');
                        $( row ).find('td:eq(7)').attr('data-th', 'الاجراءات');
                    },

                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        { "width": "1%", "targets": 0 },       //Show on all devices
                        { "className": 'cellcode', "targets": 0 },       //Show on all devices
                        { "className": 'all', "targets": [0,2,3,-1] },       //Show on all devices
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
