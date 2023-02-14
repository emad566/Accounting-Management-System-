@extends('dashboard.master', ['datatable' => 1, 'form' => 1, 'title' => 'فحص اتزان الجرد'])

@section('content')
<?php $start_time = microtime(true); ?>
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">فحص اتزان الجرد</h4>
            </div>
            <div class="col-md-7 align-self-center text-right" dir="rtl">
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
                    <div class="card-body row">

                        <?php $start = microtime(true); ?>
                            @php
                                $fields = [
                                    ['Product_Name', 'transval' => 'Product_Name'], 
                                    ['product_id', 'transval' => 'product_id'], 
                                    ['runID', 'transval' => 'runID'], 
                                    ['q_reversed', 'transval' => 'q_reversed'], 
                                    ['transfer_q_reserved', 'transval' => 'transfer_q_reserved'], 
                                    ['transfer_in', 'transval' => 'transfer_in'], 
                                    ['transfer_out', 'transval' => 'transfer_out'], 
                                    ['q_in_store', 'transval' => 'q_in_store'], 
                                    ['store_q_net', 'transval' => 'store_q_net'], 
                                    ['diff_sum', 'transval' => 'diff_sum'],
                                ];
                            @endphp

                            <div class="table-responsive m-t-40">
                                {!! indexTable([
                                    'objs' => $diff,
                                    'table' => 'stores',
                                    'title' => 'Store_Name',
                                    'trans' => '',
                                    'transval' => ':العنوان',
                                    'active' => false,
                                    'action' => false,
                                    'indexDel' => false,
                                    'isread' => false,
                                    'view' => false,
                                    'vars' => false,
                                    'fields' => $fields,
                                ]) !!}
                            </div>
                        <?php $time_elapsed_secs = microtime(true) - $start; ?>
                        <p style="display: block">Execution Time: <span
                                style="color: blue">{{ number_format((float) $time_elapsed_secs, 2, '.', '') }}</span>
                            Secs</p>
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
        

        $('.datatable').DataTable({
            "responsive": true,
            "searching": true,
            "language": {
                url: 'https://cdn.datatables.net/plug-ins/1.10.22/i18n/Arabic.json'
            },
            "dom": 'Blfrtip',
            "buttons": [{
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

            "displayLength": 50,
            "pageLength": 50,
            "lengthMenu": [
                [50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, -1],
                [50, 100, 200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]
            ]
        });
    </script>
@endsection
