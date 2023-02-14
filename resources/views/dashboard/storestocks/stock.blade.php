@extends('dashboard.master', ['datatable' => 1, 'form' => 1, 'title' => 'جرد المخازن'])

@section('content')
<?php $start_time = microtime(true); ?>
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">جرد المخازن</h4>
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
                        <?php $store_id = $store ? $store->id : null; ?>
                        @include('dashboard.storestocks.selectview')

                        <?php $start = microtime(true); ?>
                        @if ($store && $store->products)
                            @php
                                $products = $store->products
                                    ->where('is_store_q', '<>', 0)
                                    ->sortBy(function ($q) {
                                        return $q->product->Product_code;
                                    })
                                    ->all();
                                $fields = [
                                    ['product->Product_code', 'transval' => 'كود المنتج'],
                                    ['Product_Name', 'transAttr' => true],
                                    ['runID', 'transAttr' => true],
                                    ['q_in_store', 'transval' => 'داخل المخزن'],
                                    ['q_reversed', 'transval' => 'محجوز للصرف'],
                                    ['transfer_q_reserved', 'transAttr' => true],
                                    ['store_q_net', 'transval' => 'متاح'],
                                    ['transfer_in', 'transAttr' => true],
                                    ['transfer_out', 'transAttr' => true],
                                    // ['voucher_q_out', 'transAttr'=>true],
                                    ['Public_Price', 'transAttr' => true],
                                    ['expire_date', 'transAttr' => true],
                                ];
                            @endphp

                            <div class="table-responsive m-t-40">
                                {!! indexTable([
                                    'objs' => $products,
                                    'table' => 'stores',
                                    'title' => 'Store_Name',
                                    'trans' => '',
                                    'transval' => ':المخزن',
                                    'active' => false,
                                    'action' => false,
                                    'indexDel' => false,
                                    'isread' => false,
                                    'view' => false,
                                    'vars' => false,
                                    'fields' => $fields,
                                ]) !!}
                            </div>
                        @endif
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
        $(document).ready(function() {
            $(document).on("click", "#runstock", function(e) {
                e.preventDefault();
                store_id = $("select#store_id").val()
                window.location.href = "{{ url('dashboard/storestocks/stock/') }}/" + store_id + "/" + $(
                    "select#view").val()
            })

            @if ($stores && $stores->count() == 1)
                if (!$("#store_id").val()) {
                    window.location.href = "{{ url('dashboard/storestocks/stock/') }}/" +
                        {{ $stores->first()->id }} + "/" + $("select#view").val()
                }
            @endif

        });

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
