@extends('dashboard.master', ['form'=>1])

@section('content')
<style>
    .card-header{
        transition: all 3s ease-in-out;
    }
    .notAcceptFirst {
        transition: all 3s ease-in-out;
        background: radial-gradient(green, transparent);
    }
    .notAccept{
        transition: all 3s ease-in-out;
        background: radial-gradient(black, transparent);
    }
</style>

<?php $start_time = microtime(true); ?>
<div class="container-fluid">
    
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor"  id="Voucher_{{ $voucher->id }}">  إذن صرف رقم: {{ $voucher->voucher_code }}</h4>
        </div>

        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('vouchers.index') }}" class="btn btn-primary float-right">كل أذونات الصرف</a>
                @if(
                    (
                        ($voucher->voucher_status == 1 || $voucher->voucher_status == 100)
                        && Auth::id() == $voucher->user->id
                    )
                    || (Auth::user()->can(['delete_accepted_vouchers']) && $voucher->invoices->count() <1 )
                )
                <a href="{{ route('vouchers.destroy', $voucher->id) }}" class="btn btn-danger float-right mx-2">حذف</a>
                @endif

                @if (Auth::user()->can(['openVoucher']) && !$voucher->rep->voucher_id)
                <a href="{{ route('vouchers.openVoucher', $voucher->id) }}" class="btn btn-light float-right mx-2">فتح اذن الصرف</a>
                @endif

                
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
                    <h6 class="card-title"></h6>
                    <hr>
                    @include('dashboard.includes.alerts.success')
                    @include('dashboard.includes.alerts.errors')

                    <div class="row">
                        {!! printData(['label'=>'منشئ الأذن', 'data'=>$voucher->user->fullName, 'cols'=>6]) !!}
                        {!! printData(['label'=>'مخزن', 'data'=>$voucher->store->Store_Name, 'cols'=>6]) !!}
                    </div>

                    <div class="row">
                        {!! input(['errors'=>$errors, 'edit'=>$voucher, 'name'=>'voucher_code', 'type'=>'number', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6, 'attr'=>'disabled']) !!}
                        {!! input(['errors'=>$errors, 'edit'=>$voucher, 'name'=>'voucher_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>6, 'attr'=>'disabled']) !!}
                        {!! input(['errors'=>$errors, 'edit'=>$voucher, 'name'=>'voucher_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>false, 'cols'=>12, 'attr'=>'disabled']) !!}
                    </div>

                    <h4>حالة إذن الصرف</h4>
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <ul>
                                <li>{{ $voucher->status->name }}</li>

                                @if($voucher->user_keeper_return_id)
                                <li>تم التسوية مع أمين المخزن</li>
                                @endif

                                @if($voucher->user_accountant_return_id)
                                <li>تم التسوية مع المحاسب</li>
                                @endif
                            </ul>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                                <thead>
                                    <th>#</th>
                                    <th>الصنف</th>
                                    <th>{{ trans('validation.attributes.runID') }}</th>
                                    <th>الكمية</th>
                                    <th>المتاح</th>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                    $i=0;
                                    ?>
                                    @if ($products)
                                        @foreach ($products as $product)
                                            @if($product)
                                                <tr id="row{{ $product->runID }}">
                                                    <td>{{ ++$i }}</td>
                                                    <td> {{ $product->product->Product_Name }}</td>
                                                    <td> <input type="text" name="runIDs[]" value="{{ $product->runID }}" class="form-control" readonly disabled></td>
                                                    <td> <input type="number" name="voucher_quantity_outs[]" value="{{ $product->voucher_quantity }}" class="form-control" readonly disabled></td>
                                                    <td> <input type="number" name="voucher_quantity_outs[]" value="{{ $product->net_q }}" class="form-control" readonly disabled></td>
                                                <tr>
                                            @endif
                                        @endforeach

                                    @endif
                                </tbody>
                            </table>
                        </div>
                        {{-- @if($voucher->view_invoices && $voucher->view_invoices->count()>0)
                        <?php //$VoucherGetnexts = $voucher->view_invoices->sum('get_requireds') - $voucher->view_invoices->sum('client_pay'); ?>
                        {!! printData(['label'=>'إجمالي ما دفع عملاء الأذن: ', 'data'=>$voucher->view_invoices->sum('client_pay'), 'cols'=>12]) !!}
                        {!! printData(['label'=>'إجمالي الأجل لعملاء الأذن: ', 'data'=>$VoucherGetnexts, 'cols'=>12]) !!}
                        @endif --}}
                        <input type="hidden" value="{{ $i }}" id="lastCount">
                    </div>
                </div>
                @if(Auth::user()->can('Create Invoices') && !$voucher->user_keeper_return_id && !$voucher->user_accountant_return_id && $voucher->voucher_status == 3 && $voucher->user_rep_id == Auth::id())
                    <a style="width: 250px; margin:auto;" id="createInv" href="{{ route('invoices.create', $voucher->id) }}" class="btn btn-info btn-sm float-left mx-1 my-1">إنشئ فاتورة عميل</a>
                @endif
                <div id='linkgroup' class="card-footer">
                    @include('dashboard.vouchers.voucherChangeStatus')
                </div>
            </div>

            <div id="voucherReturnsDiv">
                <p class="wait">جاري تحميل المرتجعات</p>
                <img class="loadingImg" src="{{ asset('images/Loading.gif') }}" alt="">
            </div>
            <div id="voucherInvoicesDiv">
                <p class="wait">جاري تحميل الفواتير</p>
                <img class="loadingImg" src="{{ asset('images/Loading.gif') }}" alt="">
            </div>

            {{-- <div class="row">
                <h1 class="display-3 col-12 badge badge-lg badge-light  d-block" style="font-size: 20px;" >فواتير الأذن</h2>
            </div>
            <div id="accordion" class="row vouvherInvlicesLoad">
                @foreach ($invoice_ids as $inv)
                
                    <div id="inv{{ $inv }}" data-url="{{ route('vouchers.invoice' , $inv) }}"><img  class="loadingImg" src="{{ asset('images/Loading.gif') }}" alt=""></div>
                @endforeach
            </div> --}}
        </div>
    </div>
</div>
<p style="text-align: left;">Content Execution Time: {{ microtime(true) - $start_time }} Secs</p>
<?php execution_time($start_time); ?>
@endsection
@section('script')
<script>
    

</script>

<script src="{{ asset('js/resources/dashboard/vouchers/vochers_show.js?v=' .rand(1,100000)) }}"></script>
@endsection


