@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تعديل فاتورة الأرتجاع رقم: {{ $outpermit->outpermit_code }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('outpermits.index') }}" class="btn btn-primary float-right">كل فواتير الإرتجاع</a>
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
                    <h6>تفاصيل فاتورة الشراء: <a href="{{ route('inpermits.show', $inpermit->id) }}" class="btn btn-primary">{{ $inpermit->inpermit_code }}</a></h6>
                <hr>
                    <form method="POST" action="{{ route('outpermits.update', $outpermit->id) }}" class="form-horizontal form-material" id="loginform">
                        @csrf
                        <input type='hidden' name='_method' value='PUT'>

                        <input type='hidden' name='inpermit_id' value='{{ $inpermit->id }}'>
                        <input type='hidden' name='id' value='{{ $outpermit->id }}'>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div class="row">
                            {!! select(['errors'=>$errors, 'edit'=>$inpermit, 'name'=>'supplier_id', 'frkName'=>'Sup_Name', 'rows'=>$suppliers, 'transAttr'=>true, 'label'=>true, 'cols'=>4, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$inpermit, 'name'=>'inpermit_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'cols'=>4, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$inpermit, 'name'=>'inpermit_date', 'type'=>'date', 'transAttr'=>true, 'label'=>true, 'cols'=>4, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$inpermit, 'name'=>'inpermit_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12, 'attr'=>'disabled']) !!}
                        </div>

                        <h6>بيانات فاتورة الإرتجاع</h6>
                        <hr>
                        <div class="row">
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'cols'=>6,]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_date', 'type'=>'date', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6,]) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_detail', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'cols'=>12,]) !!}
                        </div>


                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table id="productsTable" class="table table-hover table-bordered color-bordered-table purple-bordered-table">
                                    <thead>
                                        <th>#</th>
                                        <th>الصنف</th>
                                        <th>رقم التشغيلة</th>
                                        <th>كمية المرتجع</th>
                                        <th>الكمية المتاحة</th>
                                        <th>كمية الباقي</th>
                                        <th>كمية الشراء</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $outproducts = $outpermit->outproducts()->groupBy('id')->get();
                                        ?>
                                        <?php $i=0; ?>
                                        @foreach ($outproducts as $outproduct)
                                            <?php
                                                $Quantity_out =  (old('Quantity_outs'))? old('Quantity_outs')[$i] : $outproduct->Quantity_out;
                                                $product_id = $outproduct->product_id;
                                            ?>
                                            <?php $Buy_Price =  (old('Buy_Prices'))? old('Buy_Prices')[$i] : $outproduct->Buy_Price; ?>
                                            <?php $runID =  (old('runIDs'))? old('runIDs')[$i] : $outproduct->runID; ?>
                                            <?php $expire_date =  (old('expire_dates'))? old('expire_dates')[$i] : $outproduct->expire_date; ?>
                                            <?php $id_runID = $product_id . "_" . $runID; ?>
                                            <tr id="row{{ $id_runID }}">
                                                <td>{{ ++$i }}</td>
                                                <td>
                                                    <input type="hidden" name="inpermit_product_ids[]" value="{{ $outproduct->inpermit_product_id }}">
                                                    <input type="hidden" name="product_ids[]" value="{{ $product_id }}">
                                                    {{ $outproduct->product->Product_Name }}
                                                </td>
                                                <td> <span id="runID">{{ $outproduct->runID }}</span></td>
                                                <td> <input type="number" name="Quantity_outs[]" value="{{ $Quantity_out }}" id="Quantity_out{{ $id_runID }}" id_runID="{{ $id_runID }}" class="form-control Quantity_out" max="{{ $outproduct->Quantity }}" min="0"></td>
                                                <td> <span id="avaliableQuantity{{ $id_runID }}">{{ intVal($outproduct->net_q + $outproduct->Quantity_out) }}</span>
                                                    <span style="display: none" id="avaliableNet_q{{ $id_runID }}">{{ intVal($outproduct->net_q + $Quantity_out) }}</span>
                                                </td>
                                                <td> <span id="restQuantity{{ $id_runID }}">{{ $outproduct->net_q }}</span></td>
                                                <td> <span id="inpermitQuantity{{ $id_runID }}">{{ $outproduct->Quantity }}</span></td>
                                            <tr>
                                        @endforeach

                                    </tbody>
                                </table>
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
    $rowNum = 0;
    $('document').ready(function(){
        // $(document).on("keyup change focusout", ".Quantity_out", function(e){
        $(document).on("change", ".Quantity_out", function(e){
            id_runID = $(this).attr('id_runID')
            avaliableQuantity = parseFloat($('#avaliableQuantity'+ id_runID).text())
            Quantity_out = parseFloat($(this).val());
            if(isNaN(Quantity_out)){
                Quantity_out = 0
            }
            if(Quantity_out > avaliableQuantity){
                alert("الكمية المرتجعة يجب أن لا تكون أكثر من الكمية المتاحة")
                $(this).val('')
                $(this).focus()
            }else{
                rest = avaliableQuantity - Quantity_out
                $('#restQuantity'+ id_runID).text(rest)
            }

            $val = $('.Quantity_out').val();
        });
    })
</script>
@endsection

