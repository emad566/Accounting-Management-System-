@extends('dashboard.master', ['datatable'=>1, 'form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">الصلاحيات</h4>
        </div>
        <div class="col-md-7 align-self-center text-right" dir="rtl">
            <div class="d-flex justify-content-end align-items-center">

                <div id="verticalcenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="vcenter">إنشاء صلاحية</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body text-left">
                                @php $cols = 12; @endphp
                                @include('dashboard.users.permissions.createModel')
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">{{ trans('main.Close') }}</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <a href="{{ route('permissions.create') }}" data-toggle="modal" data-target="#verticalcenter" class="btn btn-info  m-l-15"><i class="fa fa-plus-circle"></i> {{ trans('main.Add New') }}</a>
            </div>
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
                    <h4 class="card-title">الصلاحيات</h4>

                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('permissions.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $fields = [
                                ['name', 'transval'=>'كود الصلاحية'],
                                ['name_ar', 'transval'=>'اسم الصلاحية'],
                                // ['created_at->diffForHumans()', 'transval'=>'وقت الإنشاء'],
                                ];
                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$permissions,
                                'table'=>'permissions',
                                'title'=>'name',
                                'trans'=>'',
                                'transval'=>':الصلاحية',
                                'active'=>false,
                                'indexEdit'=>true,
                                'indexDel'=>true,
                                'isread'=>false,
                                'view'=>false,
                                'vars'=>false,
                                'fields'=>$fields,
                            ]) !!}
                        </div>
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
        $(function() {

            // $('.datatable').DataTable();
            $(function() {
                var table = $('.datatable').DataTable({
                    "responsive" : true,
                    "language": {
                        url: '{{ asset('json/Arabic.json') }}',
                    },
                    "dom": 'Blfrtip',
                    "buttons": [
                        'copy', 'excel', 'print',
                    ],

                    "columnDefs": [
                        { "orderable": false, "targets": 0},
                        { "className": 'device', "targets": 0 },       //Show on all devices
                        { "className": 'hide', "targets": 1 },       //Show on all devices
                        { "className": 'all', "targets": 2 },       //Show on all devices
                        { "className": 'all', "targets": 3 },       //Show on all devices
                    ],

                    "displayLength": 50,
                    "lengthMenu": [[2,4,8,10,20, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1], [2,4,8,10,20, 25, 50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]
                });
                // Order by the grouping
                $('.datatable tbody').on('click', 'tr.group', function() {
                    var currentOrder = table.order()[0];
                    if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                        table.order([2, 'desc']).draw();
                    } else {
                        table.order([2, 'asc']).draw();
                    }
                });
            });


        });
        $('.buttons-copy, .buttons-print, .buttons-excel').addClass('btn btn-primary mr-1');

    </script>
@endsection

