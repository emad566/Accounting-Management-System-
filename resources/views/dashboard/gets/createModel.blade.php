<form method="POST" action="{{ route('gets.store') }}" class="form-horizontal form-material" id="loginform">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <input type="hidden" name="invoice_id", value="{{ $invoice->id }}">
    <?php
        $isReadonly = ($payType == 10 || $payType == 40)? 'readonly="readonly"' : '';

    ?>
    <div class="row">
        {!! printData(['label'=>'العميل ' . $payType , 'data'=>$invoice->client->client_name, 'cols'=>3, 'id'=>'', 'class'=>'', ]) !!}
        {!! select(['errors'=>$errors, 'name'=>'pay_types', 'frkName'=>'name', 'rows'=>$pay_types, 'select_id'=>$payType, 'transval'=>'نوع السداد', 'notrans'=>true, 'required'=>'required', 'label'=>false, 'cols'=>3]) !!}
        <!-- {!! input(['errors'=>$errors, 'name'=>'get_code', 'type'=>'number', 'transval'=>'كود التحصيل', 'label'=>false, 'required'=>'required', 'cols'=>3]) !!} -->
        {!! input(['errors'=>$errors, 'name'=>'get_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>false, 'required'=>'required', 'cols'=>3]) !!}
    </div>
    <div class="row">
        {!! printData(['label'=>'ملاحظات', 'data'=>$invoice->invoice_details, 'cols'=>12, 'id'=>'', 'class'=>'', ]) !!}
    </div>

    <div class="row">
        <div class="col-12 table-responsive">
            <table id="productsTable" class="table table-hover table-bordered color-bordered-table purple-bordered-table">
                <thead>
                    <th>#id</th>
                    <th>الصنف</th>
                    <th>{{ trans('validation.attributes.runID') }}</th>
                    <th>سعر الجمهور</th>
                    <th>كمية الأجل</th>
                    <th>خصم</th>
                    <th>كمية السداد</th>
                    <th>سداد جم</th>
                    <th>أجل</th>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @foreach ($invoice->view_invoice_products as $product)
                        <?php
                        $runid  = old('runIDs')? old('runIDs')[$i] : $product->runID;
                        $id_runID = $product->id . "_" .  $runid;
                        $payQ = ($payType == 10)? $product->get_quantity_next : '';
                        $payPricesQ = ($payType == 10)? $product->get_next : '';
                        $pay_next = ($payType != 10)? $product->get_next : '';
                        ?>
                        <tr id="row{{ $id_runID }}" class="productItem">
                            <td>{{ ++$i }}</td>
                            <td> <input type="hidden" name="invoice_product_ids[]" value="{{ $product->id }}">{{ $product->product->Product_Name }}</td>
                            <td>{{ $product->runID }}</td>
                            <td>{{ $product->invoice_public_price }}</td>
                            <td>{{ $product->get_quantity_next }}</td>
                            <td>{{ $product->discount }}</td>
                            <td> <input
                                public_price="{{ $product->invoice_public_price }}"
                                get_quantity="{{ $product->get_quantity_next }}"
                                discount="{{ $product->discount }}"
                                invoice_product_id="{{ $product->id }}"

                                type="number" name="pay_quantitys[]" value="@if(old('pay_quantitys')){{ old('pay_quantitys')[$i-1] }}@else{{ $payQ }}@endif" class="form-control pay_quantitys pay_quantityItem" {{ $isReadonly }}></td>
                            <td> <input id="paid_price_{{ $product->id }}" type="number" name="paid_prices[]" value="@if($payPricesQ){{ number_format((float)$payPricesQ, 2, '.', '') }}@endif" class="form-control paid_prices" readonly tabindex="-1"></td>
                            <td> <input id="paid_next_{{ $product->id }}" type="number" name="paid_nexts[]" value="@if($pay_next){{ number_format((float)$pay_next, 2, '.', '') }}@endif" class="form-control paid_nexts" readonly tabindex="-1"></td>
                        <tr>
                    @endforeach
                    <?php
                    $totalRequired = $invoice->view_invoice->get_nexts;
                    $totale = $totalRequired;
                    $totalWithBalance = $totalRequired - $invoice->client->view_client->get_overPrice_sum;
                    $totalRequired = ($payType == 10)?  $totale : 0;
                    $totaleNext = ($payType == 10)?  0 : $totale;
                    ?>
                    <tr class="totalTr">
                        <th colspan="7">إجمــــالي</th>
                        <td><span style="color: red;" id="totaleInvoice">{{ $totalRequired }}</span></td>
                        <td><span style="color: red;" id="totalNext">{{ $totaleNext }}</span></td>
                    </tr>
                </tbody>
            </table>
            <hr>


            <p>محفظة العميل: <span id="client_balance">{{ $invoice->client->view_client->get_overPrice_sum }}</span></p>
            <p>المطلوب سدادة: <span style="color: blue;" id="totalPaid">{{ $totalWithBalance }}</span> جم</p>
            {!! input(['errors'=>$errors, 'name'=>'client_pay', 'type'=>'number', 'transval'=>"سداد", 'label'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" step="0.01"']) !!}
            <input type="hidden" value="{{ $i }}" id="lastCount">
            <p>فرق حساب يضاف إلي محفظة العميل: <span id="client_balance_diff">0</span></p>
        </div>
    </div>

    <div class="row">
        {!! buttonAction() !!}
    </div>
</form>
