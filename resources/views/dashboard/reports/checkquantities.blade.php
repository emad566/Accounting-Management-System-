@extends('dashboard.master', ['datatable'=>1, 'form'=>1, 'title'=>'تقرير أرصدة أصناف الشركة', 'report'=>true])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تقرير أرصدة  أصناف الشركة</h4>
        </div>
        <div class="col-md-7 align-self-center text-right" dir="rtl">
            {!! checkbox(['errors' => $errors, 'name' => 'group', 'id' => 'group', 'transval' => 'بدون رقم تشغيله', 'cols' => 12, 'class' => 'group', 'check' => ($group)? true : false]) !!}
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

                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('states.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            if($group){
                                $fields = [
                                    ['Product_Name', 'transval'=>'الصنف'],
                                    ['in_out_q_net', 'transval'=>'صافي المورد'],
                                    ['voucher_q', 'transval'=>'اذن صرف خارج'],
                                    ['invoice_q', 'transval'=>'بيع'],
                                    ['transfer_out', 'transval'=>'قيد الشحن'],
                                    ['q_in_store', 'transval'=>'داخل المخازن'],
                                    ['in_deff', 'transval'=>'الفرق'],
                                ];
                            }else{
                                $fields = [
                                    ['Product_Name', 'transval'=>'الصنف'],
                                    ['runID', 'transval'=>'التشغيلة'],
                                    ['in_out_q_net', 'transval'=>'صافي المورد'],
                                    ['voucher_q', 'transval'=>'اذن صرف خارج'],
                                    ['invoice_q', 'transval'=>'بيع'],
                                    ['transfer_out', 'transval'=>'قيد الشحن'],
                                    ['q_in_store', 'transval'=>'داخل المخازن'],
                                    ['in_deff', 'transval'=>'الفرق'],
                                ];
                            }
                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$checks,
                                'table'=>'checks',
                                'title'=>'Product_Name',
                                'trans'=>'false',
                                'transval'=>' :الصنف',
                                'active'=>false,
                                'action'=>false,
                                'indexEdit'=>false,
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
        $(document).on('click', '#group', function () { 
            href = '{{ route('reports.checkquantities', $group? 0 : 1) }}';
            window.location.href = href;
        })

        var channel = pusher.subscribe('TransferEventChannel');
            channel.bind('App\\Events\\TransferEvt', function (data) {
        });

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
                        { "className": 'all', "targets": 2 },       //Show on all devices
                        { "className": 'all', "targets": 3 },       //Show on all devices
                    ],

                    "displayLength": 200,
                    "lengthMenu": [[200, 400, 2000, 1000, 2000, 4000, 8000, 16000, -1], [200, 400, 500, 1000, 2000, 4000, 8000, 16000, "All"]]
                });
                // Order by the grouping
                $('.datatable tbody').on('click', 'tr.group', function() {
                    var currentOrder = table.order()[0];
                    if (currentOrder[0] === 1 && currentOrder[1] === 'asc') {
                        table.order([1, 'desc']).draw();
                    } else {
                        table.order([1, 'asc']).draw();
                    }
                });
            });


        });
        $('.buttons-copy, .buttons-print, .buttons-excel').addClass('btn btn-primary mr-1');

        
    </script>
@endsection

