@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل فاتورة مشتريات</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('outpermits.create', $inpermit->id) }}"  class="btn btn-light float-right mx-2">إنشاء فاتورة  ماردودات مشتريات</a>
                <a href="{{ route('inpermits.show', $inpermit->id) }}"  class="btn btn-primary float-right mx-2">عرض</a>

                <a href="{{ route('inpermits.index') }}" class="btn btn-primary float-right">كل فواتير المشتريات</a>
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
                    <h6>تعديل الفاتورة : {{ $inpermit->inpermit_details }}</h6>
                    <hr>
                    <form method="POST" action="{{ route('inpermits.update', $inpermit->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>
                        <input type='hidden' name='id' value='{{ $inpermit->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div class="row">
                            {!! select(['errors'=>$errors, 'edit'=>$inpermit, 'name'=>'supplier_id', 'frkName'=>'Sup_Name', 'rows'=>$suppliers, 'transAttr'=>true, 'label'=>false, 'required'=>'required', 'cols'=>4 ]) !!}
                            {!! input(['errors'=>$errors,  'edit'=>$inpermit, 'name'=>'inpermit_code', 'type'=>'number', 'transAttr'=>true, 'label'=>false, 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$inpermit, 'name'=>'inpermit_date', 'type'=>'date', 'transAttr'=>true, 'label'=>false, 'required'=>'required', 'cols'=>4]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$inpermit, 'name'=>'inpermit_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>false, 'cols'=>12]) !!}
                        </div>

                        <div class="row relationElements">
                            {!! select(['errors'=>$errors, 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attrs'=>['Public_Price'] ]) !!}
                            {!! input(['errors'=>$errors, 'type'=>'text', 'name'=>'runID', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'attr'=>'maxlength="50" minlength="1"']) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Public_Price', 'transAttr'=>true, 'class'=>'', 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="10000" step="0.01"']) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Buy_Price', 'transAttr'=>true, 'class'=>'isDisabled', 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="100" step="0.01" ']) !!}
                            {!! input(['errors'=>$errors, 'type'=>'date', 'name'=>'create_date', 'transAttr'=>true, 'class'=>'isDisabled', 'label'=>true, 'attr'=>'', 'cols'=>2]) !!}
                            {!! input(['errors'=>$errors, 'type'=>'date', 'name'=>'expire_date', 'transAttr'=>true, 'class'=>'isDisabled', 'label'=>true, 'attr'=>'', 'cols'=>2]) !!}
                            {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'Quantity', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'min="0" max="9999999"']) !!}

                            <button type="button" id="inpermitAdd" class="btn btn-small btn-succes my-1 pull-left"><i class="fa fa-plus-circle"></i> أضف</button>
                        </div>


                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table id="productsTable" class="table table-hover table-bordered color-bordered-table purple-bordered-table">
                                    <thead>
                                        <th>#</th>
                                        <th>الصنف</th>
                                        <th>{{ trans('validation.attributes.runID') }}</th>
                                        <th>{{ trans('validation.attributes.Public_Price') }}</th>
                                        <th>{{ trans('validation.attributes.Buy_Price') }}</th>
                                        <th>{{ trans('validation.attributes.create_date') }}</th>
                                        <th>{{ trans('validation.attributes.expire_date') }}</th>
                                        <th>الكمية</th>
                                        <th>القيمة</th>
                                        <th>حذف</th>
                                    </thead>
                                    <tbody>
                                        <?php $i=0; ?>
                                        <?php $products =$inpermit->manyProducts; ?>
                                        @if(old('quantities'))
                                            <?php $i=0; ?>
                                            @if (old('product_ids'))
                                                @foreach (old('product_ids') as $product_id)
                                                    <?php $product = App\Models\Product::find($product_id); ?>
                                                    @if($product)
                                                        <?php $id_runID = old('product_ids')[$i] . "_" . old('runIDs')[$i]; ?>
                                                        <tr id="row{{ $id_runID }}">
                                                            <td data-th="#">{{ ++$i }}</td>
                                                            <td data-th="الصنف"> <input type="hidden" id="product_id{{ $id_runID }}" name="product_ids[]" value="{{ old('product_ids')[$i-1] }}">{{ $product->Product_Name }}</td>
                                                            <td data-th="{{ trans('validation.attributes.runID') }}"> <input type="text"  proId="{{ old('product_ids')[$i-1] }}"  name="runIDs[]" id="runID{{ $id_runID }}" value="{{ old('runIDs')[$i-1] }}" class="form-control runIDedit runIDs"></td>
                                                            <td data-th="{{ trans('validation.attributes.Public_Price') }}"> <input type="number" id="Public_Price{{ $id_runID }}" name="Public_Prices[]" value="{{ old('Public_Prices')[$i-1] }}" class="form-control Public_Prices onChange" min="0" max="10000" step="0.01"></td>
                                                            <td data-th="{{ trans('validation.attributes.Buy_Price') }}"> <input type="number" id="Buy_Price{{ $id_runID }}" name="Buy_Prices[]" value="{{ old('Buy_Prices')[$i-1] }}" class="form-control Buy_Prices onChange" min="0" max="100" step="0.01"></td>
                                                            <td data-th="{{ trans('validation.attributes.create_date') }}"> <input type="date" id="create_date{{ $id_runID }}" name="create_dates[]" value="{{ old('create_dates')[$i-1] }}" class="form-control create_dates"></td>
                                                            <td data-th="{{ trans('validation.attributes.expire_date') }}"> <input type="date" id="expire_date{{ $id_runID }}" name="expire_dates[]" value="{{ old('expire_dates')[$i-1] }}" class="form-control expire_dates"></td>
                                                            <td data-th="الكمية"> <input type="number" Public_Price="{{ $product->Public_Price }}" id="quantity{{ $id_runID }}" name="quantities[]" value="{{ old('quantities')[$i-1] }}" class="form-control quantities onChange" tabindex="-1"></td>
                                                            <td data-th="القيمة"> <input type="number" id="price{{ $id_runID }}" name="prices[]" value="{{ old('prices')[$i-1] }}" class="form-control prices" readonly="readonly"></td>
                                                            <td data-th="حذف"> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                                        <tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @else
                                            @foreach ($products as $product)
                                                <?php $quantity =  (old('quantities'))? old('quantities')[$i] : $product->Quantity; ?>
                                                <?php $Public_Price =  (old('Public_Prices'))? old('Public_Prices')[$i] : $product->Public_Price; ?>
                                                <?php $Buy_Price =  (old('Buy_Prices'))? old('Buy_Prices')[$i] : $product->Buy_Price; ?>
                                                <?php $price =  (old('prices'))? old('prices')[$i] : $product->net_total; ?>
                                                <?php $runID =  (old('runIDs'))? old('runIDs')[$i] : $product->runID; ?>
                                                <?php $create_date =  (old('create_dates'))? old('create_dates')[$i] : $product->create_date; ?>
                                                <?php $expire_date =  (old('expire_dates'))? old('expire_dates')[$i] : $product->expire_date; ?>

                                                <?php $id_runID = $product->product_id . "_" . $runID; ?>
                                                <tr id="row{{ $id_runID }}" proId="{{ $product->product_id }}">
                                                    <td>{{ ++$i }}</td>
                                                    <td> <input type="hidden" id="product_id{{ $id_runID }}" name="product_ids[]" value="{{ $product->product_id }}">{{ $product->Product_Name }}</td>
                                                    <td> <input proId="{{ $product->product_id }}" type="text" id="runID{{ $id_runID }}" name="runIDs[]" value="{{ $runID }}" class="form-control runIDedit runIDs" required></td>
                                                    <td> <input type="number" id="Public_Price{{ $id_runID }}" name="Public_Prices[]" value="{{ $Public_Price }}" class="form-control Public_Prices onChange" min="0" max="100" step="0.01" readonly="readonly"></td>
                                                    <td> <input type="number" id="Buy_Price{{ $id_runID }}" name="Buy_Prices[]" value="{{ $Buy_Price }}" class="form-control Buy_Prices onChange" min="0" max="100" step="0.01" readonly="readonly"></td>
                                                    <td> <input type="date" id="create_date{{ $id_runID }}" name="create_dates[]" value="{{ $create_date }}" class="form-control create_dates" readonly="readonly"></td>
                                                    <td> <input type="date" id="expire_date{{ $id_runID }}" name="expire_dates[]" value="{{ $expire_date }}" class="form-control expire_dates" readonly="readonly"></td>
                                                    <td> <input type="number" id="quantity{{ $id_runID }}" name="quantities[]" value="{{ $quantity }}" class="form-control quantities onChange"></td>
                                                    <td> <input type="number" id="price{{ $id_runID }}" name="prices[]" value="{{ $price }}" class="form-control prices" readonly tabindex="-1"></td>
                                                    <td> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a> </td>
                                                <tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <?php $totalPrice = (old('totalPrice'))? old('totalPrice') : $inpermit->manyProducts->sum('net_total'); ?>

                                    <tfoot>
                                        <tr id="totalTr">
                                            <td data-th="الأجمالي" colspan="8">الأجمـــــالـــي</td>
                                            <td data-th="الأجمالي" id="priceTotalTd" class="badge badge-warning"><input id="totalPrice" type="number" name="totalPrice" value="{{ $totalPrice }}" class="form-control totalPrice"  readonly></td>
                                            <td></td>
                                        </tr>
                                    <tfoot>
                                </table>
                                <input type="hidden" value="{{ $i }}" id="lastCount" name="lastCount">
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

    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3>تفاصيل فواتير الإرتجاع</h3>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('outpermits.create', $inpermit->id) }}" class="btn btn-light float-right">إنشاء فاتورة إرتجاع</a>
            </div>
        </div>
    </div>

    @foreach ($inpermit->outpermits as $outpermit)
    <!-- ============================================================== -->
    <!-- Start outpermits Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'readonly']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_date', 'type'=>'date', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'readonly']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_detail', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'readonly']) !!}
                        </div>

                        <div class="col-sm-12 col-md-7 table-responsive">
                            <table id="productsTable" class="table table-hover table-bordered color-bordered-table purple-bordered-table">
                                <thead>
                                    <th>#</th>
                                    <th>الصنف</th>
                                    <th>رقم التشغيلة</th>
                                    <th>كمية المرتجع</th>
                                </thead>
                                <tbody>
                                    <?php
                                        $outproducts =$outpermit->outproducts;
                                    ?>
                                    <?php $i=0; ?>
                                    @foreach ($outproducts as $outproduct)
                                        @if($outproduct->Quantity_out > 0)
                                            <?php
                                                $Quantity_out = $outproduct->Quantity_out;
                                                $product_id = $outproduct->product_id;
                                            ?>
                                            <?php $Buy_Price = $outproduct->Buy_Price; ?>
                                            <?php $runID =  $outproduct->runID; ?>
                                            <?php $create_date =  $outproduct->create_date; ?>
                                            <?php $expire_date =  $outproduct->expire_date; ?>


                                            <tr id="row{{ $i }}">
                                                <td>{{ ++$i }}</td>
                                                <td>
                                                    {{ $outproduct->product->Product_Name }}
                                                </td>
                                                <td> <span id="runID">{{ $outproduct->runID }}</span></td>
                                                <td> <input readonly type="number" value="{{ $Quantity_out }}" id="{{ $product_id }}" pid={{ $product_id }} class="form-control Quantity_out" max="{{ $outproduct->Quantity }}" min="0"></td>
                                            <tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
