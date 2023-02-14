@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'الأشعارات'])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">الأشعارات</h4>
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
                    <h4 class="text-themecolor card-title">كل الأشعارات</h4>

                    <div class="row">
                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                    </div>

                    <div class="table-responsive m-t-40">
                        <table class="table table-bordered mobileTable" id="yajraTable">
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>الأشعار</th>
                                    <th>المنشئ</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
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
                    "responsive": true,

                    "ajax": '{!! route('notifs.yajranotifs') !!}',


                    "columns":[
                        { data: 'DT_RowData.data-img', name: 'img' },
                        { data: 'DT_RowData.data-content', name: 'content'},
                        { data: 'DT_RowData.data-create_user', name: 'create_user'},
                        { data: 'DT_RowData.data-created_at', name: 'created_at'},
                    ],

                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        { "orderable": false, "targets": 0 },
                        { "width": 80, "targets": 0 },
                        { "className": 'all', "targets": 0 },       //Show on all devices
                        { "className": 'all', "targets": 1 },       //Show on all devices
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
