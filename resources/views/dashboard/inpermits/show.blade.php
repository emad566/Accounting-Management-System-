@extends('dashboard.master', ['form'=>1])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor" id="Inpermit_{{ $inpermit->id }}">فاتورة شراء من المورد</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('outpermits.create', $inpermit->id) }}"  class="btn btn-light float-right mx-2">إنشاء فاتورة إرتجاع</a>
                <a href="{{ route('inpermits.edit', $inpermit->id) }}"  class="btn btn-warning float-right mx-2">تعديل</a>
                <a href="{{ route('inpermits.destroy', $inpermit->id) }}"  class="btn btn-danger float-right mx-2">حذف</a>
                <a href="{{ route('inpermits.index') }}" class="btn btn-primary float-right">كل فواتير الشراء</a>
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
                    <h6 class="card-title">فاتورة شراء من المورد برقم : {{ $inpermit->inpermit_code }}</h6>
                    <hr>

                        @include('dashboard.includes.alerts.success')
                        @include('dashboard.includes.alerts.errors')

                        <div class="row">
                            {!! printData(['transAttr'=>'supplier_id', 'data'=>$inpermit->supplier->Sup_Name, 'cols'=>4]) !!}
                            {!! printData(['transAttr'=>'inpermit_date', 'data'=>$inpermit->inpermit_date, 'cols'=>4]) !!}
                            {!! printData(['transAttr'=>'inpermit_details', 'data'=>$inpermit->inpermit_details, 'cols'=>4]) !!}
                        </div>

                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                    <thead>
                                        <th>#</th>
                                        <th>الصنف</th>
                                        <th>{{ trans('validation.attributes.runID') }}</th>
                                        <th>كمية الشراء</th>
                                        <th>الباقي</th>
                                        <th>سعر الجمهور</th>
                                        <th>{{ trans('validation.attributes.Buy_Price') }}</th>
                                        <th>القيمة</th>
                                        <th>{{ trans('validation.attributes.create_date') }}</th>
                                        <th>{{ trans('validation.attributes.expire_date') }}</th>
                                    </thead>
                                    <tbody>
                                        <?php $i=0; ?>
                                        @foreach ($inpermit->manyProducts->sortBy(function($q){
                                            return $q->product->Product_code;
                                        })
                                        ->all(); as $product)
                                            <?php $quantity = $product->Quantity; ?>
                                            <?php $Public_Price = $product->Public_Price; ?>
                                            <?php $Buy_Price = $product->Buy_Price; ?>
                                            <?php $net_total = $product->net_total; ?>
                                            <?php $runID = $product->runID; ?>
                                            <?php $create_date = $product->create_date; ?>
                                            <?php $expire_date = $product->expire_date; ?>

                                            <?php $id_runID = $product->id . "_" . $runID; ?>
                                            <tr id="row{{ $id_runID }}">
                                                <td>{{ ++$i }}</td>
                                                <td>{{ $product->product->Product_Name }}</td>
                                                <td>{{ $runID }}</td>
                                                <td>{{ $quantity }}</td>
                                                <td>{{ $product->net_q }}</td>
                                                <td>{{ $Public_Price }}</td>
                                                <td>{{ $Buy_Price }}</td>
                                                <td>{{ $net_total }}</td>
                                                <td>{{ $create_date }}</td>
                                                <td>{{ $expire_date }}</td>
                                            <tr>
                                        @endforeach
                                    </tbody>
                                    <tr class="totalTR">
                                        <td colspan="7">الأجمالي</td>
                                        <td class="badge badge-warning" style="font: 18px bold; ">{{ $inpermit->manyProducts->sum('net_total') }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </table>
                                <input type="hidden" value="{{ $i }}" id="lastCount" name="lastCount">
                            </div>
                        </div>

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
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_date', 'type'=>'date', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'disabled']) !!}
                            {!! input(['errors'=>$errors, 'edit'=>$outpermit, 'name'=>'outpermit_detail', 'transAttr'=>true, 'maxlength'=>191, 'label'=>true, 'required'=>'required', 'cols'=>12, 'attr'=>'disabled']) !!}
                            <div>
                                <a href="{{ route('outpermits.destroy', $outpermit->id) }}"  class="btn btn-danger float-right mx-2">حذف</a>
                            </div>
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
                                            <?php $expire_date =  $outproduct->expire_date; ?>


                                            <tr id="row{{ $i }}">
                                                <td>{{ ++$i }}</td>
                                                <td>
                                                    {{ $outproduct->product->Product_Name }}
                                                </td>
                                                <td> <span id="runID">{{ $outproduct->runID }}</span></td>
                                                <td> <input disabled type="number" value="{{ $Quantity_out }}" id="{{ $product_id }}" pid={{ $product_id }} class="form-control Quantity_out" max="{{ $outproduct->Quantity }}" min="0"></td>
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
@endsection

