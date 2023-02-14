@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل إذن الصرف : {{ $voucher->voucher_code }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('vouchers.index') }}" class="btn btn-primary float-right">كل أذونات الصرف</a>
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
                    <h6 class="card-title">تفاصيل إذن الصرف: <a href="{{ route('vouchers.edit', $voucher->id) }}" class="btn btn-primary">{{ $voucher->voucher_code }}</a></h6>
                <hr>
                    <form method="POST" action="{{ route('vouchers.update', $voucher->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>

                        <input type='hidden' name='voucher_id' value='{{ $voucher->id }}'>
                        <input type='hidden' name='id' value='{{ $voucher->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')
                        <div class="row">
                            {!! select(['errors'=>$errors, 'edit'=>$voucher->store->id, 'name'=>'store_id', 'frkName'=>'Store_Name', 'rows'=>$stores, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6]) !!}
                        </div>

                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$voucher, 'name'=>'voucher_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$voucher, 'name'=>'voucher_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$voucher, 'name'=>'voucher_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>false, 'cols'=>12]) !!}
                        </div>

                        <div class="row relationElements">
                            {!! select(['errors'=>$errors, 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>3]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$voucher, 'type'=>'number', 'name'=>'runID', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'min="0" max="9999999"']) !!}
                            {!! input(['errors'=>$errors,  'type'=>'number', 'name'=>'avliable', 'transval'=>"المتاح", 'label'=>true, 'cols'=>2, 'attr'=>'readonly']) !!}

                            {!! input(['errors'=>$errors, 'edit'=>$voucher, 'type'=>'number', 'name'=>'voucher_quantity_out', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'min="0" max="9999999"']) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'rest', 'transval'=>"الباقي", 'label'=>true, 'cols'=>2, 'attr'=>'readonly']) !!}
                            <button type="button" id="inpermitAdd" class="btn btn-small btn-success my-1"><i class="fa fa-plus-circle"></i> أضف</button>
                        </div>
                        <hr>



                        <hr>

                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                    <thead>
                                        <th>#</th>
                                        <th>الصنف</th>
                                        <th>{{ trans('validation.attributes.runID') }}</th>
                                        <th>الكمية</th>
                                        <th>حذف</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=0;
                                        $products = (old('product_ids'))? old('product_ids') : $products;
                                        ?>
                                        @if ($products)
                                            @foreach ($products as $product)
                                                <?php
                                                $product_id = (old('product_ids')) ? $product : $product->id;
                                                $productCls = App\Models\Product::find($product->id);
                                                $runID = (old('runIDs')) ? old('runIDs')[$i-1] : $product->pivot->runID;
                                                $voucher_quantity = (old('voucher_quantity_outs')) ? old('voucher_quantity_outs')[$i-1] : $product->pivot->voucher_quantity;
                                                ?>
                                                @if($product)
                                                    <?php $id_runID = $product_id . "_" . old('runIDs')[$i]; ?>
                                                    <tr id="row{{ $id_runID }}">
                                                        <td>{{ ++$i }}</td>
                                                        <td> <input type="hidden" name="product_ids[]" value="{{ $product_id }}">{{ $productCls->Product_Name }}</td>
                                                        <td> <input type="text" name="runIDs[]" value="{{ $runID }}" class="form-control" readonly></td>
                                                        <td> <input type="number" name="voucher_quantity_outs[]" value="{{ $voucher_quantity }}" class="form-control" readonly></td>
                                                        <td> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                                    <tr>
                                                @endif
                                            @endforeach

                                        @endif
                                    </tbody>
                                </table>
                                <input type="hidden" value="{{ $i }}" id="lastCount">
                            </div>
                        </div>

                        <div class="row">
                            {!! buttonAction() !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection
@section('script')
<script>
    $('document').ready(function(){
        lastCount = parseInt($("#lastCount").val()) +1;

        $(document).on("click", "#inpermitAdd", function(e){

            if(!$("#runID").val() || !$("#product_id").val() || !$("#voucher_quantity_out").val() || $("#voucher_quantity_out").val() <1){
                e.preventDefault();
                alert("من فضلك اختار صنف وكمية موجبة أقل من 9999999..! وباقي بيانات الصنف بشكل صحيح.")
                return ;
            }

            id_runID = $("#product_id").val()+ "_" + $("#runID").val()

            if($("#row"+ id_runID).length){
                e.preventDefault();
                alert(" هذا الصنف موجود بالفعل في اذن التحويل يمكنك تغيير كميته بدل من إضافته مرة أخري.!")
                return ;
            }

            $newRow = '<tr id="row'+  id_runID +'">'
                        + '<td>'+ lastCount + '</td>'
                        + '<td> <input type="hidden" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td> <input type="text" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td> <input type="number" name="voucher_quantity_outs[]" value="'+ $("#voucher_quantity_out").val() +'" class="form-control" min="0" max="99999999" readonly></td>'
                        + '<td> <a href="#" delId="row'+  id_runID +'" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>'
                     +'<tr>';

            lastCount++;

            $("#voucher_quantity_out").val('')

            $("#product_id").val('')
            $("#runID").val('')
            $("#rest").val('')
            $("#avliable").val('')
            tableBody = $("#productsTable tbody");
            tableBody.append($newRow);

            $("#product_id").focus()

        });

        $(document).on("click", ".prodcutDelete", function(e){
            delId = $(this).attr('delId');
            $("#"+delId).remove()
            e.preventDefault();
        });

        $(document).on("change", "#runID", function(e){

            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
                $(this).val('')
                $("#product_id").focus()

            }else{
                store_id =$("#store_id").val()
                runID=$("#runID").val()
                product_id = $("#product_id").val()

                if(product_id && runID && store_id){
                    $.ajax({
                        url: "{{ url("dashboard/voucher/") }}/" +product_id+"/"+runID+"/"+store_id,

                        type: 'GET',
                        cache:false,
                        success: function(data){
                                if(data == false){
                                    $('#avliable').val(0)
                                    $('#voucher_quantity_out').attr('max', 0)
                                }else{
                                    $('#avliable').val(data)
                                    $('#voucher_quantity_out').attr('max', data)
                                }

                            },
                        error: function(xhr){
                                alert(xhr.status+' '+xhr.statusText);
                            }
                    });
                }
            }
        });

        $(document).on("change", "#voucher_quantity_out", function(e){
            if(!$("#product_id").val()){
                alert("من فضلك اختر منتج!")
            }else{
                rest = $('#avliable').val() - $(this).val();
                if(rest<0 || rest >= $('#avliable').val()){
                    alert (" الكمية يجب ان تكون اقل من المتاح و أكبر من الصفر!")
                    $(this).val('')
                    $('#rest').val('')
                    $(this).focus()
                }else
                $('#rest').val(rest)
            }

        })

        $(document).on("change", "#store_id", function(e){
            store_id_chaneg()
            $("#voucher_quantity_out").val('')
            $("#product_id").val('')
            $("#runID").val('')
            $("#rest").val('')
            $("#avliable").val('')
            $("#productsTable tbody tr").remove()
        })

        function store_id_chaneg(){
            store_id = $("#store_id").val()
            if(store_id){
                $.ajax({
                    url: "{{ url("dashboard/voucher/fromto") }}/" + store_id,
                    // data:{
                    //     _token: '{!! csrf_token() !!}',
                    // },
                    type: 'GET',
                    cache:false,
                    success: function(data){
                            if(data == false){

                            }else{
                                $(".product_id").replaceWith(data)
                            }

                        },
                    error: function(xhr){
                            alert(xhr.status+' '+xhr.statusText);
                        }
                });
            }
        }

        store_id_chaneg()

    })
</script>
@endsection


