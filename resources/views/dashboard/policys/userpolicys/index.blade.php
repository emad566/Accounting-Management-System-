@extends('dashboard.master', ['datatable'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">سياسات المندوبين</h4>
        </div>
        <div class="col-md-7 align-self-center text-right" dir="rtl">
            <div class="d-flex justify-content-end align-items-center">
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
                    <h4 class="card-title">الأعضاء</h4>

                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('manageusers.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $fields = [
                                ['fullName', 'transval'=>'العضو'],
                                ['last_time', 'transAttr'=>true],
                                ['rep_limit', 'transval'=>'الحد الأقصي لصندوق العضو'],
                                // ['is_multi_due_inherit->name', 'transval'=>'أكثر من أجل لنفس الصنف'],
                            ];
                        @endphp

                        <div id="yajraTable" class="table-responsive dataTable m-t-40">
                            {!! indexTable([
                                'objs'=>$users,
                                'table'=>'userpolicys',
                                'title'=>'fullName',
                                'trans'=>'',
                                'transval'=>':العضو',
                                'active'=>false,
                                'indexEdit'=>true,
                                'indexDel'=>false,
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
    $(document).ready(function(){
        // DataTables
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
                        { "className": 'hide', "targets": [0,1] },       //Show on all devices
                        { "className": 'all', "targets": [2,3,4] },       //Show on all devices
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
            // End DataTables
    })
    </script>
@endsection

