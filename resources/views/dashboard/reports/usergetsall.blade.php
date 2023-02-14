@extends('dashboard.master', ['datatable' => 1, 'form' => 1, 'title' => 'تقرير صناديق المندوبين'])

@section('content')
<?php $start_time = microtime(true); ?>
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">صناديق المناديب</h4>
            </div>
            <div class="col-md-7 align-self-center text-right" dir="rtl">
                <div class="d-flex justify-content-end align-items-center">

                    <div id="verticalcenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter"
                        aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

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
                        <h4 class="card-title"></h4>


                        @php
                            $downloadUrls = [];
                            foreach ($users as $user) {
                                $downloadUrls[$user->id] = '<a  href="' . route('reports.usergets', $user->id) . '"><i class="fas fa-eye delEdit"></i> عرض</a>';
                            }
                            
                            $fields = [
                                ['fullName', 'transval' => 'المندوب'],
                                ['usergets->user_gets', 'transval' => 'تحصيلات'],
                                ['usergets->user_transaction_amounts', 'transval' => 'تحويلات مالية'],
                                ['usergets->user_spend_amounts', 'transval' => 'مصروفات'],
                                ['usergets->user_safer_balance', 'transval' => 'رصيد الصندوق'],
                                [$downloadUrls, 'transval' => 'عرض'],
                                // ['created_at->diffForHumans()', 'transval'=>'وقت الإنشاء'],
                            ];
                        @endphp

                        <div id="dataTable"
                            class="table table-hover table-bordered  table-striped  dtr-inline table-responsive m-t-40">
                            {!! indexTable([
                                'objs' => $users,
                                'table' => 'users',
                                'title' => 'لمندوب',
                                'trans' => '',
                                'transval' => ' :لمندوب',
                                'active' => false,
                                'action' => false,
                                'indexEdit' => false,
                                'indexDel' => false,
                                'isread' => false,
                                'view' => false,
                                'vars' => false,
                                'fields' => $fields,
                                [$downloadUrls, false, 'view'],
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
