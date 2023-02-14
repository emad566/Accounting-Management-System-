<form method="POST" action="{{ route('invoices.update', $invoice->id) }}" class="" id="loginform"  enctype="">
    @csrf
    @include('dashboard.includes.alerts.success')
    @include('dashboard.includes.alerts.errors')

    <input type='hidden' name='_method' value='PUT'>
    <input type='hidden' name='id' value='{{ $invoice->id }}'>

    <div class="row">
        {!! input(['errors'=>$errors, 'edit'=>$invoice->voucher->store, 'name'=>'Store_Name', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'disabled']) !!}
        {!! select(['errors'=>$errors,'name'=>'pay_types', 'frkName'=>'name', 'rows'=>$pay_types, 'select_id'=>$payType, 'transval'=>'نوع السداد', 'notrans'=>true, 'required'=>'required', 'label'=>true, 'cols'=>2]) !!}
        {!! select(['errors'=>$errors, 'name'=>'client_id', 'frkName'=>'client_name_title', 'rows'=>$clients, 'select_id'=>$invoice->client_id, 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>2, 'attr'=>'data-live-search="true"']) !!}
        {!! input(['errors'=>$errors, 'edit'=>$invoice, 'name'=>'invoice_code', 'type'=>'text', 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
        {!! input(['errors'=>$errors, 'edit'=>$invoice, 'name'=>'invoice_date', 'type'=>'date', 'value'=>Carbon\Carbon::now()->isoFormat('YYYY-MM-DD'), 'transAttr'=>true, 'label'=>true, 'required'=>'required', 'cols'=>3]) !!}
    </div>

    <div class="row">
        {!! input(['errors'=>$errors, 'edit'=>$invoice, 'name'=>'invoice_details', 'transAttr'=>true, 'maxlength'=>191, 'label'=>false, 'cols'=>12]) !!}
        {{-- {!! img(['errors'=>$errors, 'edit'=>$invoice, 'type'=>'text', 'name'=>'image', 'transval'=>'صورة الفاتورة', 'DragFileHer'=>'إضغط هنا اذا كنت تريد تغيير صورة الفاتورة', 'cols'=>12]) !!} --}}
        <p>صورة الفاتورة</p>
        <canvas class="hidden" id="canvas-area" width="300" height="100"></canvas>
        <textarea class="hidden" name="imgData" id="imgData"></textarea>
        <input id="image" type="file" accept="image/*" style="display:block" />
        <div id="previewInvoice" class="invoiceImage">
            <img id="previewinvoiceImg" @if(file_exists($invoice->image_rel_path())) src="{{ url($invoice->image_rel_path()) }}" @endif>
        </div>


    </div>

    <div class="row relationElements">
        {!! select(['errors'=>$errors, 'name'=>'product_id', 'frkName'=>'Product_Name', 'rows'=>$products, 'transAttr'=>true, 'label'=>true, 'cols'=>2]) !!}
        {!! select(['errors'=>$errors, 'name'=>'runID', 'frkName'=>'runID', 'rows'=>[], 'transAttr'=>true, 'label'=>true, 'cols'=>1]) !!}
        {{-- {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'runID', 'transAttr'=>true, 'label'=>true, 'cols'=>2, 'attr'=>'min="0" max="9999999"']) !!} --}}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'avliable', 'transval'=>"المتاح", 'label'=>true, 'cols'=>1, 'attr'=>'readonly']) !!}

        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'invoice_quantity', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="9999999"', 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'bounce', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'pay_quantity', 'transval'=>"كمية السداد", 'label'=>true, 'cols'=>1, 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'discount', 'transAttr'=>true, 'label'=>true, 'cols'=>1, 'attr'=>'min="0" max="100" step="0.01"', 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'pay_price', 'transval'=>"القيمة", 'label'=>true, 'cols'=>1, 'attr'=>'readonly', 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'paid_price', 'transval'=>"سداد", 'label'=>true, 'cols'=>1, 'attr'=>'readonly', 'class'=>'inptutNull']) !!}
        {!! input(['errors'=>$errors, 'type'=>'number', 'name'=>'paid_next', 'transval'=>"أجل", 'label'=>true, 'cols'=>1,  'attr'=>'readonly', 'class'=>'inptutNull']) !!}
        <button type="button" id="inpermitAdd" editAdd="add" class="btn btn-small btn-success mx-1"><i class="fa fa-plus-circle"></i> أضف</button>
        <button type="button" id="cancelEdit" class="btn btn-small btn-danger mx-1" style="display: none"><i class="fa fa-eject"></i>الغاء</button>
    </div>
    <div class="row">
        <p>سعر الجمهور: <span id="Public_Price"></span></p>
    </div>


    <div class="row">
        <div class="col-12 table-responsive">
            <table id="productsTable" class="mobileTable table table-hover table-bordered color-bordered-table purple-bordered-table">
                <thead>
                    <th>#id</th>
                    <th>الصنف</th>
                    <th>{{ trans('validation.attributes.runID') }}</th>
                    <th>الكمية</th>
                    <th>بونص</th>
                    <th>خصم</th>
                    <th>القيمة</th>
                    <th>كمية السداد</th>
                    <th>سداد</th>
                    <th>أجل</th>
                    <th>حذف</th>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @foreach ($invoice->view_invoice_products->sortBy(function($q){
                        return $q->product->Product_code;
                    })
                    ->all() as $invoice_product)
                        <?php $id_runID = $invoice_product->product_id . "_" . $invoice_product->runID;
                            $Public_Price = Public_Price($invoice_product->product_id, $invoice_product->runID);
                        ?>
                        <tr id="row{{ $id_runID }}" class="productItem">
                            <td>{{ ++$i }}</td>
                            <td> <input type="hidden" name="product_ids[]" value="@if(old('product_ids')){{ old('product_ids')[$i-1] }}@else{{ $invoice_product->product_id }}@endif">{{ $invoice_product->product->Product_Name }}</td>
                            <td> <input type="text" name="runIDs[]" value="@if(old('runIDs')){{ old('runIDs')[$i-1] }}@else{{ $invoice_product->runID }}@endif" class="form-control" readonly></td>
                            <td> <input type="number" name="invoice_quantitys[]" value="@if(old('invoice_quantitys')){{ old('invoice_quantitys')[$i-1] }}@else{{ $invoice_product->invoice_quantity }}@endif" class="form-control" readonly></td>
                            <td> <input type="number" name="bounces[]" value="@if(old('bounces')){{ old('bounces')[$i-1] }}@else{{ $invoice_product->invoice_bounce }}@endif" class="form-control" readonly></td>
                            <td> <input type="number" name="discounts[]" value="@if(old('discounts')){{ old('discounts')[$i-1] }}@else{{ $invoice_product->discount }}@endif" class="form-control" readonly></td>
                            <td> <input type="number" name="pay_prices[]" value="@if(old('pay_prices')){{ old('pay_prices')[$i-1] }}@else{{ $invoice_product->get_required }}@endif" class="form-control pay_priceSum" readonly></td>
                            <td> <input type="number" name="pay_quantitys[]" value="@if(old('pay_quantitys')){{ old('pay_quantitys')[$i-1] }}@else{{ $invoice_product->get_quantity }}@endif" class="form-control pay_quantityItem" readonly
                                discount="@if(old('discounts')){{ old('discounts')[$i-1] }}@else{{ $invoice_product->discount }}@endif" invoice_quantity="@if(old('pay_quantitys')){{ old('pay_quantitys')[$i-1] }}@else{{ $invoice_product->get_quantity }}@endif" Public_Price="{{ $Public_Price }}"></td>
                            <td> <input type="number" name="paid_prices[]" value="@if(old('paid_prices')){{ old('paid_prices')[$i-1] }}@else{{ $invoice_product->get_paid }}@endif" class="form-control paid_priceSum" readonly></td>
                            <td> <input type="number" name="paid_nexts[]" value="@if(old('paid_nexts')){{ old('paid_nexts')[$i-1] }}@else{{ $invoice_product->get_next }}@endif" class="form-control paid_nextSum" step="0.01" readonly></td>
                            <td> <a href="#" delId="row{{ $id_runID }}" class="prodcutDelete"><i class="fas fa-trash-alt delEdit"></i></a>
                                <a href="#"

                                product_id="{{ $invoice_product->product_id }}"
                                runID="{{ $invoice_product->runID }}"
                                invoice_quantity="{{ $invoice_product->invoice_quantity }}"
                                bounce="{{ $invoice_product->invoice_bounce }}"
                                discount="{{ $invoice_product->discount }}"
                                pay_price="{{ $invoice_product->get_required }}"
                                pay_quantity="{{ $invoice_product->get_quantity }}"
                                paid_price="{{ $invoice_product->get_paid }}"
                                paid_next="{{ $invoice_product->get_next }}"

                                delId="row{{ $id_runID }}" class="productEdit"><i class="fas fa-edit delEdit"></i></a> </td>
                        <tr>
                    @endforeach
                </tbody>
            </table>
            <hr>

            <p>إجمالي الفاتورة: <span style="color: #000;" id="totalRequired">{{ $invoice->view_invoice->get_requireds }}</span> جم</p>
            <p>محفظة العميل: <span id="client_balance">{{ $invoice->client->view_client->get_overPrice_sum }}</span></p>
            <p>المطلوب سدادة: <span style="color: blue;" id="totalPaid">{{ $invoice->view_invoice->get_paids }}</span> جم</p>
            <p>إجمالي الآجل: <span style="color: red;" id="totalNext">{{ $invoice->view_invoice->get_nexts }}</span> جم</p>
            {!! input(['errors'=>$errors, 'name'=>'client_pay', 'type'=>'number', 'transval'=>"سداد", 'label'=>true, 'required'=>'required', 'cols'=>3, 'attr'=>'min="0" step="0.01"', 'value'=>$invoice->view_invoice->client_pays]) !!}
            <p>فرق حساب يضاف إلي محفظة العميل: <span id="client_balance_diff">{{ $invoice->view_invoice->client_balance_effect }}</span></p>
            <input type="hidden" value="{{ $i }}" id="lastCount">
        </div>
    </div>

    <div class="row">
        {!! buttonAction(false, 'حفظ التعديلات') !!}
    </div>
</form>
