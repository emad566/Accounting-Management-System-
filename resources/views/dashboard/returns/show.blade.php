@extends('dashboard.master', ['form'=>1,'title'=>'مرتجع'])

@section('content')
<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">عرض مرتجع برقم: {{ $return->return_code }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('returns.index') }}" class="btn btn-primary mx-2 float-right">كل سندات المرتجعات</a>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle Emad test -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"></h6>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <a href="{{ route('invoices.show', $return->invoice_id) }}" class="float-right btn btn-danger mx-1">فاتورة المرتجع</a>
                        </div>
                    </div>

                    <div class="row" style="border:solid 3px #ab8ce4">
                        {!! printData(['label'=>'العميل', 'data'=>$return->client_name, 'cols'=>6, 'id'=>'', 'class'=>'']) !!}
                        {!! printData(['label'=>'مندوب المرتجع', 'data'=>$return->fullName, 'cols'=>6, 'id'=>'', 'class'=>'']) !!}
                        {!! printData(['label'=>'التاريخ', 'data'=>$return->return_date, 'cols'=>6, 'id'=>'', 'class'=>'']) !!}
                        {!! printData(['label'=>'تاريخ النظام', 'data'=>$return->created_at, 'cols'=>6, 'id'=>'', 'class'=>'']) !!}
                        <p class="btn btn-primary d-block text-left">{{ $return->return_date }} (مندوب المرتجعات: {{ $return->return->user->name }})</p>
                        <table id="gets" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                            <thead>
                                <th>الصنف</th>
                                <th>{{ trans('validation.attributes.runID') }}</th>
                                <th>كمية المرتجع</th>
                                <th>مرتجع البونس</th>
                            </thead>
                            <tbody>
                            @foreach ($return->return->returnProducts as $returnProduct)
                            <tr>
                                <td>{{ $returnProduct->invoice_product->product->Product_Name }}</td>
                                <td>{{ $returnProduct->invoice_product->runID }}</td>
                                <td>{{ $returnProduct->return_quantity }}</td>
                                <td>{{ $returnProduct->return_bounce }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php execution_time($start_time); ?>
@endsection

@section('script')
@endsection


