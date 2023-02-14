@extends('dashboard.master', ['datatable'=>1, 'form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">فواتير الشراء من الموردين</h4>
        </div>
        <div class="col-md-7 align-self-center text-right" dir="rtl">
            <div class="d-flex justify-content-end align-items-center">
                {{-- <a href="{{ route('outpermits.find') }}" class="btn btn-info float-right mx-2">إنشاء فاتورة مردودات مشتريات</a> --}}
                <a href="{{ route('inpermits.create') }}" class="btn btn-info float-right mx-2">إنشاء فاتورة مشتريات</a>

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
                    <h4 class="card-title col-md-12">فواتير الشراء من الموردين</h4>

                    <div class="col-md-12 align-self-center text-right" dir="rtl">
                        <div class="d-flex justify-content-end align-items-center">

                            <div id="verticalcenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="vcenter" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="vcenter">فاتورة شراء جديدة</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body text-left">
                                            @php $cols = 12; @endphp
                                            @include('dashboard.inpermits.createModel')
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">{{ trans('main.Close') }}</button>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>

                            <a href="{{ route('inpermits.create') }}" data-toggle="modal" data-target="#verticalcenter" class="btn btn-info  m-l-15"><i class="fa fa-plus-circle"></i> {{ trans('main.Add New') }}</a>
                        </div>
                    </div>
                    {{-- <h6 class="card-subtitle">{{ trans('main.Export data to Copy, CSV, Excel, PDF & Print') }}</h6> --}}

                    <form id='delete-formMulti' class='delete-formMulti'
                        method='post'
                        action='{{ route('inpermits.delete') }}'>
                        @csrf
                        <input type='hidden' name='_method' value='post'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        @php
                            $fields = [
                                // ['id', 'transAttr'=>true],
                                ['supplier->Sup_Name', 'transval'=>'المورد'],
                                ['inpermit_code', 'transAttr'=>true],
                                // ['inpermit_details', 'transAttr'=>true],
                                ['inpermit_date', 'transAttr'=>true],
                                // ['created_at->diffForHumans()', 'transval'=>'وقت الإنشاء'],
                            ];
                        @endphp

                        <div class="table-responsive m-t-40">
                            {!! indexTable([
                                'objs'=>$inpermits,
                                'table'=>'inpermits',
                                'title'=>'inpermit_details',
                                'trans'=>'',
                                'transval'=>' :فاتورة الشراء',
                                'active'=>false,
                                'indexEdit'=>true,
                                'indexDel'=>true,
                                'isread'=>false,
                                'view'=>true,
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

    $('document').ready(function(){
        lastCount = parseInt($("#lastCount").val()) +1;

        $(document).on("click", "#inpermitAdd", function(e){
            if(!$("#Buy_Price").val() || !$("#runID").val() || !$("#expire_date").val() || !$("#create_date").val() || !$("#product_id").val() || !$("#Quantity").val() || $("#Quantity").val() <1){
                e.preventDefault();
                alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بششكل صحيح.")
                return ;
            }

            id_runID = $("#product_id").val()+ "_" + $("#runID").val()
            if($("#row"+id_runID).length){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في اذن الإضافة يمكنك تغيير كميته علي أن تضيفه مرة أخري.!")
                return ;
            }

            Public_Price = parseFloat($("#Public_Price").val())
            quantity = parseInt($("#Quantity").val())
            discount = parseFloat($("#Buy_Price").val())
            price = (100-discount)/100*Public_Price*quantity
            // alert(price)
            price = parseFloat(price).toFixed(2)
            $newRow = '<tr id="row'+ id_runID +'">'
                        + '<td data-th="#">'+ lastCount + '</td>'
                        + '<td data-th="الصنف"> <input type="hidden" id="product_id'+$("#product_id").val()+'" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td data-th="{{ trans("validation.attributes.runID") }}"> <input proId="'+$("#product_id").val()+'" type="text" id="runID'+$("#product_id").val()+'" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control runIDedit" min="0" max="99999999"  readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.create_date") }}"> <input type="date" id="create_date'+$("#product_id").val()+'" name="create_dates[]" value="'+ $("#create_date").val() +'" class="form-control" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.expire_date") }}"> <input type="date" id="expire_date'+$("#product_id").val()+'" name="expire_dates[]" value="'+ $("#expire_date").val() +'" class="form-control" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.Buy_Price") }}"> <input type="number" id="Public_Price'+$("#product_id").val()+'" name="Public_Prices[]" value="'+ $("#Public_Price").val() +'" class="form-control" min="0" max="99999999" step="0.01" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.Buy_Price") }}"> <input type="number" id="Buy_Price'+$("#product_id").val()+'" name="Buy_Prices[]" value="'+ $("#Buy_Price").val() +'" class="form-control" min="0" max="100" step="0.01" readonly></td>'
                        + '<td data-th="الكمية"> <input type="number" id="quantitie'+$("#product_id").val()+'" name="quantities[]" value="'+ $("#Quantity").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td data-th="القيمة"> <input type="number" id="price'+$("#product_id").val()+'" name="prices[]" value="'+ price +'" class="form-control prices" readonly></td>'
                        + '<td data-th="حذف"> <a href="#" delId="row'+ id_runID +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
                     +'<tr>';

            lastCount++

            $("#Quantity").val('')

            $("#product_id").val('')
            $("#runID").val('')
            $("#create_date").val('')
            $("#expire_date").val('')
            $("#Buy_Price").val('')
            tableBody = $("#productsTable tbody");
            tableBody.append($newRow);

            totalPrice = 0;
            $(".prices").each(function () {
                totalPrice += parseFloat($(this).val())
            })
            $("#totalPrice").val(parseFloat(totalPrice).toFixed(2))

        });

        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });

        $(document).on("change", "#runID, #product_id", function(e){
            if($(this).attr('id')=="product_id"){
                Public_Price = $('option:selected', this).attr('Public_Price');
                $(this).attr('Public_Price', Public_Price)
            }
            Public_Price = $("#product_id").attr('Public_Price')
            $("#Public_Price").val(Public_Price)
            try{
                product_id = $('#product_id').val();
                runID =$('#runID').val();
                if(product_id && runID){
                    $.ajax({
                        url: "{{ url("dashboard/inpermit/") }}/" +product_id+"/"+runID,
                        // data:{
                        //     _token: '{!! csrf_token() !!}',
                        // },
                        type: 'GET',
                        cache:false,
                        success: function(inpermitPro){
                                if(inpermitPro == false){
                                    $('.isDisabled').removeAttr("disabled")
                                    $('#Public_Price').removeAttr("disabled")
                                    $('.isDisabled').val("")

                                }else{
                                    $('.isDisabled').attr('disabled', 'disabled')
                                    $('#Public_Price').attr('disabled', 'disabled')
                                    Public_Price = (inpermitPro.Public_Price)? inpermitPro.Public_Price :  $("#product_id").attr('Public_Price');
                                    $("#Public_Price").val(Public_Price)
                                    $("#Buy_Price").val(inpermitPro.Buy_Price)
                                    $("#create_date").val(inpermitPro.create_date)
                                    $("#expire_date").val(inpermitPro.expire_date)
                                }

                            },
                        error: function(xhr){
                                alert(xhr.status+' '+xhr.statusText);
                            }
                    });
                }

            }catch(e){
                alert(e.message);
            }

        });

        $(document).on("change", ".runIDedit", function(e){
            try{
                Public_Price = $("#Public_Price").val()
                product_id = $(this).attr("proId");
                runID =$(this).val();
                if(product_id && runID){
                    $.ajax({
                        url: "{{ url("dashboard/inpermit/") }}/" +product_id+"/"+runID,
                        type: 'GET',
                        cache:false,
                        success: function(inpermitPro){
                                if(inpermitPro == false){
                                    $('#Public_Price'+product_id).removeAttr("disabled")
                                    $('#Buy_Price'+product_id).removeAttr("disabled")
                                    $('#create_date'+product_id).removeAttr("disabled")
                                    $('#expire_date'+product_id).removeAttr("disabled")

                                }else{
                                    $('#Public_Price'+product_id).attr('disabled', 'disabled')
                                    $('#Buy_Price'+product_id).attr('disabled', 'disabled')
                                    $('#create_date'+product_id).attr('disabled', 'disabled')
                                    $('#expire_date'+product_id).attr('disabled', 'disabled')

                                    Public_Price = (inpermitPro.Public_Price)? inpermitPro.Public_Price :  $("#product_id").attr('Public_Price');
                                    $("#Public_Price").val(Public_Price)
                                    $("#Buy_Price"+product_id).val(inpermitPro.Buy_Price)
                                    $("#create_date"+product_id).val(inpermitPro.create_date)
                                    $("#expire_date"+product_id).val(inpermitPro.expire_date)
                                }

                            },
                        error: function(xhr){
                                alert(xhr.status+' '+xhr.statusText);
                            }
                    });
                }

            }catch(e){
                alert(e.message);
            }

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
                        { "className": 'cellcode', "targets": 3 },       //Show on all devices
                        { "className": 'all', "targets": [2,3,5] },       //Show on all devices
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