<script>

    // alert("ok")
    $(document).ready(function(){
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

            if(parseFloat($("#Buy_Price").val()) <0 || parseFloat($("#Buy_Price").val()) > 100){
                e.preventDefault();
                alert(" خصم الشراء غير متوافق.!")
                return ;
            }

            Public_Price = parseFloat($("#Public_Price").val())
            quantity = parseInt($("#Quantity").val())
            discount = parseFloat($("#Buy_Price").val())
            price = (100-discount)/100*Public_Price*quantity
            // alert(price)
            price = parseFloat(price).toFixed(2)
            $newRow = '<tr id="row'+ id_runID +'" proId="'+$("#product_id").val()+'">'
                        + '<td data-th="#">'+ lastCount + '</td>'
                        + '<td data-th="الصنف"> <input type="hidden" id="product_id'+id_runID+'" name="product_ids[]" value="'+ $("#product_id").val() +'">' + $("#product_id option:selected").text() + '</td>'
                        + '<td data-th="{{ trans("validation.attributes.runID") }}"> <input proId="'+$("#product_id").val()+'" type="number" id="runID'+id_runID+'" name="runIDs[]" value="'+ $("#runID").val() +'" class="form-control runIDedit" min="0" max="99999999" required></td>'
                        + '<td data-th="{{ trans("validation.attributes.Pub lic_Price") }}"> <input type="number" id="Public_Price'+id_runID+'" name="Public_Prices[]" value="'+ $("#Public_Price").val() +'" class="form-control onChange" min="0" max="99999999" step="0.01" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.Buy_Price") }}"> <input type="number" id="Buy_Price'+id_runID+'" name="Buy_Prices[]" value="'+ $("#Buy_Price").val() +'" class="form-control onChange" min="0" max="100" step="0.01" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.create_date") }}"> <input type="date" id="create_date'+id_runID+'" name="create_dates[]" value="'+ $("#create_date").val() +'" class="form-control create_dates" readonly></td>'
                        + '<td data-th="{{ trans("validation.attributes.expire_date") }}"> <input type="date" id="expire_date'+id_runID+'" name="expire_dates[]" value="'+ $("#expire_date").val() +'" class="form-control expire_dates" readonly></td>'
                        + '<td data-th="الكمية"> <input type="number" id="quantity'+id_runID+'" name="quantities[]" value="'+ $("#Quantity").val() +'" class="form-control onChange quantities" min="0" max="99999999" readonly></td>'
                        + '<td data-th="القيمة"> <input type="number" id="price'+id_runID+'" name="prices[]" value="'+ price +'" class="form-control prices" readonly></td>'
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

            totalPrice = 0;
            $(".prices").each(function () {
                totalPrice += parseFloat($(this).val())
            })

            $("#totalPrice").val(parseFloat(totalPrice).toFixed(2))

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
                                    $('.isDisabled').removeAttr("readonly")
                                    $('#Public_Price').removeAttr("readonly")
                                    $('.isDisabled').val("")

                                }else{
                                    $('.isDisabled').attr('readonly', 'readonly')
                                    $('#Public_Price').attr('readonly', 'readonly')
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
                product_id = $(this).attr("proId");

                runID =$(this).val();
                id_runID = product_id+ "_" + runID

                if($("#row"+id_runID).length){
                    e.preventDefault();
                    alert(" هذا الصنف موجود بالفعل في اذن الإضافة يمكنك تغيير كميته علي أن تضيفه مرة أخري.!")
                    $(this).val("");
                    $(this).focus();
                    return ;
                }else{
                    $(this).closest("tr").attr("id", "row"+id_runID)
                }

                if(product_id && runID){
                    $.ajax({
                        url: "{{ url("dashboard/inpermit/") }}/" +product_id+"/"+runID,
                        type: 'GET',
                        cache:false,
                        success: function(inpermitPro){
                                if(inpermitPro == false){
                                    // alert("f="+id_runID)
                                    $('#row'+id_runID +' input').removeAttr('readonly')
                                    $('#row'+id_runID +' .prices').attr('readonly', 'readonly')


                                }else{
                                    // alert("o="+id_runID)
                                    $('#row'+id_runID +' input').attr('readonly', 'readonly')
                                    $('#row'+id_runID +' .quantities').removeAttr('readonly')
                                    $('#row'+id_runID +' .runIDedit').removeAttr('readonly')

                                    $('#row'+id_runID +' .Public_Prices').val(inpermitPro.Public_Price)
                                    $('#row'+id_runID +' .Buy_Prices').val(inpermitPro.Buy_Price)
                                    $('#row'+id_runID +' .create_dates').val(inpermitPro.create_date)
                                    $('#row'+id_runID +' .expire_dates').val(inpermitPro.expire_date)

                                    id_runIDs = "#row" + id_runID

                                    Public_Price = parseFloat($(id_runIDs + " .Public_Prices").val());
                                    Buy_Price = parseFloat($(id_runIDs + " .Buy_Prices").val());
                                    quantity = parseInt($(id_runIDs + " .quantities").val());


                                    price = (100-Buy_Price) /100*quantity*Public_Price
                                    $(id_runIDs + " .prices").val(parseFloat(price).toFixed(2))

                                    totalPrice = 0;
                                    $(".prices").each(function () {
                                        totalPrice += parseFloat($(this).val())
                                    })

                                    $("#totalPrice").val(parseFloat(totalPrice).toFixed(2))
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

        $(document).on("change", ".onChange", function(e){
            product_id = $(this).closest("tr").find(".runIDedit").attr("proId");
            runID = $(this).closest("tr").find(".runIDedit").val();
            id_runID = product_id+ "_" + runID
            id_runIDs = "#row" + id_runID

            Public_Price = parseFloat($(id_runIDs + " .Public_Prices").val());
            Buy_Price = parseFloat($(id_runIDs + " .Buy_Prices").val());
            quantity = parseInt($(id_runIDs + " .quantities").val());


            price = (100-Buy_Price) /100*quantity*Public_Price
            $(id_runIDs + " .prices").val(parseFloat(price).toFixed(2))

            totalPrice = 0;
            $(".prices").each(function () {
                totalPrice += parseFloat($(this).val())
            })

            $("#totalPrice").val(parseFloat(totalPrice).toFixed(2))
        })
    })
</script>
@endsection

