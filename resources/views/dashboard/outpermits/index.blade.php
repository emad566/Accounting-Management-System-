@extends('dashboard.master', ['datatable'=>1, 'form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">فواتير الإرتجاع  الل الموردين</h4>
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
                    <h4 class="text-themecolor">فواتير الإرتجاع  للموردين</h4>

                    <div class="col-md-12 align-self-center text-right" dir="rtl">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('outpermits.find') }}" class="btn btn-light  m-l-15"><i class="fa fa-plus-circle"></i> إنشاء فاتورة إرتجاع</a>
                        </div>
                    </div>
                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('outpermits.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $fields = [
                                // ['id', 'transAttr'=>true],
                                ['inpermit->supplier->Sup_Name', 'transval'=>'المورد'],
                                ['inpermit->inpermit_code', 'transval'=>'كود فاتورة الشراء'],
                                ['outpermit_code', 'transAttr'=>true],
                                // ['outpermit_detail', 'transAttr'=>true],
                                ['outpermit_date', 'transAttr'=>true],
                                // ['created_at->diffForHumans()', 'transval'=>'وقت الإنشاء'],
                            ];
                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$outpermits,
                                'table'=>'outpermits',
                                'title'=>'outpermit_details',
                                'trans'=>'',
                                'transval'=>' :فاتورة  الإرتجاع',
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
    $rowNum = 0;
    $('document').ready(function(){
        $(document).on("click", "#outpermitAdd", function(e){
            if(!$("#Buy_Price").val() || !$("#runID").val() || !$("#expire_date").val() || !$("#product_id").val() || !$("#Quantity").val() || $("#Quantity").val() <1){
                e.preventDefault();
                alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بششكل صحيح.")
                return ;
            }

            $uniqe = $("#product_id").val() + $("#runID").val()

            if($("#row"+$uniqe).length){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في اذن الإضافة يمكنك تغيير كميته علي أن تضيفه مرة أخري.!")
                return ;
            }

            $newRow = '<tr id="row'+ $uniqe +'">'
                        + '<td>'+ ++$rowNum + '</td>'
                        + '<td> <input type="hidden" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td> <input type="number" name="quantities[]" value="'+ $("#Quantity").val() +'" class="form-control" min="0" max="99999999"></td>'
                        + '<td> <input type="number" name="Buy_Prices[]" value="'+ $("#Buy_Price").val() +'" class="form-control" min="0" max="100" step="0.01"></td>'
                        + '<td> <input type="text" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control" min="0" max="99999999"></td>'
                        + '<td> <input type="date" name="expire_dates[]" value="'+ $("#expire_date").val() +'" class="form-control"></td>'
                        + '<td> <a href="#" delId="row'+ $("#product_id").val() +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
                     +'<tr>';

            $("#Quantity").val('')

            $("#product_id").val('')
            $("#runID").val('')
            $("#expire_date").val('')
            $("#Buy_Price").val('')
            tableBody = $("#productsTable tbody");
            tableBody.append($newRow);

        });

        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });

        // Datatable
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
                        { "className": 'cellcode', "targets": [2,4,6] },       //Show on all devices
                        { "className": 'all', "targets": [2,4,6] },       //Show on all devices
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
        // End Datatable
    })
</script>
@endsection

